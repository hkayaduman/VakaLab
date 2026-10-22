const selectQuestionScore=require('./review-editor.cjs');
const {chromium}=require(process.env.PLAYWRIGHT_PATH||'playwright');
const {spawn,spawnSync}=require('node:child_process'),fs=require('node:fs'),path=require('node:path'),crypto=require('node:crypto');
const root=path.resolve(__dirname,'..'),tmp=path.join(__dirname,'tmp'),tag=crypto.randomUUID(),config=path.join(tmp,`review_v2_config_${tag}.php`),db=path.join(tmp,`review_v2_test_${tag}.sqlite`),password=crypto.randomUUID();fs.mkdirSync(tmp,{recursive:true});
const setMode=mode=>fs.writeFileSync(config,`<?php $c=require __DIR__.'/../../config/example.php'; $c['dsn']='sqlite:${db.replaceAll('\\','/')}'; $c['db_user']=''; $c['db_password']=''; $c['https']=false; $c['ai']['mode']='${mode}'; return $c;`);
setMode('normal');const php=process.env.PHP_BIN||path.join(root,'.tools/php/php.exe'),env={...process.env,VAKALAB_CONFIG:config,VAKALAB_SEED_PASSWORD:password};
for(const command of ['migrate','seed']){const result=spawnSync(php,['bin/console.php',command],{cwd:root,env,encoding:'utf8',windowsHide:true});if(result.status!==0)throw new Error(result.stdout+result.stderr);}
const port=18779,url=`http://127.0.0.1:${port}/`,log=fs.openSync(path.join(tmp,'review-v2-server.log'),'w'),server=spawn(php,['-S',`127.0.0.1:${port}`,'-t','public'],{cwd:root,env,stdio:['ignore',log,log],windowsHide:true});
let browser,count=0;const check=(ok,label)=>{if(!ok)throw new Error(label);console.log(`PASS V2 BROWSER ${++count} ${label}`);};
(async()=>{try{
 for(let i=0;i<100;i++){try{if((await fetch(url)).ok)break;}catch{}await new Promise(r=>setTimeout(r,100));}
 browser=await chromium.launch({headless:true,executablePath:process.env.CHROME_BIN||undefined});const context=await browser.newContext({viewport:{width:1440,height:1000}}),page=await context.newPage(),errors=[];page.on('pageerror',e=>errors.push(e.message));
 await page.goto(url);await page.locator('#login').fill('TEST-ARASTIRMACI');await page.locator('#password').fill(password);await page.getByRole('button',{name:'Giriş yap'}).click();await page.locator('[data-overview-row="test-student-6"] [data-process]').click();await page.locator('#new-run').waitFor();
 check(await page.locator('.legacy-review-history').count()===0&&!(await page.locator('#root').innerText()).includes('Rapor geçmişi'),'history panels removed');
 const calls=[];let inflight=0,maxInflight=0;page.on('request',req=>{if(req.url().includes('op=step')){calls.push(req.postDataJSON().step);inflight++;maxInflight=Math.max(inflight,maxInflight);}});page.on('response',res=>{if(res.url().includes('op=step'))inflight--;});
 setMode('invalid_json');await page.locator('#new-run').click();await page.locator('#commit').click();await page.locator('dialog').waitFor({state:'detached'});const review=page.locator('[data-review-version="2"]').first();
 await review.locator('[data-review-section="2"] .status.error').waitFor();await page.waitForFunction(()=>activeAnalysisSequence===null);
 check(calls.join(',')==='1,2','invalid profile feedback stops the sequence');
 setMode('normal');await review.locator('[data-start-analysis]').click();await review.locator('[data-review-section="6"] [data-v2-edit]').waitFor();await page.waitForFunction(()=>activeAnalysisSequence===null);
 check(calls.join(',')==='1,2,2,3,5,4,6'&&maxInflight===1,'sections prepared serially in requested order');
 check(await review.locator('[data-review-section]').count()===5,'exactly five simple sections');
 const profile=review.locator('[data-review-section="2"]'),stats=review.locator('[data-review-section="5"]');
 check(await profile.locator('[data-question-score]').count()===0&&await profile.locator('[data-profile-feedback]').isVisible(),'profile only has comparison and editable feedback');
 check((await profile.innerText()).includes('Bu aşamada sorulmadı')&&(await profile.innerText()).includes('+2'),'profile shows missing final fields and real confidence change');
 check(await stats.locator('[data-chat-statistics]').isVisible()&&await stats.locator('button').count()===0&&await stats.locator('[data-question-feedback],[data-question-score]').count()===0,'chat only displays statistics without score, comment or decisions');
 for(const [step,expected] of [[3,3],[4,2],[6,1]])check(await review.locator(`[data-review-section="${step}"] [data-question-id]`).count()===expected,`section ${step} scores each answer separately`);
 check(await review.locator('[data-create-report]').isDisabled(),'report waits for four human reviews');
 await profile.locator('[data-v2-reject]').click();await profile.locator('[data-v2-status]').filter({hasText:'Reddedildi'}).waitFor();check(true,'profile comment can be rejected without score');
 const profileFeedback='Başlangıç güveni sekizden ona yükselmiş; ön bilgi ve zorluk için ikinci bir ölçüm bulunmuyor.';
 await profile.locator('[data-v2-edit]').click();check(await page.locator('#review-v2-comment').isVisible()&&await page.locator('dialog input[type="number"]').count()===0,'profile edit contains only a comment');await page.locator('#review-v2-comment').fill(profileFeedback);await page.locator('#commit').click();await page.locator('dialog').waitFor({state:'detached'});
 for(const [step,score,text] of [[3,2,'Başlangıç sorusundaki gerekçenizi vaka bilgileriyle daha açık destekleyebilirsiniz.'],[4,4,'Nihai kararınızı farklı öğrencilerin koşullarıyla daha açık ilişkilendirebilirsiniz.'],[6,2,'Yansıtmanızda kabul ettiğiniz öneriyi ve bunun kararınıza etkisini örnekleyebilirsiniz.']]){
   const card=review.locator(`[data-review-section="${step}"]`);
   await card.locator('[data-v2-accept]').click();await card.locator('[data-v2-status]').filter({hasText:'Kabul edildi'}).waitFor();await page.waitForFunction(()=>activeAnalysisSequence===null);
   await card.locator('[data-v2-edit]:enabled').waitFor();check(true,'accepted AI evaluation remains editable '+step);
   await review.locator(`[data-review-section="${step}"] [data-v2-edit]`).click();await selectQuestionScore(page,score);await page.locator('#review-v2-feedback-0').fill(text);await page.locator('#commit').click();await page.locator('dialog').waitFor({state:'detached'});
   await page.waitForFunction(()=>activeAnalysisSequence===null);await card.locator('[data-v2-edit]').click();
   check(Number(await page.locator('#review-v2-score-0').inputValue())===score&&await page.locator('#review-v2-feedback-0').inputValue()===text,'reopened editor retains latest researcher evaluation '+step);
   await page.locator('#dialog-close').click();await page.locator('dialog').waitFor({state:'detached'});
 }
 await page.waitForFunction(()=>!document.querySelector('[data-create-report]')?.disabled);await page.waitForFunction(()=>activeAnalysisSequence===null);check(true,'four decisions automatically merge without reviewing chat statistics');
 const before=await page.evaluate(()=>currentAnalysisView.steps.filter(s=>[2,3,4].includes(Number(s.step))&&s.run_id===currentAnalysisView.analysis_runs.at(-1).id).map(s=>s.id));
 const reflection=review.locator('[data-review-section="6"]');const callsBefore=calls.length;await reflection.locator('[data-v2-repeat]').click();await page.waitForFunction(()=>activeAnalysisSequence===null);await reflection.locator('[data-v2-status]').filter({hasText:'İncelemeniz bekleniyor'}).waitFor();
 check(calls.slice(callsBefore).join(',')==='6','reflection alone can be recalculated');
 const after=await page.evaluate(()=>currentAnalysisView.steps.filter(s=>[2,3,4].includes(Number(s.step))&&s.run_id===currentAnalysisView.analysis_runs.at(-1).id).map(s=>s.id));check(JSON.stringify(before)===JSON.stringify(after),'profile and other question scores unchanged');
 check(await review.locator('[data-create-report]').isDisabled(),'new reflection result needs another decision');
 await reflection.locator('[data-v2-edit]').click();await selectQuestionScore(page,2);await page.locator('#review-v2-feedback-0').fill('Yansıtmanızda karar değişiminizi somut bir konuşma örneğiyle açıklayabilirsiniz.');await page.locator('#commit').click();await page.locator('dialog').waitFor({state:'detached'});await page.waitForFunction(()=>!document.querySelector('[data-create-report]')?.disabled);await page.waitForFunction(()=>activeAnalysisSequence===null);
 for(const width of [1440,768,390]){await page.setViewportSize({width,height:1000});check(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth+1),`five-section review fits ${width}px`);await review.locator('[data-review-section="2"]').screenshot({path:path.join(tmp,`review-v2-profile-${width}.png`)});await review.locator('[data-review-section="3"]').screenshot({path:path.join(tmp,`review-v2-initial-${width}.png`)});}
 await page.setViewportSize({width:1440,height:1000});setMode('invalid_json');const created=page.waitForResponse(res=>res.url().includes('op=report')&&res.request().postDataJSON()?.action==='create');await review.locator('[data-create-report]').click();const reportId=(await (await created).json()).data.id;await page.locator(`[data-current-report="${reportId}"]`).first().waitFor();setMode('normal');
 const data=await page.evaluate(id=>api('report_data',undefined,{id,student_view:1}),reportId);
 check(data.report.format==='student-v2'&&Object.keys(data.report.sections).join(',')==='initial,final,reflection','new report has separately scored reflection');
 check(data.report.overall_feedback===profileFeedback&&data.report.sections.initial.criteria[0].score===2&&data.report.sections.final.criteria[0].score===4&&data.report.sections.reflection.criteria[0].score===2,'exact reviewed comments and question scores reach report without further AI generation');
 check(!('chat_statistics' in data.report)&&!('parts' in data.report)&&data.report.total===null,'report has no chat opinion, internal decisions or invented aggregate');
 check(await review.locator('.review-report button').allTextContents().then(labels=>labels.join('|')==='Öğrenci raporunu hazırla|Word|Excel|Öğrenciye göster'),'only four requested report buttons');
 check(!(await review.innerText()).includes('Önceki yorumlar')&&await page.locator('.legacy-review-history').count()===0,'no comment or evaluation history');
 check(await review.locator('[data-show-report]').isDisabled(),'show button explains collection must close first');
 for(const format of ['docx','xlsx']){const response=await context.request.get(url+`api.php?op=download&format=${format}&student_view=1&report_ids=${reportId}`),bytes=await response.body();check(response.status()===200&&(format==='pdf'?bytes.subarray(0,5).toString()==='%PDF-':bytes.subarray(0,2).toString()==='PK'),`student v2 ${format} downloads`);fs.writeFileSync(path.join(tmp,'review-v2-report.'+format),bytes);}
 const internal=await context.request.get(url+`api.php?op=download&format=docx&report_ids=${reportId}`);check(internal.status()===200,'internal review export remains available');
 const oldId=reportId;
 const overwritten=page.waitForResponse(res=>res.url().includes('op=report')&&res.request().postDataJSON()?.action==='create');await review.locator('[data-create-report]').click();check((await (await overwritten).json()).data.id===oldId,'prepare overwrites the same report');
 await page.waitForFunction(()=>!document.querySelector('[data-current-report]')?.disabled);
 const regenerated=page.waitForResponse(res=>res.url().includes('op=step')&&res.request().postDataJSON()?.repeat);await reflection.locator('[data-v2-repeat]').click();await regenerated;await page.waitForFunction(()=>activeAnalysisSequence===null);await reflection.locator('[data-v2-status]').filter({hasText:'İncelemeniz bekleniyor'}).waitFor();
 check(await review.locator('[data-current-report]').first().isDisabled(),'downloads wait for regenerated result review');
 await reflection.locator('[data-v2-edit]').click();await selectQuestionScore(page,4);await page.locator('#review-v2-feedback-0').fill('Yeni yansıtma yorumunuz kararınızı somut kanıtlarla açıklamanızı öneriyor.');await page.locator('#commit').click();await page.locator('dialog').waitFor({state:'detached'});await page.waitForFunction(()=>!document.querySelector('[data-current-report]')?.disabled);
 const replacement=await page.evaluate(id=>api('report_data',undefined,{id,student_view:1}),oldId);check(replacement.report.sections.reflection.criteria[0].score===4,'accepted recalculation automatically updates the same report');
 await page.evaluate(async()=>{await api('admin',{action:'collection',case_id:currentAnalysisView.application.case_id,closed:true,reason:'Synthetic current report browser fixture.'});await processView(currentAnalysisView.application.id);});
 await review.locator('[data-show-report]:enabled').waitFor();await review.locator('[data-show-report]').click();await review.locator('[data-show-report]').filter({hasText:'Öğrenciye gösteriliyor'}).waitFor();check(true,'single show button approves and publishes the current report');
 await reflection.locator('[data-v2-edit]').click();await selectQuestionScore(page,2);
 await page.locator('#review-v2-0-0-reason').fill('  ');await page.locator('#commit').click();
 check(await page.locator('dialog').isVisible(),'blank scoring reason keeps the editor open');
 await page.locator('#review-v2-0-0-reason').fill('Yanıt kısmi kanıt içerdiği için gelişiyor düzeyi seçildi.');
 await page.locator('#commit').click();await page.locator('dialog').waitFor({state:'detached'});await page.waitForFunction(()=>activeAnalysisSequence===null);await review.locator('[data-current-report]:enabled').first().waitFor();
 const editedAfterPublication=await page.evaluate(id=>api('report_data',undefined,{id,student_view:1}),oldId);
 check(editedAfterPublication.report.sections.reflection.criteria[0].score===2,'accepted evaluation can be edited again after report publication');
 await review.locator('[data-show-report]:enabled').waitFor();check(true,'editing withdraws prior publication and requires showing the updated report again');
 for(const width of [1440,390]){await page.setViewportSize({width,height:1000});check(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth+1),`four report actions fit ${width}px`);}
 await review.locator('.review-report').screenshot({path:path.join(tmp,'review-v2-current-report.png')});
 check(errors.length===0,'no browser JavaScript errors: '+errors.join('; '));console.log(`TOTAL V2 BROWSER ${count} checks passed. Isolated SQLite/mock; no physical-device or real-provider acceptance.`);
}finally{if(browser)await browser.close();server.kill();fs.closeSync(log);}})().catch(e=>{console.error(e);process.exitCode=1;});
