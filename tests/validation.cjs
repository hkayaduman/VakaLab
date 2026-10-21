// Focused regression for missing-field guidance and final-submit readiness.
const {chromium}=require(process.env.PLAYWRIGHT_PATH||'playwright');
const {spawn,spawnSync}=require('node:child_process');
const fs=require('node:fs'),path=require('node:path'),crypto=require('node:crypto');
const root=path.resolve(__dirname,'..'),tmp=path.join(__dirname,'tmp'),tag=crypto.randomUUID();
const config=path.join(tmp,`validation_config_${tag}.php`),db=path.join(tmp,`validation_test_${tag}.sqlite`),password=crypto.randomUUID();
fs.mkdirSync(tmp,{recursive:true});
const configText=`<?php $c=require __DIR__.'/../../config/example.php'; $c['dsn']='sqlite:${db.replaceAll('\\','/')}'; $c['db_user']=''; $c['db_password']=''; $c['https']=false; return $c;`;
fs.writeFileSync(config,configText);
const php=process.env.PHP_BIN||path.join(root,'.tools/php/php.exe'),env={...process.env,VAKALAB_CONFIG:config,VAKALAB_SEED_PASSWORD:password};
for(const command of ['migrate','seed']){const p=spawnSync(php,['bin/console.php',command],{cwd:root,env,encoding:'utf8',windowsHide:true});if(p.status!==0)throw new Error(p.stdout+p.stderr);}
const url='http://127.0.0.1:18764/',log=fs.openSync(path.join(tmp,'validation-server.log'),'w');
const server=spawn(php,['-S','127.0.0.1:18764','-t','public'],{cwd:root,env,stdio:['ignore',log,log],windowsHide:true});
let browser,count=0;
const check=(ok,name)=>{if(!ok)throw new Error(name);console.log(`PASS VALIDATION ${++count} ${name}`);};
(async()=>{try{
    for(let i=0;i<100;i++){try{if((await fetch(url)).ok)break;}catch{}await new Promise(r=>setTimeout(r,100));}
    browser=await chromium.launch({headless:true,executablePath:process.env.CHROME_BIN||undefined});
    const context=await browser.newContext({viewport:{width:1440,height:1000}}),page=await context.newPage(),errors=[];
    page.on('pageerror',e=>errors.push(e.message));
    const inConfirm=async()=>{await page.locator('[data-section="confirm"]').click();try{await page.locator('#submit').waitFor({timeout:5000});}catch(e){console.error('SUBMIT TAB DEBUG',await page.evaluate(()=>({section,dirty,saving,conflict,initial:!!work.application.initial_at,errors:document.querySelector('#notice')?.textContent,trace:window.tabTrace})));await page.screenshot({path:path.join(tmp,'summary-navigation-failure.png')});throw e;}};
    const submitDisabled=async()=>await page.locator('#submit').count()?await page.locator('#submit').isDisabled():await page.evaluate(()=>!canSubmit());
    await page.addInitScript(()=>{window.tabTrace=[];for(const type of ['pointerdown','pointerup','click'])document.addEventListener(type,e=>{const b=e.target.closest('button');if(b?.dataset.section){window.tabTrace.push({type,section:b.dataset.section,x:e.clientX,y:e.clientY,scroll:window.scrollY});window.tabTrace=window.tabTrace.slice(-12);}},true);});await page.goto(url);await page.locator('#login').fill('TEST-OGR-01');await page.locator('#password').fill(password);await page.getByRole('button',{name:'Giriş yap'}).click();
    await page.locator('[data-case="test-case-2"]').click();await page.locator('[data-section="profile"]').click();
    for(const [id,value] of Object.entries({knowledge:5,confidence:8,difficulty:6}))await page.locator('#'+id).selectOption(String(value));
    await page.locator('[data-section="initial"]').click();
    for(const id of ['problem','decision','reason'])await page.locator('#'+id).fill('SENTETİK '+id);
    let dialogs=0;
    await page.locator('#initial').click();await page.locator('#severity[aria-invalid="true"]').waitFor();
    check(dialogs===0&&await page.locator('#work-confirm').count()===0,'missing initial field blocks before confirmation');
    check(await page.locator('#severity').evaluate(e=>document.activeElement===e),'first missing profile field focused automatically');
    check(await page.locator('#severity-required-error').isVisible(),'missing field has inline error');
    check(await page.locator('[data-section="profile"]').innerText().then(t=>t.includes('1 eksik')),'profile tab identifies missing field count');
    check(await page.locator('#validation-summary').count()===0,'no global missing-fields box in profile even after validation');
    check(await page.locator('#submit').count()===0,'no submit control in profile before initial lock');check((await page.locator('[data-section="confirm"]').innerText()).includes('Kilitli'),'last tab is locked before initial confirmation');
    check(await page.locator('#severity').evaluate(e=>e.getBoundingClientRect().top>=document.querySelector('.workspace-top').getBoundingClientRect().bottom),'focused missing field is below sticky desktop navigation');
    await page.screenshot({path:path.join(tmp,'validation-missing-profile.png')});
    await page.locator('[data-section="initial"]').click();await page.locator('#initial').click();
    check(await page.locator('#severity').evaluate(e=>document.activeElement===e),'initial validation still redirects to the missing profile field without summary box');
    await page.locator('#severity').selectOption('0');
    check(await page.locator('#severity').getAttribute('aria-invalid')==='false','zero is valid and clears highlight immediately');
    check(await page.locator('.field-invalid').count()===0,'resolved inline field error disappears');
    await page.locator('[data-section="initial"]').click();await page.locator('#initial').click();
    await page.locator('#work-confirm').waitFor();dialogs++;await page.locator('#work-confirm-cancel').click();check(dialogs===1&&await page.locator('#problem').isEnabled(),'cancel confirmation preserves editable initial response');
    await page.locator('#initial').click();await page.locator('#work-confirm-accept').click();await page.locator('#send').waitFor();
    for(const name of ['case','profile','initial','ai','final','reflection']){await page.locator('[data-section="'+name+'"]').click();check(await page.locator('#submit').count()===0,'no final-submit control under '+name);check(await page.locator('#validation-summary').count()===0,'no global missing-fields box under '+name);}
    check(await page.locator('[data-section]').count()===7,'seven tabs with dedicated final-submit tab');await inConfirm();
    check(await submitDisabled(),'submit stays disabled after initial lock with final fields empty');
    check(await page.locator('#validation-summary [data-missing-field]').count()===5,'only submit tab has a single linked list of five missing requirements');
    // A user changing disabled in DevTools must still be stopped before confirmation.
    await page.locator('#submit').evaluate(e=>e.disabled=false);await page.locator('#submit').click();
    await page.locator('#final_decision[aria-invalid="true"]').waitFor();check(await page.locator('#validation-summary').count()===0,'following validation to final fields does not carry summary along');check(dialogs===1&&await page.locator('#work-confirm').count()===0,'forced early submit is blocked before confirmation');
    check(await page.locator('#final_decision').evaluate(e=>document.activeElement===e),'forced early submit directs to first missing final field');

    await page.locator('#final_decision').fill('SENTETİK karar');await page.locator('#final_reason').fill('SENTETİK gerekçe');await page.locator('#final_confidence').selectOption('0');await page.locator('#final_severity').selectOption('0');
    check(await submitDisabled(),'complete final assessment still requires reflection');
    await inConfirm();check(await page.locator('#validation-summary [data-missing-field]').count()===1,'submit-only list updates to the remaining reflection');await page.locator('#validation-summary [data-missing-field="reflection"]').click();check(await page.locator('#validation-summary').count()===0,'reflection has no global missing-fields box after following link');await page.locator('#reflection').fill('   ');
    await page.waitForFunction(()=>!dirty&&!saving);check(await submitDisabled(),'whitespace-only reflection does not enable submission');
    await page.locator('#reflection').fill('SENTETİK yansıtma');
    check(await submitDisabled(),'unsaved complete draft does not enable submission');
    await page.waitForFunction(()=>canSubmit());check(true,'complete saved draft enables submit without requiring an AI message');
    await page.locator('[data-section="final"]').click();await page.locator('#final_reason').fill('');
    check(await submitDisabled(),'clearing required final field disables submit immediately');
    await page.locator('#final_reason').fill('SENTETİK gerekçe');await page.waitForFunction(()=>canSubmit());
    const rejected=await page.evaluate(async()=>{
        const before=await api('view',undefined,{app_id:work.application.id}),results=[];
        for(const field of ['final_decision','final_reason','final_confidence','final_severity','reflection']){
            const draft=JSON.parse(before.application.draft);draft[field]=['final_confidence','final_severity'].includes(field)?null:'   ';
            try{await api('submit',{app_id:work.application.id,revision:Number(before.application.revision),draft});results.push(200);}catch(e){results.push(e.status);}
        }
        const after=await api('view',undefined,{app_id:work.application.id});
        return {results,state:after.application.state,snapshots:after.snapshots.filter(s=>s.kind==='submit').length};
    });
    check(rejected.results.every(status=>status===422),'server rejects each missing final requirement independently');
    check(rejected.state==='working'&&rejected.snapshots===0,'rejected submits produce no final snapshot or lock');
    fs.writeFileSync(config,configText.replace('return $c;',"$c['ai']['mode']='delay'; return $c;"));
    await page.locator('[data-section="ai"]').click();await page.locator('#question').fill('SENTETİK gecikme kontrolü');await page.locator('#send').click();
    await page.waitForFunction(()=>busy);await inConfirm();check(await submitDisabled(),'starting AI disables ready submit immediately');
    await page.waitForFunction(()=>!busy);await page.waitForFunction(()=>canSubmit());check(true,'submit reopens after AI completion and draft save');
    await context.setOffline(true);await page.waitForFunction(()=>!navigator.onLine);check(await submitDisabled(),'offline state disables ready submit');
    await context.setOffline(false);await page.waitForFunction(()=>canSubmit());check(true,'reconnection restores saved draft readiness');
    await page.setViewportSize({width:390,height:844});await page.locator('[data-section="final"]').click();await page.locator('#final_reason').fill('');await inConfirm();await page.locator('#validation-summary [data-missing-field="final_reason"]').click();
    check(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth+1),'error summary and field links fit mobile viewport');
    check(await page.locator('#final_reason').evaluate(e=>e.getBoundingClientRect().top>=document.querySelector('.workspace-top').getBoundingClientRect().bottom),'focused missing field is below sticky mobile navigation');
    await page.screenshot({path:path.join(tmp,'validation-mobile.png')});
    await page.locator('#final_reason').fill('SENTETİK son gerekçe');await page.waitForFunction(()=>canSubmit());
    await inConfirm();check(await page.locator('#validation-summary').isHidden(),'complete submission has no missing-fields box');const eventAccepted=await page.evaluate(async()=>{await flushEvents();const v=await api('view',undefined,{app_id:work.application.id});return v.application.last_section;});check(eventAccepted==='confirm','server accepts and preserves dedicated submit-section events');
    await page.screenshot({path:path.join(tmp,'submit-tab-mobile.png')});
    await page.locator('#submit').click();await page.locator('#work-confirm-accept').click();await page.getByText('Vaka teslim edildi.',{exact:true}).waitFor();
    check(await page.locator('#submit').isDisabled(),'valid final submission locks submit control');await page.locator('[data-section="final"]').click();check(await page.locator('#final_reason').isDisabled(),'submitted final response remains read only outside submit tab');
    check(errors.length===0,'no JavaScript page errors');
    console.log(`TOTAL VALIDATION ${count} checks passed. Isolated synthetic DB; user working data untouched.`);
}finally{if(browser)await browser.close();server.kill();fs.closeSync(log);}})().catch(e=>{console.error(e);process.exitCode=1;});
