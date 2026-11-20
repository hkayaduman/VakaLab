'use strict';
const assert=require('node:assert/strict');
const {chromium}=require(process.env.PLAYWRIGHT_PATH||'playwright');
const {spawn,spawnSync}=require('node:child_process');
const fs=require('node:fs');
const path=require('node:path');
const net=require('node:net');
const {randomUUID}=require('node:crypto');

const root=path.resolve(__dirname,'..'),dir=path.join(root,'tests','tmp');
const tag=randomUUID(),php=process.env.PHP_BIN||path.join(root,'.tools/php/php.exe');
const config=path.join(dir,'telemetry-'+tag+'.php'),db=path.join(dir,'telemetry-'+tag+'.sqlite').replaceAll('\\','/');
const password=randomUUID(),route='**/api.php?op=events',abort=request=>request.abort('failed');
const env={...process.env,VAKALAB_CONFIG:config,VAKALAB_SEED_PASSWORD:password};
const pause=ms=>new Promise(resolve=>setTimeout(resolve,ms));
fs.mkdirSync(dir,{recursive:true});
fs.writeFileSync(config,"<?php $c=require __DIR__.'/../../config/example.php'; $c['dsn']='sqlite:"+db+"'; $c['https']=false; return $c;");
for(const operation of ['migrate','seed']){
    const result=spawnSync(php,['bin/console.php',operation],{cwd:root,env,encoding:'utf8',windowsHide:true});
    assert.equal(result.status,0,result.stdout+result.stderr);
}

async function freePort(){
    const socket=net.createServer();
    await new Promise(resolve=>socket.listen(0,'127.0.0.1',resolve));
    const port=socket.address().port;
    await new Promise(resolve=>socket.close(resolve));
    return port;
}
async function login(page,name){
    await page.locator('#login').fill(name);
    await page.locator('#password').fill(password);
    await page.getByRole('button',{name:'Giriş yap'}).click();
    await page.locator('#logout').waitFor();
}
async function logout(page){
    await page.locator('#logout').click();
    await page.locator('#login').waitFor();
}
async function openCase(page,number=1){
    await page.locator('[data-case="test-case-'+number+'"]').click();
    await page.waitForFunction(()=>work&&viewId&&document.querySelector('[data-section]'));
    return page.evaluate(()=>work.application.id);
}
async function go(page,key){
    await page.locator('[data-section="'+key+'"]').click();
    await page.waitForFunction(wanted=>section===wanted,key);
}
async function pendingTransitions(page){
    return page.evaluate(async()=>{
        await drainEvents();
        return (await readTelemetry(me.id)).filter(row=>row.appId===work.application.id&&row.event.type==='section_open').map(row=>row.event);
    });
}

