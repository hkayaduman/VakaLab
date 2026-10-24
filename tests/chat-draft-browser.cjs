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
 const send=async(text,nextDraft)=>{
  const response=page.waitForResponse(r=>r.url().includes('op=chat'));
  await page.locator('#question').fill(text);await page.locator('#send').click();
  await page.waitForFunction(()=>busy===true);
  if(nextDraft!==undefined)await page.locator('#question').fill(nextDraft);
  await response;await page.waitForFunction(()=>busy===false);await page.locator('#send:enabled').waitFor();
 };
 const original='  SENTETİK: vakayı biliyor musun\n';
 await send(original);
 check(await page.locator('#question').inputValue()==='','reply clears exactly submitted draft including trailing newline');
 await page.evaluate(()=>save());
 let view=await page.evaluate(id=>api('view',undefined,{app_id:id}),appId);
 check(JSON.parse(view.application.draft).question==='','cleared question persists to server draft');
 check(view.messages.filter(m=>m.role==='user'&&m.content===original.trim()).length===1,'server chat has one normalized sent message');
 await page.reload();await page.locator('[data-case="test-case-1"]').click();await page.locator('[data-section="ai"]').click();
 check(await page.locator('#question').inputValue()==='','reload does not restore sent message as unsent draft');
 const next='SENTETİK: Yeni ve henüz gönderilmemiş taslak\n';
 await send('SENTETİK: İkinci gönderilen soru\n',next);
 check(await page.locator('#question').inputValue()===next,'typing another draft during response is preserved');
 await page.evaluate(()=>save());view=await page.evaluate(id=>api('view',undefined,{app_id:id}),appId);
 check(JSON.parse(view.application.draft).question===next,'new unsent draft is persisted rather than erased');
 check(!view.messages.some(m=>m.content===next.trim()),'new draft is not silently sent as a message');
 await send(next);
 check(await page.locator('#question').inputValue()==='','sending the new draft clears it only after its own response');
 const rejected='SENTETİK: Bağlantı hatasında korunacak soru\n';
 await page.route('**/api.php?op=chat',r=>r.abort('failed'));
 await page.locator('#question').fill(rejected);await page.locator('#send').click();await page.waitForFunction(()=>document.querySelector('#notice').textContent.includes('Ağ belirsizliğinde')&&!busy);
 check(await page.locator('#question').inputValue()===rejected,'network failure preserves unsent draft for retry');
 check(await page.evaluate(()=>sessionStorage.getItem(recoveryKey()+':request')!==null),'network uncertainty preserves request idempotency key');
 check(errors.length===0,'no browser JavaScript errors');
 console.log(`TOTAL CHAT DRAFT ${count} checks passed. Isolated SQLite and delayed mock only; no evaluation or real API calls.`);
}finally{if(browser)await browser.close();server.kill();fs.closeSync(log);}})().catch(e=>{console.error(e);process.exitCode=1;});
