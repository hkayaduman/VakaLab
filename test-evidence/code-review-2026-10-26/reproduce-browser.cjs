const {chromium}=require(process.env.PLAYWRIGHT_PATH||'playwright');
const {spawn,spawnSync}=require('node:child_process'),fs=require('node:fs'),path=require('node:path'),crypto=require('node:crypto');
const root=path.resolve(__dirname,'../..'),tmp=path.join(root,'tests/tmp'),tag=crypto.randomUUID(),config=path.join(tmp,`review_${tag}.php`),db=path.join(tmp,`review_${tag}.sqlite`),password=crypto.randomUUID();
fs.mkdirSync(tmp,{recursive:true});
const mode=value=>fs.writeFileSync(config,`<?php $c=require __DIR__.'/../../config/example.php';$c['dsn']='sqlite:${db.replaceAll('\\','/')}';$c['db_user']='';$c['db_password']='';$c['https']=false;$c['ai']['mode']='${value}';return $c;`);
mode('normal');
const php=path.join(root,'.tools/php/php.exe'),env={...process.env,VAKALAB_CONFIG:config,VAKALAB_SEED_PASSWORD:password};
for(const command of ['migrate','seed']){const p=spawnSync(php,['bin/console.php',command],{cwd:root,env,encoding:'utf8',windowsHide:true});if(p.status!==0)throw Error(p.stdout+p.stderr);}
const port=18896,url=`http://127.0.0.1:${port}/`,server=spawn(php,['-S',`127.0.0.1:${port}`,'-t','public'],{cwd:root,env,stdio:'ignore',windowsHide:true});
let browser;
(async()=>{try{
 for(let i=0;i<100;i++){try{if((await fetch(url)).ok)break;}catch{}await new Promise(r=>setTimeout(r,100));}
 browser=await chromium.launch({headless:true,executablePath:process.env.CHROME_BIN});const page=await browser.newPage();
 await page.goto(url);await page.locator('#login').fill('TEST-OGR-05');await page.locator('#password').fill(password);await page.getByRole('button',{name:'Giriş yap'}).click();await page.locator('[data-case="test-case-1"]').click();await page.locator('[data-section="ai"]').click();
 await page.route('**/api.php?op=chat',async route=>{await route.fetch();await route.abort('failed');},{times:1});
 await page.locator('#question').fill('FIRST SENT QUESTION');await page.locator('#send').click();
 await page.waitForFunction(()=>!busy&&document.querySelector('#notice').textContent.includes('Ağ belirsizliğinde'));
 await page.locator('#question').fill('SECOND DIFFERENT QUESTION');await page.locator('#send').click();
 await page.waitForFunction(()=>!busy&&work.draft.question==='');
 console.log(JSON.stringify(await page.evaluate(()=>({finding:'lost_response_changed_draft',draft:work.draft.question,second_message_in_chat:work.messages.some(m=>m.content==='SECOND DIFFERENT QUESTION'),first_message_in_chat:work.messages.some(m=>m.content==='FIRST SENT QUESTION')}))));
 // A failed request is retried, then the user opens a different case before its result arrives.
 mode('error');await page.locator('#question').fill('FAILED QUESTION TO RETRY');await page.locator('#send').click();await page.locator('[data-retry-request]').waitFor();
 mode('normal');let release;const gate=new Promise(r=>release=r);let entered;const hit=new Promise(r=>entered=r);
 await page.route('**/api.php?op=chat',async route=>{const response=await route.fetch();entered();await gate;await route.fulfill({response});},{times:1});
 await page.locator('[data-retry-request]').click();await page.locator('#work-confirm-accept').click();await hit;
 await page.locator('#back').click();await page.locator('[data-case="test-case-3"]').click();await page.waitForFunction(()=>work?.application.case_id==='test-case-3');const newId=await page.evaluate(()=>work.application.id);release();
 await page.waitForFunction(()=>work.messages.some(m=>m.content==='FAILED QUESTION TO RETRY'));
 console.log(JSON.stringify(await page.evaluate(async id=>({finding:'retry_cross_case',current_app:work.application.id===id,old_case_message_in_new_work:work.messages.some(m=>m.content==='FAILED QUESTION TO RETRY'),real_new_case_messages:(await api('view',undefined,{app_id:id})).messages.length}),newId)));
}finally{if(browser)await browser.close();server.kill();}})().catch(e=>{console.error(e);process.exitCode=1;});
