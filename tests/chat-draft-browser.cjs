const {chromium}=require(process.env.PLAYWRIGHT_PATH||'playwright');
const {spawn,spawnSync}=require('node:child_process'),fs=require('node:fs'),path=require('node:path'),crypto=require('node:crypto');
const root=path.resolve(__dirname,'..'),tmp=path.join(root,'tests/tmp'),tag=crypto.randomUUID(),config=path.join(tmp,`chat_draft_${tag}.php`),db=path.join(tmp,`chat_draft_${tag}.sqlite`),password=crypto.randomUUID();
fs.mkdirSync(tmp,{recursive:true});
const setMode=mode=>fs.writeFileSync(config,`<?php $c=require __DIR__.'/../../config/example.php';$c['dsn']='sqlite:${db.replaceAll('\\','/')}';$c['db_user']='';$c['db_password']='';$c['https']=false;$c['ai']['mode']='${mode}';return $c;`);
setMode('normal');const php=process.env.PHP_BIN||path.join(root,'.tools/php/php.exe'),env={...process.env,VAKALAB_CONFIG:config,VAKALAB_SEED_PASSWORD:password};
for(const command of ['migrate','seed']){const p=spawnSync(php,['bin/console.php',command],{cwd:root,env,encoding:'utf8',windowsHide:true});if(p.status!==0)throw Error(p.stdout+p.stderr);}
setMode('delay');const port=18775,url=`http://127.0.0.1:${port}/`,log=fs.openSync(path.join(tmp,`chat-draft-${tag}.log`),'w');
const server=spawn(php,['-S',`127.0.0.1:${port}`,'-t','public'],{cwd:root,env,stdio:['ignore',log,log],windowsHide:true});
let browser,count=0;const check=(ok,name)=>{if(!ok)throw Error(name);console.log(`PASS CHAT DRAFT ${++count} ${name}`);};
(async()=>{try{
 for(let i=0;i<100;i++){try{if((await fetch(url)).ok)break;}catch{}await new Promise(r=>setTimeout(r,100));}
 browser=await chromium.launch({headless:true,executablePath:process.env.CHROME_BIN||undefined});const page=await browser.newPage(),errors=[];page.on('pageerror',e=>errors.push(e.message));
 await page.goto(url);await page.locator('#login').fill('TEST-OGR-05');await page.locator('#password').fill(password);await page.getByRole('button',{name:'Giriş yap'}).click();await page.locator('[data-case="test-case-1"]').click();await page.locator('[data-section="ai"]').click();
 const appId=await page.evaluate(()=>work.application.id);
 const initialAnswers=await page.locator('#chat .message.assistant').count();
 const send=async(text,whileWaiting)=>{
  const response=page.waitForResponse(r=>r.url().includes('op=chat'));
  await page.locator('#question').fill(text);await page.locator('#send').click();
  await page.locator('#chat .chat-waiting').waitFor();
  await whileWaiting();
  await response;await page.waitForFunction(()=>busy===false);await page.locator('#send:enabled').waitFor();
 };
 const original='  SENTETİK: vakayı biliyor musun\n';
 await send(original,async()=>{
  check(await page.locator('#chat [data-pending-message]').innerText()==='Siz\n'+original.trim(),'sent text appears immediately in the chat');
  check(await page.locator('#question').isDisabled()&&await page.locator('#question').inputValue()===''&&await page.locator('#send').isDisabled(),'waiting composer is empty and locked');
  const waiting=page.getByRole('status').filter({hasText:'AI yanıt hazırlıyor…'});
  check(await waiting.isVisible()&&await waiting.locator('.chat-waiting-dots span').first().evaluate(el=>getComputedStyle(el).animationName)!=='none','waiting animation appears in the chat with a status label');
  await page.keyboard.insertText('SENTETİK: beklerken yazılamaz');
  check(await page.locator('#question').inputValue()==='','keyboard input cannot change the locked composer');
  await page.locator('[data-section="final"]').click();
  check(await page.locator('#final_reason').isEditable(),'student can still work in other sections');
  await page.locator('[data-section="ai"]').click();
  check(await page.locator('#question').isDisabled()&&await waiting.isVisible(),'switching back to chat keeps the pending state');
 });
 check(await page.locator('#chat .chat-waiting').count()===0&&await page.locator('#chat .message.assistant').count()===initialAnswers+1,'reply replaces waiting animation');
 check(await page.locator('#question').isEditable()&&await page.locator('#question').inputValue()==='','reply unlocks an empty composer including with trailing whitespace in the sent draft');
 await page.evaluate(()=>save());
 let view=await page.evaluate(id=>api('view',undefined,{app_id:id}),appId);
 check(JSON.parse(view.application.draft).question==='','cleared question persists to server draft');
 check(view.messages.filter(m=>m.role==='user'&&m.content===original.trim()).length===1,'server chat has one normalized sent message');
 await page.reload();await page.locator('[data-case="test-case-1"]').click();await page.locator('[data-section="ai"]').click();
 check(await page.locator('#question').inputValue()==='','reload does not restore sent message as unsent draft');
 await page.setViewportSize({width:390,height:844});
 await page.emulateMedia({reducedMotion:'reduce'});
 await send('SENTETİK: İkinci gönderilen soru\n',async()=>{
  check(await page.locator('#chat .chat-waiting').isVisible(),'waiting indicator appears on mobile');
  check(await page.locator('.chat-waiting-dots span').first().evaluate(el=>getComputedStyle(el).animationName)==='none','reduced motion keeps the text and stops the dots');
  check(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth),'waiting UI fits the mobile viewport');
 });
 check(await page.locator('#question').isEditable()&&await page.locator('#question').inputValue()==='','a second response also unlocks the empty composer');
 const reopened='SENTETİK: Yanıt beklerken vakaya geri dön';
 let release,entered;
 const gate=new Promise(resolve=>release=resolve),serverAnswered=new Promise(resolve=>entered=resolve);
 await page.route('**/api.php?op=chat',async route=>{
  const response=await route.fetch();entered();await gate;await route.fulfill({response});
 },{times:1});
 try{
  await page.locator('#question').fill(reopened);await page.locator('#send').click();
  await serverAnswered;
  await page.locator('#back').click();await page.locator('[data-case="test-case-1"]').click();
  await page.locator('[data-section="ai"]').click();
  check(await page.locator('#question').isDisabled()&&await page.locator('#question').inputValue()==='','reopening the same case keeps the pending composer locked and empty');
  check(await page.locator('#chat .message.user').filter({hasText:reopened}).count()===1,'reopening replaces the pending message with its single saved copy');
 }finally{release();}
 await page.locator('#send:enabled').waitFor();await page.evaluate(()=>save());
 view=await page.evaluate(id=>api('view',undefined,{app_id:id}),appId);
 check(JSON.parse(view.application.draft).question===''&&view.messages.filter(m=>m.content===reopened).length===1,'returning to a pending chat does not leave a sent draft or duplicate the message');
 const rejected='SENTETİK: Bağlantı hatasında korunacak soru\n';
 await page.route('**/api.php?op=chat',r=>r.abort('failed'));
 await page.locator('#question').fill(rejected);await page.locator('#send').click();await page.waitForFunction(()=>document.querySelector('#notice').textContent.includes('Ağ belirsizliğinde')&&!busy);
 check(await page.locator('#question').inputValue()===rejected,'network failure preserves unsent draft for retry');
 check(await page.locator('#question').isEditable()&&await page.locator('#chat .chat-waiting').count()===0,'network failure stops waiting and unlocks the composer');
 check(await page.evaluate(()=>sessionStorage.getItem(recoveryKey()+':request')!==null),'network uncertainty preserves request idempotency key');
 check(errors.length===0,'no browser JavaScript errors');
 console.log(`TOTAL CHAT DRAFT ${count} checks passed. Isolated SQLite and delayed mock only; no evaluation or real API calls.`);
}finally{if(browser)await browser.close();server.kill();fs.closeSync(log);}})().catch(e=>{console.error(e);process.exitCode=1;});
