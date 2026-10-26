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
const login=async(page,name)=>{await page.goto(url);await page.locator('#login').fill(name);await page.locator('#password').fill(password);const response=page.waitForResponse(r=>r.url().includes('op=login')&&r.request().method()==='POST');await page.getByRole('button',{name:'Giriş yap'}).click();const result=await response;if(!result.ok()){const posted=result.request().postDataJSON();throw Error('Isolated login failed: HTTP '+result.status()+', login matches fixture='+String(posted.login===name)+', password matches fixture='+String(posted.password===password));}await page.locator('[data-case], [data-admin-tab]').first().waitFor();};
(async()=>{try{
 for(let i=0;i<100;i++){try{if((await fetch(url)).ok)break;}catch{}await new Promise(r=>setTimeout(r,100));}
 chromeProcess=spawn(process.env.CHROME_BIN||chromium.executablePath(),['--remote-debugging-port=18766','--no-first-run','--no-default-browser-check','--disable-features=CalculateNativeWinOcclusion','--window-position=-32000,-32000',`--user-data-dir=${path.join(tmp,'presence-chrome-'+tag)}`],{windowsHide:true,stdio:'ignore'});
 for(let i=0;i<100;i++){try{if((await fetch('http://127.0.0.1:18766/json/version')).ok)break;}catch{}await new Promise(r=>setTimeout(r,100));}
 browser=await chromium.connectOverCDP('http://127.0.0.1:18766',{noDefaults:true});
 const context=browser.contexts()[0],page=await context.newPage(),errors=[];page.on('pageerror',e=>errors.push(e.message));
 // Independently observe actual DOM focus changes, including brief Windows
 // focus changes during restore; no measured interval should be discarded.
 await page.addInitScript(()=>{
   window.presenceObserved=[];let last='';
   const observe=()=>{try{if(!work?.application)return;const active=!document.hidden&&document.hasFocus(),key=work.application.id+'/'+active;if(key===last)return;last=key;window.presenceObserved.push({app_id:work.application.id,active,at:Date.now()});}catch{}};
   window.addEventListener('focus',observe);window.addEventListener('blur',observe);document.addEventListener('visibilitychange',observe);
 });
 await page.bringToFront();await login(page,'TEST-OGR-01');await page.locator('[data-case="test-case-1"]').click();
 await page.locator('[data-section="ai"]').click();
 const id=await page.evaluate(()=>work.application.id);
 await page.evaluate(()=>drainEvents());
 await page.screenshot({path:path.join(tmp,'presence-in-app-capture.png')});
 await new Promise(r=>setTimeout(r,450));await page.evaluate(()=>drainEvents());
 const captureRead=spawnSync(php,['-r',"require 'app/bootstrap.php';$db=new Db(cfg());$db->pdo->exec('PRAGMA query_only=ON');echo js(Navigation::build($db->all('SELECT * FROM events WHERE app_id=?',[$argv[1]])));",id],{cwd:root,env,encoding:'utf8',windowsHide:true});
 if(captureRead.status!==0)throw Error(captureRead.stderr);
 const captureView=JSON.parse(captureRead.stdout);
 check(captureView.visits.every(v=>v.section!=='outside'),'browser screenshot while focused does not itself fabricate an outside interval');
 await page.evaluate(()=>drainEvents());
 const other=await context.newPage();await other.goto('about:blank');await other.bringToFront();
 // Disable Playwright focus emulation so actual tab departure/return is observable.
 await page.waitForFunction(()=>document.hidden||!document.hasFocus());await new Promise(r=>setTimeout(r,650));
 await page.bringToFront();await page.waitForFunction(()=>!document.hidden&&document.hasFocus());
 await new Promise(r=>setTimeout(r,250));await page.locator('[data-section="initial"]').click();await page.evaluate(()=>drainEvents());
 await page.locator('#back').click();
 const researchContext=await browser.newContext(),research=await researchContext.newPage();await login(research,'TEST-ARASTIRMACI');
 const view=await research.evaluate(id=>api('view',undefined,{app_id:id}),id),visits=view.navigation.visits;
 const outside=visits.filter(v=>v.section==='outside');
 check(outside.length===1,'real tab switch creates one outside interval');
 check(outside[0].duration_seconds>=0.5,'time in another tab is measured as outside');
 check(visits.filter(v=>v.activity==='Kilitli AI bölümünü görüntüleme').length===2,'locked AI visit resumes separately after return');
 const presence=view.events.filter(e=>e.type==='app_presence');
 check(presence.some(e=>e.type_tr==='Vaka sekmesi etkinliğini kaybetti')&&presence.some(e=>e.type_tr==='Vaka sekmesi yeniden etkin'),'readable departure and return events persisted by server');
 check(view.events.some(e=>e.type_tr==='Kilitli AI bölümünü görüntüleme'),'locked access event is explicit in researcher log');
 const outsideStart=Date.parse(outside[0].start_at),outsideEnd=Date.parse(outside[0].end_at);
 check(visits.filter(v=>v.section!=='outside'&&v.end_at).every(v=>Date.parse(v.end_at)<=outsideStart||Date.parse(v.start_at)>=outsideEnd),'section durations exclude entire outside interval');
 const csv=await researchContext.request.get(url+`api.php?op=download&format=disco&app_id=${id}&include_test=1`);
 check(csv.status()===200&&(await csv.text()).includes('APP_OUTSIDE'),'actual download includes outside activity');
 await research.evaluate(async id=>{await processView(id);document.querySelector('[data-process-panel="navigation"]').open=true;},id);
 await research.locator('[data-process-panel="navigation"]').screenshot({path:path.join(tmp,'presence-navigation.png')});
 check((await research.locator('[data-process-panel="navigation"]').innerText()).includes('Vaka sekmesi etkin değil'),'outside total and activity visible in researcher screen');
 // A real delayed mock response arrives while another Chrome tab is selected.
 await researchContext.close();await page.bringToFront();await page.locator('#logout').click();await page.locator('#login').waitFor();
 await login(page,'TEST-OGR-03');await page.locator('[data-case="test-case-1"]').click();await page.locator('[data-section="ai"]').click();
 const aiId=await page.evaluate(()=>work.application.id);
 await page.locator('#question').fill('SENTETİK zaman testi: bir örnek açıkla.');
 const chatRequest=page.waitForRequest(r=>r.url().includes('op=chat')&&r.method()==='POST');await page.locator('#send').click();await chatRequest;
 await other.bringToFront();await page.waitForFunction(()=>document.hidden||!document.hasFocus());
 await page.waitForFunction(()=>!busy&&work.messages.some(m=>m.role==='assistant'),null,{polling:100,timeout:15000});await page.evaluate(()=>drainEvents());
 await page.bringToFront();await page.waitForFunction(()=>!document.hidden&&document.hasFocus());await new Promise(r=>setTimeout(r,250));
 await page.locator('[data-section="final"]').click();await page.evaluate(()=>drainEvents());await page.locator('#back').click();
 const rc=await browser.newContext(),rp=await rc.newPage();await login(rp,'TEST-ARASTIRMACI');
 const aiView=await rp.evaluate(id=>api('view',undefined,{app_id:id}),aiId),activities=aiView.navigation.activities;
 const aiOutside=aiView.navigation.visits.filter(v=>v.section==='outside');
 check(aiOutside.length===1&&aiOutside[0].duration_seconds>=4,'real delayed response completes during one outside interval');
 const questionRows=activities.filter(v=>v.activity_type==='ai_question'),responseRows=activities.filter(v=>v.activity_type==='ai_response');
 check(questionRows.length===1&&responseRows.length===1,'live asynchronous flow preserves one question and one response');
 check(responseRows[0].section==='outside'&&Date.parse(responseRows[0].start_at)>=Date.parse(aiOutside[0].start_at)&&Date.parse(responseRows[0].start_at)<=Date.parse(aiOutside[0].end_at),'actual response timestamp lies inside outside interval');
 const waitRows=activities.filter(v=>v.activity_type==='ai_wait');
 check(waitRows.every(v=>Date.parse(v.end_at)<=Date.parse(aiOutside[0].start_at)||Date.parse(v.start_at)>=Date.parse(aiOutside[0].end_at)),'live AI wait never overlaps outside interval');
 check(activities.filter(v=>v.activity_code==='AI_VIEW').every(v=>Date.parse(v.end_at)<=Date.parse(aiOutside[0].start_at)||Date.parse(v.start_at)>=Date.parse(aiOutside[0].end_at)),'no fake AI viewing during real outside response');
 const actualCsv=await rc.request.get(url+`api.php?op=download&format=disco&app_id=${aiId}&include_test=1`);check(actualCsv.status()===200&&(await actualCsv.text()).includes('Vaka sekmesi etkin değilken'),'hidden reply survives actual CSV download');
 await rp.evaluate(async id=>{await processView(id);document.querySelector('[data-process-panel="navigation"]').open=true;},aiId);await rp.locator('[data-process-panel="navigation"]').screenshot({path:path.join(tmp,'presence-ai-hidden-response.png')});
 // Return while a second request is still running, then minimize/restore on final.
 await rc.close();await page.bringToFront();await page.locator('[data-case="test-case-1"]').click();await page.locator('[data-section="ai"]').click();
 await page.locator('#question').fill('SENTETİK ikinci zaman testi: alternatif bir örnek.');
 const secondPost=page.waitForRequest(r=>r.url().includes('op=chat')&&r.method()==='POST');await page.locator('#send').click();const secondKey=(await secondPost).postDataJSON().key;
 await other.bringToFront();await page.waitForFunction(()=>document.hidden||!document.hasFocus());await new Promise(r=>setTimeout(r,650));
 await page.bringToFront();await page.waitForFunction(()=>!document.hidden&&document.hasFocus());check(await page.evaluate(()=>busy),'return before delayed reply leaves the same request pending');
 await page.waitForFunction(()=>!busy&&work.messages.filter(m=>m.role==='assistant').length===2,null,{polling:100,timeout:15000});
 await page.locator('[data-section="final"]').click();await page.waitForFunction(()=>section==='final'&&!dirty&&!saving,null,{polling:100});
 const pageSession=await context.newCDPSession(page),browserSession=await browser.newBrowserCDPSession();
 const target=await pageSession.send('Target.getTargetInfo');const windowInfo=await browserSession.send('Browser.getWindowForTarget',{targetId:target.targetInfo.targetId});
 await browserSession.send('Browser.setWindowBounds',{windowId:windowInfo.windowId,bounds:{windowState:'minimized'}});
 await page.waitForFunction(()=>document.hidden||!document.hasFocus(),null,{polling:100});await new Promise(r=>setTimeout(r,550));const minimizedAt=await page.evaluate(()=>Date.now());
 await browserSession.send('Browser.setWindowBounds',{windowId:windowInfo.windowId,bounds:{windowState:'normal'}});await browserSession.send('Browser.setWindowBounds',{windowId:windowInfo.windowId,bounds:{left:-32000,top:-32000,width:1280,height:900}});await page.bringToFront();await page.waitForFunction(()=>!document.hidden&&document.hasFocus(),null,{polling:100});
 await new Promise(r=>setTimeout(r,200));await page.locator('[data-section="ai"]').evaluate(el=>el.click());await page.evaluate(()=>drainEvents());await page.locator('#back').evaluate(el=>el.click());await page.waitForFunction(()=>work===null,null,{polling:100});
 const lastContext=await browser.newContext(),lastPage=await lastContext.newPage();await login(lastPage,'TEST-ARASTIRMACI');
 const lastView=await lastPage.evaluate(id=>api('view',undefined,{app_id:id}),aiId),second=lastView.ai_requests.find(r=>r.client_key===secondKey),lastParts=lastView.navigation.activities;
 const secondWait=lastParts.filter(v=>v.activity_type==='ai_wait'&&v.request_id===second.id);
 check(secondWait.length===2&&secondWait.every(v=>v.duration_seconds>0),'real pending wait splits into two positive intervals across absence');
 check(lastParts.filter(v=>v.activity_type==='ai_question').length===2&&lastParts.filter(v=>v.activity_type==='ai_response').length===2,'two real questions and responses stay exactly two after all returns');
 const secondSend=lastParts.find(v=>v.activity_type==='ai_question'&&v.request_id===second.id),secondReply=lastParts.find(v=>v.activity_type==='ai_response'&&v.start_at>=secondSend.start_at);
 check(lastParts.filter(v=>v.activity_code==='AI_VIEW').every(v=>Date.parse(v.end_at)<=Date.parse(secondSend.start_at)||Date.parse(v.start_at)>=Date.parse(secondReply.start_at)),'return while waiting is not counted as AI viewing');
 const lastOutside=lastView.navigation.visits.filter(v=>v.section==='outside'),observed=await page.evaluate(id=>window.presenceObserved.filter(e=>e.app_id===id),aiId),departures=observed.filter(e=>!e.active);
 fs.writeFileSync(path.join(tmp,'presence-last-view.json'),JSON.stringify({view:lastView,independent_focus_observations:observed},null,2));
 check(departures.length>=3&&lastOutside.length===departures.length&&lastOutside.every(v=>departures.some(e=>Math.abs(Date.parse(v.start_at)-e.at)<=100)),'each actual DOM focus departure has exactly one outside interval, including two tab departures and minimize');
 check(lastOutside.every(v=>observed.some(e=>e.active&&Math.abs(Date.parse(v.end_at)-e.at)<=100)),'each outside interval ends at an independently observed DOM focus return');
 const minimized=lastOutside.find(v=>Date.parse(v.start_at)<=minimizedAt&&Date.parse(v.end_at)>=minimizedAt);
 check(!!minimized&&minimized.resume_section==='final'&&minimized.duration_seconds>=0.4,'minimizing on final is recorded as outside time');
 check(lastView.navigation.visits.filter(v=>v.section==='final'&&v.end_at).every(v=>Date.parse(v.end_at)<=Date.parse(minimized.start_at)||Date.parse(v.start_at)>=Date.parse(minimized.end_at)),'minimized interval is excluded from final-section duration');
 check(errors.length===0,'student browser has no JavaScript errors');
 console.log(`TOTAL PRESENCE BROWSER ${count} checks passed; isolated synthetic database.`);
}finally{if(browser){try{const session=await browser.newBrowserCDPSession();await session.send('Browser.close');}catch{}await browser.close();}if(chromeProcess)chromeProcess.kill();server.kill();fs.closeSync(log);}})().catch(e=>{console.error(e);process.exitCode=1;});
