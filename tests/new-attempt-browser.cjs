/* New researcher workflows, entirely isolated from config/local.php and port 8088. */
const {chromium}=require(process.env.PLAYWRIGHT_PATH||'playwright');
const {spawn,spawnSync}=require('node:child_process'),fs=require('node:fs'),path=require('node:path'),crypto=require('node:crypto');
const root=path.resolve(__dirname,'..'),tmp=path.join(__dirname,'tmp'),tag=crypto.randomUUID(),config=path.join(tmp,`researcher_ui_config_${tag}.php`),db=path.join(tmp,`researcher_ui_test_${tag}.sqlite`),password=crypto.randomUUID();fs.mkdirSync(tmp,{recursive:true});
fs.writeFileSync(config,`<?php $c=require __DIR__.'/../../config/example.php';$c['dsn']='sqlite:${db.replaceAll('\\','/')}';$c['db_user']='';$c['db_password']='';$c['https']=false;return $c;`);
const php=process.env.PHP_BIN||path.join(root,'.tools/php/php.exe'),env={...process.env,VAKALAB_CONFIG:config,VAKALAB_SEED_PASSWORD:password};
for(const command of ['migrate','seed']){const p=spawnSync(php,['bin/console.php',command],{cwd:root,env,encoding:'utf8',windowsHide:true});if(p.status!==0)throw new Error(p.stdout+p.stderr);}
let browser,server,count=0;const check=(ok,name)=>{if(!ok)throw new Error(name);console.log(`PASS UI ${++count} ${name}`);};
(async()=>{const sock=require('node:net').createServer();await new Promise(r=>sock.listen(0,'127.0.0.1',r));const port=sock.address().port;await new Promise(r=>sock.close(r));const url=`http://127.0.0.1:${port}/`,log=fs.openSync(path.join(tmp,`researcher-ui-${tag}.log`),'w');
server=spawn(php,['-S',`127.0.0.1:${port}`,'-t','public'],{cwd:root,env,stdio:['ignore',log,log],windowsHide:true});
try{
 for(let i=0;i<100;i++){try{if((await fetch(url)).ok)break;}catch{}await new Promise(r=>setTimeout(r,100));}
 browser=await chromium.launch({headless:true,executablePath:process.env.CHROME_BIN||undefined});
 const errors=[],context=await browser.newContext({viewport:{width:1440,height:1000}}),page=await context.newPage();page.on('pageerror',e=>errors.push(e.message));
 const login=async(p,user)=>{await p.goto(url);await p.locator('#login').fill(user);await p.locator('#password').fill(password);await p.getByRole('button',{name:'Giriş yap'}).click();};

 await login(page,'TEST-ARASTIRMACI');await page.locator('#overview-case').waitFor();
 await page.locator('#overview-search').fill('TEST-OGR-06');await page.locator('[data-process]').click();
 await page.locator('#new-attempt').click();await page.locator('#commit').click();
 await page.locator('#dialog-error').waitFor();check(await page.locator('#dialog-error').isVisible(),'new attempt requires reason in UI');
 await page.locator('#reason').fill('SENTETİK yeniden deneme');await page.locator('#commit').click();await page.locator('dialog').waitFor({state:'detached'});
 check((await page.locator('#root').innerText()).includes('Deneme 2'),'researcher sees new attempt number');
 check(await page.locator('#new-attempt').isDisabled(),'cannot open another unfinished attempt');
 check(await page.locator('[data-attempt]').count()===2,'researcher can inspect both attempts');
 await page.locator('[data-attempt]').last().click();await page.locator('#new-attempt').waitFor();
 check(await page.locator('#new-attempt').isDisabled(),'old attempt cannot create extra successor');
 const sc=await browser.newContext(),student=await sc.newPage();student.on('pageerror',e=>errors.push(e.message));await login(student,'TEST-OGR-06');
 await student.locator('[data-case="test-case-1"]').click();await student.locator('[data-section="profile"]').click();
 check(await student.locator('[data-student-attempt]').count()===2,'student can view historical attempt');
 check(await student.locator('#knowledge').inputValue()==='','new profile is blank');
 await student.locator('[data-student-attempt]').last().click();await student.waitForFunction(()=>[...document.querySelectorAll('[data-student-attempt]')].at(-1)?.disabled);await student.locator('[data-section="final"]').click();
 check(await student.locator('#final_decision').isDisabled(),'old final response remains read only');
 check((await student.locator('#final_decision').inputValue()).length>0,'old answer preserved');
 await student.locator('[data-student-attempt]').first().click();await student.waitForFunction(()=>document.querySelector('[data-student-attempt]')?.disabled);await student.locator('[data-section="profile"]').click();
 await student.locator('#knowledge').selectOption('4');await student.locator('[data-section="case"]').click();await student.locator('#back').click();
 await student.locator('[data-case="test-case-1"]').waitFor();check(await student.locator('[data-case="test-case-1"]').count()===1,'one case card despite two attempts');
 await student.locator('[data-case="test-case-1"]').click();await student.locator('[data-section="profile"]').click();
 check(await student.locator('#knowledge').inputValue()==='4','new attempt saves independently and reopens latest');
 for(const view of ['visits','events']){
   const response=await context.request.get(url+'api.php?op=download&format=disco&view='+view+'&case_id=test-case-1&user_id=test-student-6&include_test=1');
   const csv=await response.text();const ids=await student.evaluate(()=>work.attempts.map(t=>t.id));
   check(response.ok()&&csv.split('\n')[0].includes('Deneme numarası')&&ids.every(id=>csv.includes(id)),view+' real CSV download includes attempt column and both attempt IDs');
 }
 await student.setViewportSize({width:390,height:844});check(await student.evaluate(()=>document.documentElement.scrollWidth<=innerWidth+1),'attempt navigation fits mobile');
 await student.screenshot({path:path.join(tmp,'new-attempt-mobile.png'),fullPage:true});
 check(errors.length===0,'no browser errors');console.log('OK '+count+' attempt UI checks');await sc.close();
}finally{if(browser)await browser.close();if(server)server.kill();fs.closeSync(log);}})().catch(e=>{console.error(e);process.exitCode=1;});