(async()=>{
    const port=await freePort(),url='http://127.0.0.1:'+port+'/';
    let browser,server;
    async function start(){
        server=spawn(php,['-S','127.0.0.1:'+port,'-t','public'],{cwd:root,env,stdio:'ignore',windowsHide:true});
        for(let i=0;i<75;i++){
            try{if((await fetch(url)).ok)return;}catch{}
            await pause(100);
        }
        throw Error('Local PHP did not start');
    }
    async function stop(){
        if(!server||server.exitCode!==null)return;
        const exited=new Promise(resolve=>server.once('exit',resolve));
        server.kill();
        await exited;
    }
    try{
        await start();
        browser=await chromium.launch({headless:true,executablePath:process.env.CHROME_BIN});
        const context=await browser.newContext(),staff=await browser.newPage();
        let page=await context.newPage();
        await staff.goto(url);await login(staff,'TEST-ARASTIRMACI');
        await page.goto(url);await login(page,'TEST-OGR-05');
        const id=await openCase(page);
        const view=()=>staff.evaluate(id=>api('view',undefined,{app_id:id}),id);
        const delivered=async entries=>{
            for(let i=0;i<50;i++){
                const events=(await view()).events;
                if(entries.every(e=>events.some(row=>row.client_key===e.key)))return events;
                await pause(100);
            }
            throw Error('Pending transitions never arrived');
        };
        await page.evaluate(()=>drainEvents());
        await page.route(route,abort);
        for(const section of ['profile','initial','profile'])await go(page,section);
        const first=await pendingTransitions(page);
        assert.deepEqual([...first].sort((a,b)=>a.seq-b.seq).map(event=>event.section),['profile','initial','profile']);
        await logout(page);
        await page.unroute(route,abort);
        await login(page,'TEST-OGR-01');
        await page.evaluate(()=>replayTelemetry());
        assert.ok(!(await view()).events.some(row=>first.some(e=>e.key===row.client_key)));
        await logout(page);
        await login(page,'TEST-OGR-05');
        const afterLogin=await delivered(first);
        for(const event of first){
            const matches=afterLogin.filter(row=>row.client_key===event.key);
            assert.equal(matches.length,1);
            assert.equal(matches[0].session_id,event.session);
            assert.equal(matches[0].tab_id,event.tab);
            assert.equal(matches[0].client_at,event.at);
        }
        console.log('PASS logout under failure preserves all transitions and their original trace, isolated by account');

        await openCase(page);
        await page.evaluate(()=>drainEvents());
        await stop();
        await go(page,'final');
        const second=await pendingTransitions(page);
        assert.equal(second.length,1);
        await page.close();
        await start();
        page=await context.newPage();
        await page.goto(url);
        await page.locator('#logout').waitFor();
        const afterClose=await delivered(second);
        assert.equal(afterClose.filter(row=>row.client_key===second[0].key).length,1);
        console.log('PASS a tab closed while the server is unreachable recovers its queued transition in a new tab');

        const closureId=await openCase(page,2);
        await go(page,'case');
        await page.evaluate(()=>drainEvents());
        await page.route(route,abort);
        const collection=closed=>staff.evaluate(closed=>api('admin',{action:'collection',case_id:'test-case-2',closed}),closed);
        await collection(true);
        await pause(40);
        await go(page,'profile');
        const during=(await pendingTransitions(page)).find(event=>event.section==='profile');
        assert.ok(during?.payload.capture_from&&during.payload.capture_until);
        await pause(40);
        await collection(false);
        await page.unroute(route,abort);
        await page.evaluate(()=>replayTelemetry());
        const policy=await staff.evaluate(id=>api('view',undefined,{app_id:id}),closureId);
        const held=policy.events.find(row=>row.client_key===during.key);
        assert.equal(held?.quality,'collection_cutoff_unverified');
        assert.ok(!policy.navigation.visits.some(visit=>visit.start_event_id===held.id));
        await page.locator('#back').click();
        console.log('PASS a stale tab retains its closed-window observation as raw evidence after reopen');

        await openCase(page);
        await page.evaluate(()=>drainEvents());
        await page.route(route,abort);
        await page.evaluate(()=>{for(let i=0;i<105;i++)event('heartbeat');});
        const backlog=await page.evaluate(async()=>(
            (await readTelemetry(me.id)).filter(row=>row.appId===work.application.id).map(row=>row.event)
        ));
        assert.ok(backlog.length>100);
        await logout(page);
        await login(page,'TEST-OGR-05');
        await openCase(page);
        assert.notEqual(await page.evaluate(()=>me.telemetry_session),backlog[0].session);
        await go(page,'confirm');
        const third=await pendingTransitions(page);
        assert.equal(third.length,1);
        await page.locator('#submit').click();
        await page.locator('#work-confirm-accept').click();
        await page.waitForFunction(()=>work.application.state==='submitted');
        const result=await view();
        assert.equal(result.events.filter(row=>row.client_key===third[0].key).length,1);
        assert.equal(result.navigation.milestones.filter(m=>m.kind==='submit').length,1);
        assert.equal(result.snapshots.filter(snapshot=>snapshot.kind==='submit').length,1);
        assert.equal(result.navigation.visits.filter(visit=>visit.section==='confirm').at(-1).end_reason,'submit_confirmed');
        assert.ok(result.navigation.coverage.expected_missing_events>0);
        await page.unroute(route,abort);
        await page.evaluate(()=>replayTelemetry());
        const complete=await delivered(backlog);
        for(const event of backlog){
            const stored=complete.filter(row=>row.client_key===event.key);
            assert.equal(stored.length,1);
            assert.notEqual(stored[0].quality,'collection_cutoff_unverified');
        }
        assert.equal((await view()).navigation.coverage.expected_missing_events,0);
        console.log('PASS submission succeeds during an event outage and a large backlog from a previous sign-in completes research coverage');
    }finally{
        if(browser)await browser.close();
        await stop();
    }
})().catch(error=>{console.error(error);process.exitCode=1;});
