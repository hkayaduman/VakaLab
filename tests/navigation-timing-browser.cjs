const {chromium}=require(process.env.PLAYWRIGHT_PATH||'playwright');
const {spawn,spawnSync}=require('node:child_process'),fs=require('node:fs'),path=require('node:path'),crypto=require('node:crypto');
const root=path.resolve(__dirname,'..'),tmp=path.join(root,'tests/tmp'),tag=crypto.randomUUID(),config=path.join(tmp,`timing_${tag}.php`),db=path.join(tmp,`timing_${tag}.sqlite`),password=crypto.randomUUID(),php=process.env.PHP_BIN||path.join(root,'.tools/php/php.exe');
fs.mkdirSync(tmp,{recursive:true});fs.writeFileSync(config,`<?php $c=require __DIR__.'/../../config/example.php';$c['dsn']='sqlite:${db.replaceAll('\\','/')}';$c['db_user']='';$c['db_password']='';$c['https']=false;return $c;`);
const env={...process.env,VAKALAB_CONFIG:config,VAKALAB_SEED_PASSWORD:password};for(const command of ['migrate','seed']){const p=spawnSync(php,['bin/console.php',command],{cwd:root,env,encoding:'utf8',windowsHide:true});if(p.status!==0)throw Error(p.stdout+p.stderr);}
const url='http://127.0.0.1:18897/',server=spawn(php,['-S','127.0.0.1:18897','-t','public'],{cwd:root,env,stdio:'ignore',windowsHide:true});let browser,n=0;
const check=(ok,name)=>{if(!ok)throw Error(name);console.log(`PASS LIVE TIMING ${++n} ${name}`);};
(async()=>{try{
 for(let i=0;i<100;i++){try{if((await fetch(url)).ok)break;}catch{}await new Promise(r=>setTimeout(r,100));}
 browser=await chromium.launch({headless:true,executablePath:process.env.CHROME_BIN});const page=await browser.newPage(),errors=[];page.on('pageerror',e=>errors.push(e.message));
 const login=async(p,name)=>{await p.goto(url);await p.locator('#login').fill(name);await p.locator('#password').fill(password);await p.getByRole('button',{name:'Giriş yap'}).click();await p.locator('[data-case],[data-admin-tab]').first().waitFor();};
 await login(page,'TEST-OGR-01');await page.locator('[data-case="test-case-1"]').click();await page.waitForFunction(()=>!!work);
 const id=await page.evaluate(()=>work.application.id);const go=async name=>{await page.locator(`[data-section="${name}"]`).click();await page.waitForFunction(s=>section===s&&!dirty&&!saving,name);};
 await go('ai');check(await page.locator('#send').count()===0,'AI tab stays locked before initial confirmation');
 await go('profile');for(const name of ['knowledge','confidence','difficulty','severity'])await page.locator('#'+name).selectOption('5');
 await go('initial');for(const name of ['problem','decision','reason'])await page.locator('#'+name).fill('SENTETİK zaman denetimi '+name);
 await page.locator('#initial').click();await page.locator('#work-confirm-accept').click();await page.locator('#send').waitFor();await page.waitForFunction(()=>!dirty&&!saving);
 await page.evaluate(()=>drainEvents());
 const activeSectionSequence=await page.evaluate(()=>seq);
 await go('ai');
 check(await page.evaluate(sequence=>seq===sequence,activeSectionSequence),'reselecting the active AI section records no new event');
 await page.evaluate(()=>{window.timingMarks=[];document.addEventListener('click',e=>{const b=e.target.closest('[data-section]');if(b)window.timingMarks.push({section:b.dataset.section,wall:Date.now(),perf:performance.now()});},true);});
 const order=['case','profile','initial','ai','final','reflection','confirm','case'];
 for(let i=0;i<order.length;i++){await go(order[i]);await page.evaluate(ms=>new Promise(r=>setTimeout(r,ms)),250+i*50);}
 const marks=await page.evaluate(()=>window.timingMarks);await page.evaluate(()=>drainEvents());
 const research=await browser.newContext(),rp=await research.newPage();await login(rp,'TEST-ARASTIRMACI');const data=await rp.evaluate(id=>api('view',undefined,{app_id:id}),id);
 const comparisons=[];
 for(let i=0;i<marks.length-1;i++){
   const mark=marks[i],next=marks[i+1];const matches=data.navigation.visits.filter(v=>v.section===mark.section&&Math.abs(Date.parse(v.start_at)-mark.wall)<150&&Math.abs(Date.parse(v.end_at)-next.wall)<150);
   check(matches.length===1,`${mark.section} has one correctly bounded visit`);const visit=matches[0],expected=(next.perf-mark.perf)/1000,error=Math.abs(visit.duration_seconds-expected);
   check(visit.next_section===next.section,`${mark.section} records actual destination ${next.section}`);
   check(error<0.15,`${mark.section} duration agrees with independent monotonic clock within 150ms`);
   check(!visit.locked&&visit.duration_seconds>0,`${mark.section} unlocked time is positive`);
   comparisons.push({section:mark.section,next:next.section,expected_seconds:expected,actual_seconds:visit.duration_seconds,error_ms:error*1000});
 }
 check(comparisons.length===7,'all seven sections independently timed');
 const expectedTotal=(marks.at(-1).perf-marks[0].perf)/1000,actualTotal=comparisons.reduce((sum,v)=>sum+v.actual_seconds,0);
 check(Math.abs(expectedTotal-actualTotal)<0.15,'seven-section total agrees with elapsed monotonic time without double counting');
 await rp.evaluate(async id=>{await processView(id);document.querySelector('[data-process-panel="navigation"]').open=true;},id);
 const ui=await rp.locator('[data-process-panel="navigation"] table').first().locator('tbody tr').evaluateAll(rows=>rows.map(tr=>[...tr.children].map(td=>td.textContent.trim())));
 for(const section of new Set(comparisons.map(c=>c.section))){const visits=data.navigation.visits.filter(v=>v.section===section&&v.duration_seconds!==null),sum=visits.reduce((s,v)=>s+v.duration_seconds,0),label=await rp.evaluate(s=>processSections[s],section),row=ui.find(r=>r[0]===label);check(!!row&&row[3]===(sum>0&&sum<1?'1 sn altında':await rp.evaluate(s=>processDuration(s),sum)),`${section} UI total matches API including repeated visits`);}
 await go('final');
 const originalTab=await page.evaluate(()=>tab),popup=page.waitForEvent('popup');
 await page.evaluate(()=>{window.open(location.href,'_blank');});
 const duplicate=await popup;
 await duplicate.locator('[data-case="test-case-1"]').click();
 await duplicate.locator('[data-section="final"]').click();
 check(await duplicate.locator('#final_decision').isDisabled(),'tab with inherited storage is read only');
 check(await page.locator('#final_decision').isEnabled(),'original tab remains writable');
 const duplicateTab=await duplicate.evaluate(()=>tab);
 check(duplicateTab!==originalTab,'inherited tab gets a separate telemetry identity');
 await duplicate.evaluate(()=>drainEvents());await page.evaluate(()=>drainEvents());
 const raw=await rp.evaluate(id=>api('view',undefined,{app_id:id}),id);
 check(raw.events.some(e=>e.tab_id===originalTab)&&raw.events.some(e=>e.tab_id===duplicateTab),'separate telemetry tab identities persist in the database');
 await duplicate.close();await page.bringToFront();
 const sequenceBeforeReload=await page.evaluate(()=>seq);
 await page.reload();await page.locator('[data-case="test-case-1"]').click();await page.locator('[data-section="final"]').click();
 check(await page.locator('#final_decision').isEnabled()&&await page.evaluate(id=>tab===id,originalTab),'normal reload keeps the telemetry tab identity without a false lock');
 check(await page.evaluate(previous=>seq>previous,sequenceBeforeReload),'normal reload continues the existing event sequence');

 await page.context().addInitScript(()=>{window.inheritedTelemetryTab=sessionStorage.getItem('vakalab:tab');});
 const retainedKey=await page.evaluate(()=>{
   const key=recoveryKey()+':observed-chat';
   sessionStorage.setItem(key,JSON.stringify({attempt_id:'synthetic-observation'}));
   return key;
 });
 const delayedPopup=page.waitForEvent('popup');
 await page.evaluate(()=>{window.open(location.href,'_blank');});
 const delayed=await delayedPopup;
 await delayed.locator('[data-case="test-case-1"]').waitFor();
 const delayedState=await delayed.evaluate(key=>({before:window.inheritedTelemetryTab,id:tab,copiedState:sessionStorage.getItem(key)}),retainedKey);
 check(delayedState.before===originalTab&&delayedState.id!==originalTab,'copy splits the inherited tab identity while still on home');
 check(delayedState.copiedState===null&&await page.evaluate(key=>sessionStorage.getItem(key)!==null,retainedKey),'only the copy discards inherited recovery state');

 await page.bringToFront();await go('case');await go('profile');await go('initial');
 await page.locator('#back').click();await page.locator('[data-case="test-case-1"]').waitFor();
 await delayed.bringToFront();await delayed.locator('[data-case="test-case-1"]').click();
 await delayed.locator('[data-section="final"]').click();await delayed.locator('[data-section="case"]').click();
 await delayed.evaluate(()=>drainEvents());
 check(await delayed.evaluate(expected=>!readOnlyTab&&tab===expected,delayedState.id),'delayed copy keeps its own telemetry identity after the original leaves the case');
 const sequential=await rp.evaluate(id=>api('view',undefined,{app_id:id}),id);
 const affected=sequential.events.filter(e=>[originalTab,delayedState.id].includes(e.tab_id));
 const rawKeys=affected.map(e=>[e.session_id,e.tab_id,e.client_seq].join('/'));
 check(affected.some(e=>e.tab_id===delayedState.id)&&new Set(rawKeys).size===rawKeys.length,'no duplicate raw sequence in the delayed copy scenario');
 await delayed.close();
 check(errors.length===0,'tab timing journey has no JavaScript errors');const summary={checks:n,tolerance_ms:150,expected_total_seconds:expectedTotal,actual_total_seconds:actualTotal,comparisons};fs.writeFileSync(path.join(tmp,'navigation-live-timing-summary.json'),JSON.stringify(summary,null,2));console.log(JSON.stringify(summary));
 console.log(`TOTAL LIVE TIMING ${n} checks passed; all seven sections, independent monotonic measurements.`);
}finally{if(browser)await browser.close();server.kill();}})().catch(e=>{console.error(e);process.exitCode=1;});
