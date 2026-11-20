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
 await page.locator('[data-admin-tab="participants"]').click();await page.locator('#add-participant').click();
 check(await page.locator('#p-role').isVisible(),'user creation includes student/researcher choice');
 check(await page.locator('#p-consent,#p-experience,#p-tools,#p-withdrawal,#reason').count()===0,'simple account form omits removed fields and reason');
 const newlogin='RESEARCHER-UI-'+tag.slice(0,8);await page.locator('#p-role').selectOption('researcher');await page.locator('#p-login').fill(newlogin);await page.locator('#p-name').fill('SENTETİK yeni araştırmacı');await page.locator('#p-password').fill(password);await page.locator('#commit').click();await page.locator('dialog').waitFor({state:'detached'});
 check(await page.getByText(newlogin,{exact:true}).count()===1,'researcher account created via short form');
 const otherContext=await browser.newContext(),other=await otherContext.newPage();await login(other,newlogin);await other.locator('#overview-case').waitFor();
 check(await other.locator('[data-overview-row]').count()===0,'new researcher has no other researcher students');
 await other.locator('[data-admin-tab="cases"]').click();check(await other.locator('[data-edit-case]').count()===0,'new researcher has no inherited cases');await otherContext.close();
 await page.locator('[data-admin-tab="overview"]').click();await page.locator('#overview-case').waitFor();
 await page.locator('#overview-case').selectOption('test-case-2');await page.locator('#overview-search').fill('TEST-OGR-05');
 check(await page.locator('[data-overview-row]').count()===1,'overview finds one student within selected case');
 const processButton=page.locator('[data-overview-row] [data-process]');
 // TEST-OGR-05 has not started case 2 in this fixture: no misleading open action.
 check(await processButton.count()===0,'unstarted student has no misleading process-open button');
 await page.locator('#overview-case').selectOption('test-case-1');await page.locator('[data-overview-row] [data-process]').click();await page.locator('#admin-back').click();await page.locator('#overview-case').waitFor();
 check(await page.locator('#overview-case').inputValue()==='test-case-1'&&await page.locator('#overview-search').inputValue()==='TEST-OGR-05','return from process preserves case and student selection');
 await page.locator('#overview-search').fill('');await page.locator('#overview-status').selectOption('submitted');check(await page.locator('[data-overview-row]').count()===1,'overview status filter works within case');await page.locator('#overview-status').selectOption('');
 await page.screenshot({path:path.join(tmp,'researcher-021-overview.png'),fullPage:true});await page.setViewportSize({width:390,height:844});check(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth+1),'selected-case student list fits mobile');await page.setViewportSize({width:1440,height:1000});
 await page.locator('[data-admin-tab="cases"]').click();await page.locator('[data-assigned="test-case-1"]').click();await page.getByRole('heading',{name:'Atanan öğrenciler',exact:true}).waitFor();
 check(await page.locator('[data-assigned-student]').count()===7,'case card displays all and only assigned students');await page.locator('#manage-case-assignments').click();await page.locator('#a-case').waitFor();check(await page.locator('#a-case').inputValue()==='test-case-1','assigned list opens bulk management with correct case selected');await page.locator('#dialog-close').click();await page.locator('dialog').waitFor({state:'detached'});
 await page.locator('#assignment').click();
 check(await page.locator('[data-assign-student]').count()===7,'bulk assignment lists all owned students as checkboxes');
 await page.locator('#select-all-students').check();check(await page.locator('[data-assign-student]:checked').count()===7,'select all selects every student');
 await page.locator('[data-assign-student]').first().uncheck();check(await page.locator('#select-all-students').evaluate(n=>n.indeterminate),'select all indicates partial selection');
 await page.locator('#select-all-students').check();await page.locator('#a-case').selectOption('test-case-5');await page.locator('#commit').click();await page.locator('dialog').waitFor({state:'detached'});
 await page.locator('#assignment').click();await page.locator('#a-case').selectOption('test-case-5');check(await page.locator('[data-assignment-status]').evaluateAll(nodes=>nodes.every(n=>n.textContent.includes('Atanmış'))),'bulk assignment persists for all students');await page.locator('#dialog-close').click();
 const controlContext=await browser.newContext(),control=await controlContext.newPage();await login(control,'TEST-OGR-04');await control.locator('[data-case="test-case-1"]').click();await control.locator('[data-section="final"]').click();
 const studentContext=await browser.newContext(),student=await studentContext.newPage();student.on('pageerror',e=>errors.push(e.message));await login(student,'TEST-OGR-05');await student.locator('[data-case="test-case-1"]').click();await student.locator('[data-section="final"]').click();
 check(await student.locator('#final_decision').isEnabled(),'working student initially editable');
 page.once('dialog',d=>d.accept());await page.locator('[data-collection="test-case-1"]').click();await page.locator('[data-collection="test-case-1"]').filter({hasText:'Veri toplamayı aç'}).waitFor();
 await student.reload();await student.locator('[data-case="test-case-1"]').click();await student.locator('[data-section="final"]').click();check(await student.locator('#final_decision').isDisabled(),'case closure makes student fields read only');check(await student.getByText('Bu vakada veri toplama kapalı.',{exact:false}).count()===1,'student sees closure explanation');
 await page.locator('#assignment').click();await page.locator('#a-case').selectOption('test-case-1');await page.locator('#a-active').selectOption('late-open');await page.locator('[data-assign-student][value="test-student-5"]').check();await page.locator('#commit').click();await page.locator('dialog').waitFor({state:'detached'});
 await student.reload();await student.locator('[data-case="test-case-1"]').click();await student.locator('[data-section="final"]').click();check(await student.locator('#final_decision').isEnabled(),'selected late access restores working fields');
 await control.reload();await control.locator('[data-case="test-case-1"]').click();await control.locator('[data-section="final"]').click();check(await control.locator('#final_decision').isDisabled(),'unselected student stays closed while selected student receives late access');await controlContext.close();
 await page.locator('#assignment').click();await page.locator('#a-case').selectOption('test-case-1');await page.locator('#a-active').selectOption('late-close');await page.locator('[data-assign-student][value="test-student-5"]').check();await page.locator('#commit').click();await page.locator('dialog').waitFor({state:'detached'});
 await student.reload();await student.locator('[data-case="test-case-1"]').click();await student.locator('[data-section="final"]').click();check(await student.locator('#final_decision').isDisabled(),'revoking late access returns fields to read only');
 page.once('dialog',d=>d.accept());await page.locator('[data-collection="test-case-1"]').click();await page.locator('[data-collection="test-case-1"]').filter({hasText:'Veri toplamayı kapat'}).waitFor();
 await page.screenshot({path:path.join(tmp,'researcher-simple-cases.png'),fullPage:true});
 await page.locator('[data-admin-tab="definitions"]').click();await page.locator('#add-rubric').click();
 check(await page.locator('#r-text').count()===1&&await page.locator('#d-body').count()===0,'rubric creation uses guided fields instead of JSON');
 await page.locator('#commit').click();await page.locator('#dialog-error').waitFor({state:'visible'});check(await page.locator('#d-name').evaluate(n=>n===document.activeElement),'missing rubric name directs focus to field');
 await page.locator('#d-name').fill('SENTETİK FORM RUBRİĞİ');await page.locator('#r-text').fill('SENTETİK: Kanıt ve farklı bakış açılarını değerlendirin.');
 const dim=page.locator('[data-rubric-dimension]').first(),levels=dim.locator('[data-rubric-level]');await dim.locator('[data-rubric-field="name"]').fill('Gerekçe');await dim.locator('[data-rubric-field="weight"]').fill('2');
 await levels.nth(0).locator('[data-rubric-field="level-name"]').fill('Başlangıç');await levels.nth(0).locator('[data-rubric-field="description"]').fill('Kanıt henüz sunulmamış.');await levels.nth(1).locator('[data-rubric-field="level-name"]').fill('Yetkin');await levels.nth(1).locator('[data-rubric-field="description"]').fill('Açık ve ilgili kanıtlar var.');
 await dim.locator('[data-add-level]').click();await levels.nth(2).locator('[data-rubric-field="level-name"]').fill('Ara');await levels.nth(2).locator('[data-rubric-field="score"]').fill('2');await levels.nth(2).locator('[data-rubric-field="description"]').fill('Kısmi kanıt.');check(await levels.count()===3,'rubric level can be added');await levels.nth(2).locator('[data-remove-level]').click();check(await levels.count()===2&&await levels.nth(0).locator('[data-rubric-field="level-name"]').inputValue()==='Başlangıç','removing level preserves other level data');
 await page.locator('#add-dimension').click();const dim2=page.locator('[data-rubric-dimension]').nth(1);await dim2.locator('[data-rubric-field="name"]').fill('Bakış açıları');await dim2.locator('[data-rubric-field="max"]').fill('2');const l2=dim2.locator('[data-rubric-level]');await l2.nth(0).locator('[data-rubric-field="level-name"]').fill('Tek bakış');await l2.nth(0).locator('[data-rubric-field="description"]').fill('Tek taraf ele alınmış.');await l2.nth(1).locator('[data-rubric-field="level-name"]').fill('Çoklu bakış');await l2.nth(1).locator('[data-rubric-field="description"]').fill('Farklı taraflar ele alınmış.');
 await page.locator('#commit').click();await page.locator('#dialog-error').waitFor({state:'visible'});check((await page.locator('#dialog-error').innerText()).includes('aralığı')&&await l2.nth(1).locator('[data-rubric-field="score"]').evaluate(n=>n===document.activeElement),'out-of-range level score has readable error and field focus');await l2.nth(1).locator('[data-rubric-field="score"]').fill('2');
 await page.locator('#add-dimension').click();await page.locator('[data-rubric-dimension]').nth(2).locator('[data-remove-dimension]').click();check(await page.locator('[data-rubric-dimension]').count()===2&&await dim.locator('[data-rubric-field="name"]').inputValue()==='Gerekçe','removing dimension preserves existing input');
 await page.setViewportSize({width:390,height:844});check(await page.locator('dialog').evaluate(n=>n.scrollWidth<=n.clientWidth+1),'guided rubric form fits mobile dialog');await page.setViewportSize({width:1440,height:1000});await page.screenshot({path:path.join(tmp,'researcher-021-rubric.png'),fullPage:true});
 await page.locator('#commit').click();await page.locator('dialog').waitFor({state:'detached'});const oldRubric=await page.evaluate(()=>dash.definitions.find(d=>d.name==='SENTETİK FORM RUBRİĞİ'));const rubricBody=JSON.parse(oldRubric.body);
 check(rubricBody.dimensions.length===2&&rubricBody.dimensions[0].weight===2&&rubricBody.dimensions[1].levels[1].score===2,'system builds and saves complete rubric JSON from fields');
 const formCard=page.locator('.card').filter({has:page.getByRole('heading',{name:'SENTETİK FORM RUBRİĞİ',exact:false})});await formCard.locator('summary').click();check((await formCard.innerText()).includes('Çoklu bakış')&&await formCard.locator('pre').count()===0,'saved rubric displays readable dimensions and levels');await formCard.locator('[data-edit-def]').click();check(await page.locator('[data-rubric-dimension]').count()===2&&await page.locator('[data-rubric-field="name"]').first().inputValue()==='Gerekçe','rubric editor restores saved field values');
 await page.locator('[data-rubric-field="name"]').first().fill('Gerekçelendirme');await page.locator('#commit').click();await page.locator('dialog').waitFor({state:'detached'});const versioned=await page.evaluate(()=>dash.definitions.filter(d=>d.name==='SENTETİK FORM RUBRİĞİ'));const latest=versioned.find(d=>d.parent_id===oldRubric.id);
 check(versioned.find(d=>d.id===oldRubric.id).body===oldRubric.body&&JSON.parse(latest.body).dimensions[0].id===rubricBody.dimensions[0].id,'rubric editing preserves old version and stable dimension identity');
 await page.locator('#add-label').click();check(await page.locator('#reason').count()===0,'new label needs no reason');await page.locator('#d-name').fill('SENTETİK SİL');await page.locator('#d-body').fill('Kısa açıklama');await page.locator('#d-target-role').selectOption('user');await page.locator('#commit').click();await page.locator('dialog').waitFor({state:'detached'});
 const card=page.locator('.card').filter({has:page.getByRole('heading',{name:'SENTETİK SİL',exact:false})});check(await card.count()===1,'label description entered as plain text');page.once('dialog',d=>d.accept());await card.locator('[data-delete-def]').click();await card.waitFor({state:'detached'});check(await page.getByRole('heading',{name:'SENTETİK SİL',exact:false}).count()===0,'deleted label leaves active list');
 const rubric=page.locator('.card').filter({has:page.getByRole('heading',{name:'TEST RUBRİĞİ',exact:false})});page.once('dialog',d=>d.accept());await rubric.locator('[data-delete-def]').click();await rubric.waitFor({state:'detached'});check(await page.getByRole('heading',{name:'TEST RUBRİĞİ',exact:false}).count()===0,'used rubric removable from active list');
 await page.locator('[data-admin-tab="exports"]').click();await page.locator('#export-case').waitFor();check(await page.locator('#export-case option').count()===7,'export selects all or single owned case');
 const fullDisco=page.locator('#disco-all-data');check(await page.locator('#admin-body input[type="checkbox"],#csv-table').count()===1&&!(await fullDisco.isChecked()),'Disco offers an unchecked option to include non-research attempts');
 await page.locator('#export-case').selectOption('test-case-1');const disco=page.locator('[data-export="disco"]');
 const [download]=await Promise.all([page.waitForEvent('download'),disco.click()]);await download.saveAs(path.join(tmp,'ui-disco.csv'));
 const defaultCsv=fs.readFileSync(path.join(tmp,'ui-disco.csv'),'utf8');check(defaultCsv.includes('Start Timestamp')&&defaultCsv.trim().split(/\r?\n/).length===1,'default Disco download excludes synthetic cases');
 await fullDisco.check();const [complete]=await Promise.all([page.waitForEvent('download'),disco.click()]);await complete.saveAs(path.join(tmp,'ui-disco-all.csv'));
 const fullCsv=fs.readFileSync(path.join(tmp,'ui-disco-all.csv'),'utf8');check(fullCsv.includes('Excluded')&&fullCsv.includes('test-case-1'),'explicit full Disco download includes the case with cohort flags');
 await page.screenshot({path:path.join(tmp,'researcher-simple-export.png'),fullPage:true});await page.setViewportSize({width:390,height:844});check(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth+1),'simplified export fits mobile viewport');await page.screenshot({path:path.join(tmp,'researcher-simple-export-mobile.png'),fullPage:true});
 await page.setViewportSize({width:1440,height:1000});await page.locator('[data-admin-tab="system"]').click();await page.locator('#admin-body').getByRole('heading',{name:'İşlem geçmişi'}).waitFor();check(await page.locator('#close-study').count()===0,'global closure button removed');
 check(await page.locator('#audit-list .audit-entry').count()===20,'audit initially shows twenty readable records');await page.locator('#more-audit').click();check(await page.locator('.audit-entry').count()>20,'audit can reveal older records without losing history');
 check(await page.locator('#audit-list .audit-entry').count()>0&&await page.locator('#audit-list pre').count()===0,'audit history is readable records instead of raw JSON');check((await page.locator('#audit-list').innerText()).includes('Öğrenciye ek süre verildi'),'audit translates late access operation into Turkish');
 await page.locator('#audit-search').fill('SENTETİK SİL');check(await page.locator('.audit-entry').count()>0&&(await page.locator('#audit-list').innerText()).includes('Etiket: SENTETİK SİL'),'audit search resolves deleted definition names');await page.locator('#audit-search').fill('');
 const [auditDownload]=await Promise.all([page.waitForEvent('download'),page.locator('#download-audit').click()]);await auditDownload.saveAs(path.join(tmp,'ui-audit.json'));const auditJson=JSON.parse(fs.readFileSync(path.join(tmp,'ui-audit.json'),'utf8'));check(Array.isArray(auditJson)&&auditJson.length===await page.evaluate(()=>dash.audit.length),'audit JSON download contains complete scoped recent history');await page.screenshot({path:path.join(tmp,'researcher-021-audit.png')});
 await page.setViewportSize({width:390,height:844});check(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth+1),'readable audit history fits mobile');

 await page.locator('[data-admin-tab="cases"]').click();
 await page.locator('[data-edit-case="test-case-1"]').click();
 check((await page.locator('#c-body').innerText()).includes('[SENTETİK VAKA]'),'existing plain-text cases open in the editor');
 await page.locator('#dialog-close').click();
 await page.locator('dialog').waitFor({state:'detached'});
 await page.locator('#add-case').click();
 const editor=page.getByRole('textbox',{name:'Vaka metni',exact:true});
 check(await editor.isEditable()&&await page.locator('textarea#c-instructions').isEditable(),'visual editor is confined to the case body');
 check(await page.locator('.case-editor').evaluate(e=>e.getBoundingClientRect().right<=innerWidth),'formatting toolbar fits the mobile dialog');
 await page.setViewportSize({width:1440,height:1000});
 const richCaseTitle='SENTETİK biçimlendirilmiş vaka';
 await page.locator('#c-title').fill(richCaseTitle);
 await page.locator('#commit').click();
 await page.locator('#dialog-error').waitFor({state:'visible'});
 check((await page.locator('#dialog-error').innerText()).includes('Başlık ve metin gerekli'),'an empty editor cannot save a case');
 await editor.fill('Önemli Gerekçe Kanıt');
 const selectText=async(label)=>editor.evaluate((element,label)=>{
    element.focus();
    const texts=document.createTreeWalker(element,NodeFilter.SHOW_TEXT);
    for(let node=texts.nextNode();node;node=texts.nextNode()){
        const index=node.data.indexOf(label);
        if(index<0)continue;
        const range=document.createRange();
        range.setStart(node,index);
        range.setEnd(node,index+label.length);
        const selection=window.getSelection();
        selection.removeAllRanges();
        selection.addRange(range);
        return;
    }
    throw new Error('Selectable text missing: '+label);
 },label);
 await selectText('Önemli');
 await page.getByRole('button',{name:'Kalın',exact:true}).click();
 await selectText('Gerekçe');
 await page.getByRole('button',{name:'İtalik',exact:true}).click();
 await selectText('Kanıt');
 const underline=page.getByRole('button',{name:'Altı çizili',exact:true});
 await underline.click();
 check(await editor.locator('u').innerText()==='Kanıt'&&await underline.getAttribute('aria-pressed')==='true','underline is visible and the toolbar reports it as active');
 await underline.click();
 check(await editor.locator('u').count()===0,'pressing underline again removes it from the selection');
 await underline.click();
 await page.getByRole('button',{name:'Madde işaretli liste',exact:true}).click();
 check(await editor.locator('ul li').count()===1&&await editor.locator('b,strong').innerText()==='Önemli'&&
    await editor.locator('i,em').innerText()==='Gerekçe','bold, italic and list are visible while editing');
 await page.locator('#c-status').selectOption('active');
 await page.locator('#c-instructions').fill('SENTETİK yönerge');
 await page.locator('#commit').click();
 await page.locator('dialog').waitFor({state:'detached'});
 const richCase=await page.evaluate(title=>dash.cases.find(c=>c.title===title),richCaseTitle);
 check(richCase.body==='Önemli Gerekçe Kanıt'&&richCase.body_html.includes('<strong>Önemli</strong>')&&richCase.body_html.includes('<u>Kanıt</u>'),'stored version retains formatted markup and the plain text used by analysis');
 await page.locator('[data-preview="'+richCase.id+'"]').click();
 check(await page.locator('dialog .case-body ul li em').innerText()==='Gerekçe'&&
    await page.locator('dialog .case-body ul li u').innerText()==='Kanıt','researcher preview renders the saved formatting');
 await page.locator('#dialog-close').click();
 await page.locator('dialog').waitFor({state:'detached'});
 await page.locator('[data-edit-case="'+richCase.id+'"]').click();
 check(await editor.locator('ul li strong').innerText()==='Önemli'&&
    await editor.locator('ul li u').innerText()==='Kanıt','reopening the editor restores the saved formatting');
 await page.locator('#dialog-close').click();
 await page.locator('dialog').waitFor({state:'detached'});
 await page.evaluate(cid=>api('admin',{action:'assign',case_id:cid,user_id:'test-student-5',active:true}),richCase.id);
 await student.reload();
 await student.locator('[data-case="'+richCase.id+'"]').click();
 await student.locator('[data-section="case"]').click();
 check(await student.locator('[data-source="case"] ul li strong').innerText()==='Önemli'&&
    await student.locator('[data-source="case"] ul li em').innerText()==='Gerekçe'&&
    await student.locator('[data-source="case"] ul li u').evaluate(el=>el.textContent==='Kanıt'&&getComputedStyle(el).textDecorationLine.includes('underline')),
    'student sees the same bold, italic, underline and list in the assigned case');
 const safeCase=await page.evaluate(()=>api('admin',{action:'case',title:'SENTETİK izinli biçim',body_html:'<div><strong class="sample">Metin</strong> <a href="#example">örnek</a></div>'}));
 const safeVersion=await page.evaluate(async id=>(await api('dashboard')).cases.find(c=>c.id===id),safeCase.id);
 check(safeVersion.body_html==='<div><strong>Metin</strong> örnek</div>'&&safeVersion.body==='Metin örnek',
    'server allows only supported markup and strips attributes');

 check(errors.length===0,'new workflows have no JavaScript page errors: '+errors.join('; '));await studentContext.close();console.log(`TOTAL NEW UI ${count} checks passed. Isolated SQLite/mock, Chrome desktop/mobile emulation.`);
}finally{if(browser)await browser.close();if(server)server.kill();fs.closeSync(log);}})().catch(e=>{console.error(e);process.exitCode=1;});
