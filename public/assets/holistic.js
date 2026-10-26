'use strict';
const holisticDrafts=new Map();
function holisticState(v){
    const reports=[...v.reports].filter(r=>processObject(r.body).review_version===3).sort((a,b)=>Number(b.version)-Number(a.version));
    const report=reports[0],body=processObject(report?.body),saved=holisticDrafts.get(v.application.id),draft=saved&&(saved.dirty||Number(saved.version)>=Number(report?.version||0))?saved:undefined;
    const rubrics=dash.definitions.filter(d=>d.family==='rubric'&&!d.deleted_at&&!Number(d.archived)&&!dash.definitions.some(n=>n.parent_id===d.id));
    const rubricId=draft?.rubricId||body.rubric_id||dash.cases.find(c=>c.id===v.application.case_id)?.rubric_id||rubrics[0]?.id||'';
    const definition=dash.definitions.find(d=>d.id===rubricId),rubric=definition?processObject(definition.body):body.rubric;
    return {report,body,draft,rubrics,rubricId,rubric,questions:v.review_questions||body.questions||[]};
}
function holisticQuestionEditor(q,review,rubric,index){
    return `<article class="question-review" data-question-id="${esc(q.source_id)}"><p class="eyebrow">${esc(q.section)}</p><h3>${index+1}. ${esc(q.question)}</h3><h4>Öğrencinin yanıtı</h4><div class="prose question-answer">${esc(q.answer||'Yanıt kaydı yok.')}</div>
    ${field('holistic-feedback-'+index,'Bu yanıta geri bildirim',review?.feedback||'','textarea')}
    <h4>Bu yanıtın rubrik puanları</h4>${(rubric?.dimensions||[]).map((d,i)=>`${select('holistic-level-'+index+'-'+i,d.name,[['','Düzey seçin'],...d.levels.map(l=>[l.name,l.name+' · '+l.score+' puan'])],review?.scores?.[d.id]?.level||'')}${field('holistic-reason-'+index+'-'+i,'Puanın gerekçesi (isteğe bağlı)',review?.scores?.[d.id]?.reason||'','textarea')}`).join('')}
    <p class="review-total" id="holistic-question-total-${index}"></p></article>`;
}
function renderHolisticReview(v){
    const {report,body,draft,rubrics,rubricId,rubric,questions}=holisticState(v),id=v.application.id;
    const runs=new Set(v.analysis_runs.filter(r=>Number(r.review_version)===3).map(r=>r.id)),run=[...v.analysis_runs].reverse().find(r=>Number(r.review_version)===3);
    const pending=!!activeAnalysisSequence||v.steps.some(s=>runs.has(s.run_id)&&s.status==='running'),failed=v.steps.some(s=>s.run_id===run?.id&&s.status==='failed'),closed=!!v.application.collection_closed_at&&!Number(v.application.allow_late);
    const choices=[...rubrics];if(rubricId&&!choices.some(d=>d.id===rubricId)){const old=dash.definitions.find(d=>d.id===rubricId);if(old)choices.push(old);}
    const reviews=draft?.reviews||body.question_reviews||{},text=draft?.text??body.report_text??'',legacy=report&&body.report_layout!=='questions-v1';
    const profile=body.profile||holisticProfile(processObject(v.application.draft));
    return `<section class="review-workspace holistic-workspace" data-review-version="3" data-analysis-run="${esc(run?.id||'manual')}" data-holistic-app="${esc(id)}">
    <h3>Soru, yanıt, geri bildirim ve puan</h3><p>Her soruyu öğrencinin kendi yanıtıyla birlikte inceleyin. AI tüm soruları tek istekte değerlendirir ve tek rapor hazırlar. Geri bildirimleri ve puanları kendiniz de girebilir veya düzenleyebilirsiniz.</p>
    ${v.application.state!=='submitted'?'<p>Rapor hazırlamak için öğrenci tesliminin tamamlanması gerekiyor.</p>':''}
    ${legacy?'<p class="review-total">Kayıtlı rapor önceki genel değerlendirme düzeninde. Soru bazındaki alanlar henüz doldurulmadı. Yeni düzen için AI ile rapor hazırlayın veya her soruyu elle değerlendirin. Önceki rapor önizlemede korunur.</p>':''}
    <fieldset ${pending||v.application.state!=='submitted'?'disabled':''}>
    ${select('holistic-rubric','Değerlendirme rubriği',choices.map(d=>[d.id,d.name+' · v'+d.version]),rubricId)}
    <button id="holistic-generate" ${!rubric?'disabled':''}>${pending?'Rapor hazırlanıyor…':'AI ile rapor ve puanları hazırla'}</button>
    ${questions.map((q,i)=>holisticQuestionEditor(q,reviews[q.source_id],rubric,i)).join('')}
    ${field('holistic-report','Genel değerlendirme / ek not (isteğe bağlı)',text,'textarea')}
    <p id="holistic-total" class="review-total"></p><p class="muted">Her soru seçilen rubriğe göre puanlanır. Genel toplam, soruların ağırlıklı puanlarının toplamıdır.</p>
    <button id="holistic-save" ${!rubric?'disabled':''}>Raporu ve puanları taslak olarak kaydet</button>${draft?.dirty?'<button id="holistic-discard" class="secondary">Kaydedilmemiş düzenlemeleri bırak</button>':''}</fieldset>
    <p data-sequence-status role="status">${pending?'Rapor hazırlanıyor. Sonuç otomatik güncellenecek.':failed?'Son denemede rapor hazırlanamadı. Önceki kayıt ve elle yazdığınız taslak korunur.':draft?.dirty?'Kaydedilmemiş değişiklikler var.':report?'Rapor kaydedildi. '+(report.approved_at?'Araştırmacı onaylı.':'Araştırmacı onayı bekliyor.'):'Henüz rapor kaydedilmedi.'}</p>
    <div class="row"><button id="holistic-approve" ${!report||pending||draft?.dirty||report.approved_at?'disabled':''}>Raporu onayla</button><button id="holistic-show" ${!report?.approved_at||!closed||pending||draft?.dirty||report?.published_at?'disabled':''}>Öğrenciye göster</button><button id="holistic-hide" class="secondary" ${!report?.published_at||pending?'disabled':''}>Göstermeyi geri al</button></div>
    <p>${report?.published_at&&closed?'Öğrenciye açık':'Öğrenciye kapalı'}${!closed?' · Öğrenciye göstermek için bu vakada veri toplamayı kapatın.':''}</p>
    ${report?`<div class="row"><button class="secondary" data-student-report="${report.id}" ${pending||draft?.dirty?'disabled':''}>Öğrenci önizlemesi</button>${['docx','pdf','xlsx'].map(format=>`<button class="secondary" data-holistic-download="${format}" ${pending||draft?.dirty?'disabled':''}>${{docx:'Word',pdf:'PDF',xlsx:'Excel'}[format]}</button>`).join('')}</div>`:''}
    <h3>Başlangıç–nihai profil karşılaştırması</h3>${reviewProfileV2({profile,feedback:'Bu değerler öğrencinin öz bildirimidir; değişimler tek başına başarı veya neden göstermez.'})}</section>`;
}
function holisticProfile(d){return [['knowledge',null,'Ön bilgi'],['confidence','final_confidence','Güven'],['difficulty','final_difficulty','Algılanan zorluk'],['severity','final_severity','Algılanan ciddiyet']].map(([a,b,title])=>({title,initial:d[a]??null,final:b?d[b]??null:null,final_collected:!!b&&Object.hasOwn(d,b),change:b&&Number.isFinite(d[a])&&Number.isFinite(d[b])?d[b]-d[a]:null}));}
function holisticDirty(id){return !!holisticDrafts.get(id)?.dirty;}
function bindHolisticReview(v,id){
    if(!document.querySelector('[data-holistic-app]'))return;
    const state=holisticState(v),visible=()=>me?.role==='researcher'&&$('#process-data')?.dataset.appId===id;
    const remember=()=>{
        const reviews=Object.fromEntries(state.questions.map((q,index)=>[q.source_id,{feedback:getValue('holistic-feedback-'+index),scores:Object.fromEntries((state.rubric?.dimensions||[]).map((d,i)=>[d.id,{level:getValue('holistic-level-'+index+'-'+i),reason:getValue('holistic-reason-'+index+'-'+i)}]))}]));
        holisticDrafts.set(id,{rubricId:state.rubricId,text:getValue('holistic-report'),reviews,version:state.report?.version||0,dirty:true});
        for(const name of ['holistic-approve','holistic-show'])if($('#'+name))$('#'+name).disabled=true;
        document.querySelectorAll('[data-holistic-download],[data-student-report]').forEach(b=>b.disabled=true);const status=document.querySelector('[data-sequence-status]');if(status)status.textContent='Kaydedilmemiş değişiklikler var.';updateTotal();
    };
    const updateTotal=()=>{
        let total=0,max=0,all=true;state.questions.forEach((q,index)=>{let sum=0,limit=0,complete=true;
            for(const [i,d] of (state.rubric?.dimensions||[]).entries()){const l=d.levels.find(l=>l.name===getValue('holistic-level-'+index+'-'+i));if(!l)complete=false;else sum+=Number(l.score)*Number(d.weight);limit+=Number(d.max)*Number(d.weight);}
            $('#holistic-question-total-'+index).textContent=complete?`Soru puanı: ${Math.round(sum*10000)/10000} / ${Math.round(limit*10000)/10000}`:'Soru puanı için her ölçütün düzeyini seçin.';total+=sum;max+=limit;all=all&&complete;
        });$('#holistic-total').textContent=all?`Genel toplam: ${Math.round(total*10000)/10000} / ${Math.round(max*10000)/10000}`:'Genel toplam için tüm soruların ölçüt düzeylerini seçin.';
    };
    document.querySelectorAll('.holistic-workspace textarea,.holistic-workspace select:not(#holistic-rubric)').forEach(el=>el.addEventListener('input',remember));
    $('#holistic-rubric').onchange=()=>{remember();const draft=holisticDrafts.get(id);draft.rubricId=getValue('holistic-rubric');for(const item of Object.values(draft.reviews))item.scores={};processView(id,visible);};
    async function action(kind){
        const holisticRuns=new Set(currentAnalysisView.analysis_runs.filter(r=>Number(r.review_version)===3).map(r=>r.id));
        if(activeAnalysisSequence||currentAnalysisView.steps.some(s=>holisticRuns.has(s.run_id)&&s.status==='running'))return;
        if(kind==='save')remember();
        const draft=holisticDrafts.get(id),p={action:kind,app_id:id,rubric_id:state.rubricId,version:draft?.version??state.report?.version??0,report_id:state.report?.id};
        if(kind==='save'){p.report=draft.text;p.questions=draft.reviews;}
        const token={appId:id,runId:'holistic',mode:kind};activeAnalysisSequence=token;
        if(kind==='generate'&&draft)draft.dirty=false;
        try{await processView(id,visible);await api('holistic',p);holisticDrafts.delete(id);if(visible())notify(kind==='generate'?'Her sorunun geri bildirimi ve puanı hazır. İnceleyip düzenleyebilirsiniz.':'Rapor işlemi kaydedildi.');}
        catch(e){if(kind==='generate'&&draft&&e.status)draft.dirty=true;if(visible())notify(e.message);}
        finally{if(activeAnalysisSequence===token)activeAnalysisSequence=null;if(visible())try{await processView(id,visible);}catch(e){notify(e.message);}}
    }
    for(const kind of ['generate','save','approve','show','hide'])handler('#holistic-'+kind,()=>action(kind));
    handler('#holistic-discard',()=>{holisticDrafts.delete(id);processView(id,visible);});
    document.querySelectorAll('[data-holistic-download]').forEach(b=>b.onclick=()=>download({report_ids:state.report.id,format:b.dataset.holisticDownload,student_view:1}));updateTotal();
}
function holisticStudentHtml(r){
    const d=r.report,perQuestion=d.report_layout==='questions-v1';
    const sections=Object.values(d.sections).map(s=>`<section class="question-review">${perQuestion?`<p class="eyebrow">${esc(s.section)}</p><h2>${esc(s.title)}</h2><h3>Öğrencinin yanıtı</h3><div class="prose question-answer">${esc(s.answer)}</div><h3>Geri bildirim</h3><div class="prose">${esc(s.feedback)}</div><p class="review-total">Soru puanı: ${esc(s.total)} / ${esc(s.max)}</p>`:''}<h3>Rubrik puanları ve gerekçeleri</h3>${s.criteria.map(c=>`<h4>${esc(c.name)} · ${esc(c.score)} / ${esc(c.max)}</h4><p>${esc(c.level)} · Ağırlık ${esc(c.weight)}</p><p class="prose">${esc(c.feedback)}</p>`).join('')}</section>`).join('');
    return `<article class="student-report"><h1>Değerlendirme raporu</h1><p>${esc(d.case_title)} · ${esc(d.participant)}</p><p>Rubrik: ${esc(d.rubric_name)}</p><p>${r.approved_at?'Araştırmacı onaylı':'İnceleme taslağı'}</p>${sections}${d.overall_feedback?`<h2>Genel değerlendirme</h2><div class="prose">${esc(d.overall_feedback)}</div>`:''}<p><b>Genel toplam: ${esc(d.total)} / ${esc(d.max)}</b></p><p>Her soru rubrik ağırlıklarıyla puanlanır. Genel toplam soru puanlarının toplamıdır.</p><h2>Başlangıç–nihai profil karşılaştırması</h2>${reviewProfileV2({profile:d.profile,feedback:'Profil değerleri öz bildirimdir.'})}<div class="row"><button data-student-download="docx">Word indir</button><button data-student-download="pdf">PDF indir</button></div></article>`;
}