const {chromium}=require(process.env.PLAYWRIGHT_PATH||'playwright');
const {spawn,spawnSync}=require('node:child_process');
const fs=require('node:fs'),path=require('node:path'),crypto=require('node:crypto');
const root=path.resolve(__dirname,'..'),tmp=path.join(__dirname,'tmp'),tag=crypto.randomUUID(),password=crypto.randomUUID();
fs.mkdirSync(tmp,{recursive:true});
const config=path.join(tmp,`presence-${tag}.php`),db=path.join(tmp,`presence-${tag}.sqlite`),php=process.env.PHP_BIN||path.join(root,'.tools/php/php.exe');
fs.writeFileSync(config,`<?php $c=require __DIR__.'/../../config/example.php';$c['dsn']='sqlite:${db.replaceAll('\\','/')}';$c['db_user']='';$c['db_password']='';$c['https']=false;return $c;`);
const env={...process.env,VAKALAB_CONFIG:config,VAKALAB_SEED_PASSWORD:password};
for(const command of ['migrate','seed']){const r=spawnSync(php,['bin/console.php',command],{cwd:root,env,encoding:'utf8',windowsHide:true});if(r.status!==0)throw Error(r.stdout+r.stderr);}
// Delay only runtime chat; fixture seed above remains fast.
fs.writeFileSync(config,fs.readFileSync(config,'utf8').replace('return $c;',"$c['ai']['mode']='delay';return $c;"));
const url='http://127.0.0.1:18765/',log=fs.openSync(path.join(tmp,'presence-server.log'),'w');
const server=spawn(php,['-S','127.0.0.1:18765','-t','public'],{cwd:root,env,stdio:['ignore',log,log],windowsHide:true});
let browser,chromeProcess,count=0;const check=(ok,name)=>{if(!ok)throw Error(name);console.log(`PASS PRESENCE BROWSER ${++count} ${name}`);};
const login=async(page,name)=>{await page.goto(url);await page.locator('#login').fill(name);await page.locator('#password').fill(password);await page.getByRole('button',{name:'Giriş yap'}).click();await page.locator('[data-case], [data-admin-tab]').first().waitFor();};
(async()=>{try{
 for(let i=0;i<100;i++){try{if((await fetch(url)).ok)break;}catch{}await new Promise(r=>setTimeout(r,100));}
 chromeProcess=spawn(process.env.CHROME_BIN||chromium.executablePath(),['--remote-debugging-port=18766','--no-first-run','--no-default-browser-check','--disable-features=CalculateNativeWinOcclusion','--window-position=-32000,-32000',`--user-data-dir=${path.join(tmp,'confirm-chrome-'+tag)}`],{windowsHide:true,stdio:'ignore'});
 for(let i=0;i<100;i++){try{if((await fetch('http://127.0.0.1:18766/json/version')).ok)break;}catch{}await new Promise(r=>setTimeout(r,100));}
 browser=await chromium.connectOverCDP('http://127.0.0.1:18766',{noDefaults:true});
 const context=browser.contexts()[0],page=await context.newPage(),errors=[];page.on('pageerror',e=>errors.push(e.message));await page.bringToFront();
 await login(page,'TEST-OGR-05');await page.locator('[data-case="test-case-1"]').click();await page.locator('[data-section="confirm"]').click();await page.waitForFunction(()=>canSubmit());const finalId=await page.evaluate(()=>work.application.id);
 await page.locator('#submit').click();await page.locator('#work-confirm').waitFor();check(await page.evaluate(()=>document.hasFocus()&&!document.hidden),'in-page confirmation keeps application focus');await page.locator('#work-confirm-cancel').click();check(await page.evaluate(()=>work.application.state!=='submitted'),'cancel leaves final draft editable');
 await page.locator('#submit').click();await page.locator('#work-confirm').waitFor();await page.keyboard.press('Escape');await page.locator('#work-confirm').waitFor({state:'detached'});check(await page.evaluate(()=>work.application.state!=='submitted'),'Escape cancels without submitting');
 await page.locator('#submit').click();await page.locator('#work-confirm').waitFor();await new Promise(r=>setTimeout(r,500));await page.locator('#work-confirm-accept').click();await page.getByText('Vaka teslim edildi.',{exact:true}).waitFor();
 await page.locator('#logout').click();await page.locator('#login').waitFor();await login(page,'TEST-OGR-01');await page.locator('[data-case="test-case-1"]').click();await page.waitForFunction(()=>!!work?.application?.id);const initialId=await page.evaluate(()=>work.application.id);await page.evaluate(()=>api('events',{app_id:work.application.id,events:[{key:crypto.randomUUID(),tab:'stale-fixture-tab',seq:1,at:'2026-10-20T09:00:00Z',type:'section_open',section:'case',payload:{view_id:'stale-unclosed-fixture',app_active:true,visible:true,focused:true}}]}));
 await page.locator('[data-section="profile"]').click();for(const id of ['knowledge','confidence','difficulty','severity'])await page.locator('#'+id).selectOption('5');await page.locator('[data-section="initial"]').click();for(const id of ['problem','decision','reason'])await page.locator('#'+id).fill('SENTETİK onay testi');
 await page.locator('#initial').click();await page.locator('#work-confirm').waitFor();await page.locator('#work-confirm-cancel').click();check(await page.locator('#problem').isEnabled(),'cancel initial confirmation preserves responses');
 await page.locator('#initial').click();await page.locator('#work-confirm').waitFor();const other=await context.newPage();await other.goto('about:blank');await other.bringToFront();await page.waitForFunction(()=>document.hidden||!document.hasFocus(),null,{polling:100});await new Promise(r=>setTimeout(r,650));await page.bringToFront();await page.waitForFunction(()=>!document.hidden&&document.hasFocus(),null,{polling:100});
 check(await page.locator('#work-confirm').isVisible(),'real departure while confirmation is open does not dismiss it');await page.locator('#work-confirm-accept').click();await page.locator('#send').waitFor();check(await page.evaluate(()=>!!work.application.initial_at),'initial confirmation still unlocks AI');
 await page.locator('#back').click();await page.getByRole('heading',{name:'Vakalarım'}).waitFor();await other.bringToFront();await new Promise(r=>setTimeout(r,650));await page.bringToFront();await page.locator('[data-case="test-case-1"]').click();await new Promise(r=>setTimeout(r,200));await page.locator('#back').click();
 const rc=await browser.newContext(),rp=await rc.newPage();await login(rp,'TEST-ARASTIRMACI');
 const finalView=await rp.evaluate(id=>api('view',undefined,{app_id:id}),finalId),initialView=await rp.evaluate(id=>api('view',undefined,{app_id:id}),initialId);
 check(finalView.navigation.visits.filter(v=>v.section==='outside').length===0,'cancel, Escape and acceptance create no fake final-confirm departure');
 check(finalView.events.filter(e=>e.type==='workflow_confirmed'&&JSON.parse(e.payload).kind==='submit').length===1,'final success marker remains exactly once');
 check(initialView.navigation.visits.filter(v=>v.section==='outside').length===1,'actual departure during initial dialog remains exactly one outside interval');
 check(initialView.navigation.visits.find(v=>v.section==='outside').duration_seconds>=0.5,'real dialog-time departure retains its duration');
 const gaps=initialView.navigation.visits.filter(v=>v.section==='away');check(gaps.length===1&&gaps[0].duration_seconds>=0.6,'home to reopening is measured as case-away time');check(initialView.navigation.visits.some(v=>v.start_at==='2026-10-20T09:00:00Z'&&v.end_at===null),'stale unclosed trace stays unknown while new case gap is retained');
 check(initialView.navigation.visits.filter(v=>!['outside','away'].includes(v.section)&&v.end_at).every(v=>Date.parse(v.end_at)<=Date.parse(gaps[0].start_at)||Date.parse(v.start_at)>=Date.parse(gaps[0].end_at)),'case-away time is never charged to application sections');
 const csv=await rc.request.get(url+`api.php?op=download&format=disco&app_id=${initialId}&include_test=1`);const text=await csv.text();check(csv.status()===200&&text.includes('CASE_AWAY')&&text.includes('APP_OUTSIDE')&&text.includes('INITIAL_CONFIRMED'),'actual CSV distinguishes case absence, app absence and initial success');
 await rp.evaluate(async id=>{await processView(id);document.querySelector('[data-process-panel="navigation"]').open=true;},initialId);await rp.locator('[data-process-panel="navigation"]').screenshot({path:path.join(tmp,'confirmation-gap-fixed.png')});check((await rp.locator('[data-process-panel="navigation"]').innerText()).includes('Vaka dışında'),'case-away interval shown in researcher screen');
 check(errors.length===0,'no student JavaScript errors');console.log(`TOTAL CONFIRMATION ${count} checks passed; isolated synthetic data.`);
}finally{if(browser){try{const session=await browser.newBrowserCDPSession();await session.send('Browser.close');}catch{}await browser.close();}if(chromeProcess)chromeProcess.kill();server.kill();fs.closeSync(log);}})().catch(e=>{console.error(e);process.exitCode=1;});
