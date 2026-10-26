const {chromium}=require(process.env.PLAYWRIGHT_PATH||'playwright');
const {spawn,spawnSync}=require('node:child_process'),fs=require('node:fs'),path=require('node:path'),crypto=require('node:crypto');
const root=path.resolve(__dirname,'..'),tmp=path.join(__dirname,'tmp'),tag=crypto.randomUUID(),config=path.join(tmp,`holistic_${tag}.php`),db=path.join(tmp,`holistic_test_${tag}.sqlite`),password=crypto.randomUUID();fs.mkdirSync(tmp,{recursive:true});
const setMode=mode=>fs.writeFileSync(config,`<?php $c=require __DIR__.'/../../config/example.php'; $c['dsn']='sqlite:${db.replaceAll('\\','/')}'; $c['db_user']=''; $c['db_password']=''; $c['https']=false; $c['ai']['mode']='${mode}'; return $c;`);
setMode('normal');const php=process.env.PHP_BIN||path.join(root,'.tools/php/php.exe'),env={...process.env,VAKALAB_CONFIG:config,VAKALAB_SEED_PASSWORD:password};
for(const command of ['migrate','seed']){const r=spawnSync(php,['bin/console.php',command],{cwd:root,env,encoding:'utf8',windowsHide:true});if(r.status!==0)throw new Error(r.stdout+r.stderr);}
const port=18779,url=`http://127.0.0.1:${port}/`,server=spawn(php,['-S',`127.0.0.1:${port}`,'-t','public'],{cwd:root,env,stdio:'ignore',windowsHide:true});
let browser,count=0;const check=(ok,label)=>{if(!ok)throw new Error(label);console.log(`PASS HOLISTIC BROWSER ${++count} ${label}`);};
(async()=>{try{
 for(let i=0;i<100;i++){try{if((await fetch(url)).ok)break;}catch{}await new Promise(r=>setTimeout(r,100));}
 browser=await chromium.launch({headless:true,executablePath:process.env.CHROME_BIN||undefined});const page=await browser.newPage({viewport:{width:1440,height:1000}}),errors=[],calls=[];
 page.on('pageerror',e=>errors.push(e.message));page.on('request',r=>{if(r.url().includes('op=holistic'))calls.push(r.postDataJSON());});
 check((await page.request.get(url+'api.php?op=holistic')).status()===405,'assessment endpoint rejects GET writes');
 check((await page.request.post(url+'api.php?op=holistic',{data:{action:'generate'}})).status()===403,'assessment endpoint rejects missing CSRF token');
 await page.goto(url);await page.locator('#login').fill('TEST-ARASTIRMACI');await page.locator('#password').fill(password);await page.getByRole('button',{name:'Giriş yap'}).click();await page.locator('[data-overview-row="test-student-6"] [data-process]').click();await page.locator('#holistic-report').waitFor();
 check(await page.locator('[data-review-version="3"]').count()===1&&await page.locator('#new-run').count()===0,'single report editor replaces the old five-section workflow');
 check(await page.locator('#holistic-approve').isDisabled(),'no approval before a report exists');
 const manual='Araştırmacının AI kullanmadan yazdığı rapor. Vaka gerekçeleri ve yansıtma birlikte incelenmiştir. <script>window.injected=true</script>';
 await page.locator('#holistic-report').fill(manual);
 check(await page.locator('.question-review[data-question-id]').count()===6,'six separate question answer feedback and score blocks');
 const feedbacks=page.locator('[id^="holistic-feedback-"]');for(let i=0;i<await feedbacks.count();i++)await feedbacks.nth(i).fill('Araştırmacının yalnız bu soruya özel geri bildirimi: '+i);
 for(const id of ['problem','decision','reason','final_decision','final_reason','reflection']){const card=page.locator(`[data-question-id="${id}"]`);const expected=await page.evaluate(id=>currentAnalysisView.review_questions.find(q=>q.source_id===id),id);check((await card.innerText()).includes(expected.question)&&(await card.locator('.question-answer').innerText())===expected.answer,'correct question and full student answer shown: '+id);}

 const levels=page.locator('[id^="holistic-level-"]');for(let i=0;i<await levels.count();i++)await levels.nth(i).selectOption({index:1});
 const saved=page.waitForResponse(r=>r.url().includes('op=holistic')&&r.request().postDataJSON()?.action==='save');await page.locator('#holistic-save').click();const savedRow=(await(await saved).json()).data;await page.waitForFunction(()=>activeAnalysisSequence===null&&!document.querySelector('.holistic-workspace fieldset[disabled]'));
 check(calls.filter(c=>c.action==='generate').length===0&&await page.locator('#holistic-report').inputValue()===manual,'manual report saves without an AI assessment request');
 check((await page.locator('#holistic-total').innerText()).includes('0 /'),'manual zero scores are retained');
 check((await page.locator('.holistic-workspace').innerText()).includes('Başlangıç–nihai profil karşılaştırması'),'profile comparison remains visible without an AI call');
 await page.locator('#holistic-approve').click();await page.waitForFunction(()=>activeAnalysisSequence===null&&!document.querySelector('.holistic-workspace fieldset[disabled]'));check(await page.locator('#holistic-show').isDisabled(),'approval remains separate from publication and collection gate');
 await page.locator('[data-student-report]').click();await page.locator('dialog .student-report').waitFor();
 check(await page.locator('dialog .question-review').count()===6,'student preview has six question assessments');
 check((await page.locator('dialog .student-report').innerText()).includes('Başlangıç–nihai profil karşılaştırması'),'student preview includes profile comparison');

 check((await page.locator('dialog .student-report').innerText()).includes(manual)&&!await page.evaluate(()=>!!window.injected),'report preview escapes pasted HTML');await page.locator('#dialog-close').click();
 // Deliberately lose a successful response; automatic readback must still show it.
 let backendFinished,releaseReply;const backendDone=new Promise(r=>backendFinished=r),gate=new Promise(r=>releaseReply=r);
 await page.route('**/api.php?op=holistic',async route=>{if(route.request().postDataJSON().action!=='generate')return route.continue();await route.fetch();backendFinished();await gate;await route.abort('failed');});
 await page.locator('#holistic-generate').click();await backendDone;
 try{
   await page.waitForFunction(()=>document.querySelector('#holistic-report').value.includes('Bu sentetik rapor'),null,{timeout:10000});
   check(true,'AI result appears without F5 even when its POST reply is lost');
   check(await page.locator('#holistic-generate').isDisabled()&&await page.locator('#holistic-save').isDisabled(),'duplicate generate and manual save are blocked during pending request');
 }finally{releaseReply();}
 await page.waitForFunction(()=>activeAnalysisSequence===null&&!document.querySelector('.holistic-workspace fieldset[disabled]'));await page.unroute('**/api.php?op=holistic');
 check(calls.filter(c=>c.action==='generate').length===1,'completed AI generation is not resent by observer');
 const report=await page.evaluate(()=>holisticState(currentAnalysisView).report);check(report.id===savedRow.id&&!report.approved_at&&!report.published_at,'one current report is updated and requires fresh approval');
 await page.locator('#holistic-report').fill('AI taslağını araştırmacı olarak düzenledim; gerekçeleri rubriğe göre netleştirdim.');
 const untouchedFeedback=await page.locator('#holistic-feedback-0').inputValue();
 await page.locator('#holistic-feedback-2').fill('Yalnız gerekçe sorusuna ait araştırmacı düzeltmesi; diğer soruların geri bildirimi korunur.');
 for(let i=0;i<await levels.count();i++)await levels.nth(i).selectOption({index:await levels.nth(i).locator('option').count()-1});
 check(await page.locator('#holistic-approve').isDisabled(),'unsaved edits cannot be approved');
 await page.locator('#holistic-save').click();await page.waitForFunction(()=>activeAnalysisSequence===null&&!document.querySelector('.holistic-workspace fieldset[disabled]'));
 check(calls.filter(c=>c.action==='generate').length===1,'researcher can edit AI report and criterion scores without another AI call');
 check((await page.locator('#holistic-feedback-2').inputValue()).startsWith('Yalnız gerekçe')&&await page.locator('#holistic-feedback-0').inputValue()===untouchedFeedback,'editing one question does not change another question feedback');
 await page.locator('#holistic-approve').click();await page.waitForFunction(()=>activeAnalysisSequence===null&&!document.querySelector('.holistic-workspace fieldset[disabled]'));
 await page.evaluate(async()=>{await api('admin',{action:'collection',case_id:currentAnalysisView.application.case_id,closed:true});await processView(currentAnalysisView.application.id);});
 await page.locator('#holistic-show').click();await page.waitForFunction(()=>activeAnalysisSequence===null&&!document.querySelector('.holistic-workspace fieldset[disabled]'));check(await page.locator('#holistic-hide').isEnabled(),'explicit show publishes approved report in closed case');
 for(const format of ['docx','pdf','xlsx']){const d=page.waitForEvent('download');await page.locator(`[data-holistic-download="${format}"]`).click();const result=await d;check((await result.failure())===null,'holistic '+format+' download');}
 await page.locator('#holistic-hide').click();await page.waitForFunction(()=>activeAnalysisSequence===null&&!document.querySelector('.holistic-workspace fieldset[disabled]'));check(await page.locator('#holistic-hide').isDisabled()&&await page.locator('#holistic-show').isEnabled(),'report publication can be withdrawn and restored');
 const previous=await page.locator('#holistic-report').inputValue();setMode('invalid_json');await page.locator('#holistic-generate').click();await page.waitForFunction(()=>activeAnalysisSequence===null&&!document.querySelector('.holistic-workspace fieldset[disabled]'));check(await page.locator('#holistic-report').inputValue()===previous,'invalid AI output preserves saved researcher report');setMode('normal');
 for(const width of [1440,768,390]){await page.setViewportSize({width,height:1000});check(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth+1),'single report editor fits '+width+'px');}
 await page.evaluate(()=>notify(''));await page.locator('.question-review').first().screenshot({path:path.join(tmp,'holistic-editor-390.png')});
 await page.locator('#admin-back').click();await page.waitForTimeout(2200);check(await page.locator('#process-data').count()===0,'status observer stops after leaving the work');
 check(errors.length===0,'no browser JavaScript errors: '+errors.join('; '));console.log('TOTAL HOLISTIC BROWSER '+count+' checks passed; local mock provider only.');
}finally{if(browser)await browser.close();server.kill();}})().catch(e=>{console.error(e);process.exitCode=1;});
