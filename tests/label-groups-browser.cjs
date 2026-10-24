const {chromium}=require(process.env.PLAYWRIGHT_PATH||'playwright');
const {spawn,spawnSync}=require('node:child_process'),fs=require('node:fs'),path=require('node:path'),crypto=require('node:crypto');
const root=path.resolve(__dirname,'..'),tmp=path.join(root,'tests/tmp'),tag=crypto.randomUUID(),config=path.join(tmp,`chat_draft_${tag}.php`),db=path.join(tmp,`chat_draft_${tag}.sqlite`),password=crypto.randomUUID();
fs.mkdirSync(tmp,{recursive:true});
const setMode=mode=>fs.writeFileSync(config,`<?php $c=require __DIR__.'/../../config/example.php';$c['dsn']='sqlite:${db.replaceAll('\\','/')}';$c['db_user']='';$c['db_password']='';$c['https']=false;$c['ai']['mode']='${mode}';return $c;`);
setMode('normal');const php=process.env.PHP_BIN||path.join(root,'.tools/php/php.exe'),env={...process.env,VAKALAB_CONFIG:config,VAKALAB_SEED_PASSWORD:password};
for(const command of ['migrate','seed']){const p=spawnSync(php,['bin/console.php',command],{cwd:root,env,encoding:'utf8',windowsHide:true});if(p.status!==0)throw Error(p.stdout+p.stderr);}
setMode('normal');const port=18777,url=`http://127.0.0.1:${port}/`,log=fs.openSync(path.join(tmp,`chat-draft-${tag}.log`),'w');
const server=spawn(php,['-S',`127.0.0.1:${port}`,'-t','public'],{cwd:root,env,stdio:['ignore',log,log],windowsHide:true});
let browser,count=0;const check=(ok,name)=>{if(!ok)throw Error(name);console.log(`PASS LABEL GROUPS ${++count} ${name}`);};
(async()=>{try{
 for(let i=0;i<100;i++){try{if((await fetch(url)).ok)break;}catch{}await new Promise(r=>setTimeout(r,100));}
 browser=await chromium.launch({headless:true,executablePath:process.env.CHROME_BIN||undefined});const page=await browser.newPage(),errors=[];page.on('pageerror',e=>errors.push(e.message));
 await page.goto(url);await page.locator('#login').fill('TEST-ARASTIRMACI');await page.locator('#password').fill(password);await page.getByRole('button',{name:'Giriş yap'}).click();await page.locator('[data-admin-tab="definitions"]').click();
 await page.locator('#add-label').click();check(await page.locator('#d-target-role option').count()===3,'label form offers explicit student and AI groups');
 await page.locator('#d-name').fill('SENTETİK AI gerekçe sundu');await page.locator('#d-body').fill('Yalnız AI yanıtı gerekçe sunduğunda.');await page.locator('#d-target-role').selectOption('assistant');await page.locator('#commit').click();await page.waitForFunction(()=>!document.querySelector('dialog[open]')||document.querySelector('#dialog-error:not([hidden])'));if(await page.locator('#dialog-error:not([hidden])').count())throw Error(await page.locator('#dialog-error').innerText());
 const def=await page.evaluate(()=>dash.definitions.find(d=>d.name==='SENTETİK AI gerekçe sundu'));check(JSON.parse(def.body).target_role==='assistant','AI group persisted by server');
 await page.locator(`[data-edit-def="${def.id}"]`).click();check(await page.locator('#d-target-role').inputValue()==='assistant','edit form preserves group');await page.locator('#dialog-close').click();
 const appId=await page.evaluate(()=>dash.applications.find(a=>a.user_id==='test-student-4').id);
 await page.evaluate(async id=>{await processView(id);},appId);await page.locator('[data-process-panel="conversation"] summary').click();
 await page.locator('[data-label-message]').first().click();check(await page.locator('dialog [data-choose-label]').count()===8,'student message chooser contains only student labels');check(await page.locator(`dialog [data-choose-label="${def.id}"]`).count()===0,'student chooser excludes AI label');await page.locator('#dialog-close').click();
 await page.locator('[data-label-message]').nth(1).click();check(await page.locator('dialog [data-choose-label]').count()===1,'AI chooser contains only AI label');check(await page.locator(`dialog [data-choose-label="${def.id}"]`).count()===1,'AI chooser includes its own label');
 await page.locator('#dialog-close').click();await page.evaluate(async()=>{await adminHome();});await page.locator('[data-admin-tab="definitions"]').click();await page.locator('#add-label').click();
 check(await page.locator('#d-topic-scope option').count()===4,'scope selector offers pedagogy, outside and both with an empty option');await page.locator('#d-name').fill('SENTETİK Konu dışı istek');await page.locator('#d-target-role').selectOption('user');await page.locator('#d-topic-scope').selectOption('outside');await page.locator('#d-body').fill('Yalnız konu dışı kişisel yatırım talepleri.');await page.locator('#commit').click();await page.locator('dialog').waitFor({state:'detached'});
 const scoped=await page.evaluate(()=>dash.definitions.find(d=>d.name==='SENTETİK Konu dışı istek'));check(JSON.parse(scoped.body).topic_scope==='outside','researcher-selected outside scope is persisted');await page.locator(`[data-edit-def="${scoped.id}"]`).click();check(await page.locator('#d-topic-scope').inputValue()==='outside','scope selection survives editing');await page.screenshot({path:path.join(tmp,'student-label-scope.png'),fullPage:true});await page.locator('#dialog-close').click();
 check(errors.length===0,'no browser runtime errors');await page.screenshot({path:path.join(tmp,'label-groups.png'),fullPage:true});
 console.log(`TOTAL LABEL GROUPS ${count} checks passed; isolated synthetic database.`);
 }finally{if(browser)await browser.close();server.kill();fs.closeSync(log);}})().catch(e=>{console.error(e);process.exitCode=1;});
