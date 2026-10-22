'use strict';
const reviewSections=[{step:2,title:'1. Vaka profilindeki değişim',kind:'profile'},{step:3,title:'2. Başlangıç değerlendirmesi',kind:'questions'},{step:5,title:'3. AI sohbeti',kind:'statistics'},{step:4,title:'4. Nihai değerlendirme',kind:'questions'},{step:6,title:'5. Yansıtma',kind:'questions'}];
function analysisStepLabel(runId,step){return Number(currentAnalysisView?.analysis_runs.find(r=>r.id===runId)?.review_version)===2?(reviewSections.find(s=>s.step===step)?.title.replace(/^\d+\. /,'')||'Değerlendirme'):stepNames[step];}
function renderReviewV2(run,v){
    const progress=analysisProgress(run.id,v),byStep=n=>progress.find(p=>p.n===n),human=[2,3,4,6];
    const latestDecision=s=>s&&v.decisions.filter(d=>d.step_id===s.id).at(-1);
    const ready=reviewSections.filter(section=>byStep(section.step)?.done).length,decided=human.filter(n=>latestDecision(byStep(n)?.done)).length;
    const busy=!!activeAnalysisSequence||progress.some(p=>p.status==='running'),generated=progress.filter(p=>p.n!==8).every(p=>p.done),merge=byStep(8)?.done;
    return `<section class="review-workspace" data-analysis-run="${run.id}" data-review-version="2"><p><b>${ready} / 5 bölüm hazır</b> · ${decided} / 4 bölüm kararı kaydedildi</p><button data-start-analysis="${run.id}" ${busy||generated?'disabled':''} ${generated&&!busy?'hidden':''}>${busy?'Hazırlanıyor…':ready?'Hazırlamaya devam et':'Değerlendirmeyi başlat'}</button><p data-sequence-status role="status" aria-live="polite">${busy?'Bölümler sırayla hazırlanıyor.':generated?'Yorum ve puanları inceleyin; kabul edin, düzenleyin veya reddedin.':'Bir kez başlatın; beş bölüm sırayla hazırlansın.'}</p><div class="review-cards">${reviewSections.map(section=>{
        const p=byStep(section.step),s=p?.done,decision=latestDecision(s),approved=decision&&['accept','edit'].includes(decision.action),out=processObject(approved?decision.body:s?.output),status=p?.status==='running'?'Hazırlanıyor':!s?'Henüz hazır değil':section.kind==='statistics'?'İstatistik':decision?.action==='reject'?'Reddedildi':approved?'Kabul edildi':'İncelemeniz bekleniyor';
        return `<section class="review-card" data-review-section="${section.step}"><div class="review-card-heading"><h3>${section.title}</h3><span class="pill" data-v2-status>${status}</span></div>${p?.latest?.status==='failed'?`<p class="status error">${s?'Yeniden hazırlanamadı; önceki sonuç korundu.':'Bu bölüm hazırlanamadı. Hazırlamaya devam et ile yeniden deneyebilirsiniz.'}</p>`:''}${!s?'<p>Hazır olduğunda burada görünecek.</p>':section.kind==='profile'?reviewProfileV2(out):section.kind==='statistics'?reviewStatisticsV2(out.statistics):reviewQuestionsV2(out,processObject(s.input))}${s&&section.kind!=='statistics'?`<div class="review-actions"><button data-v2-accept="${s.id}" ${busy||approved?'disabled':''}>${approved?'Kabul edildi':'Kabul et'}</button><button class="secondary" data-v2-edit="${s.id}" ${busy?'disabled':''}>Düzenle</button><button class="secondary" data-v2-reject="${s.id}" ${busy||decision?.action==='reject'?'disabled':''}>Reddet</button><button class="secondary" data-v2-repeat="${s.id}" ${busy?'disabled':''}>${section.kind==='profile'?'Yeniden yazdır':'Yeniden hesaplat'}</button></div>`:''}</section>`;
    }).join('')}</div>${reviewReportActions(run,v,{merge,busy,decided})}</section>`;
}
function reviewProfileV2(out){
    const value=n=>n===null||n===undefined?'Kayıt yok':esc(n);
    return `<div class="table-wrap"><table class="review-profile-table"><thead><tr><th>Profil bilgisi</th><th>Başlangıç</th><th>Nihai</th><th>Değişim</th></tr></thead><tbody>${(out.profile||[]).map(row=>`<tr><th>${esc(row.title)}</th><td>${value(row.initial)}</td><td>${row.final_collected?value(row.final):'Bu aşamada sorulmadı'}</td><td>${row.change===null?'Karşılaştırılamıyor':row.change>0?'+'+esc(row.change):esc(row.change)}</td></tr>`).join('')}</tbody></table></div><h4>Profil değişimine ilişkin yorum</h4><p class="prose" data-profile-feedback>${esc(out.feedback)}</p>`;
}
function reviewStatisticsV2(stats){
    if(!stats)return '<p>İstatistikler henüz hazır değil.</p>';
    return `<div class="process-summary-grid" data-chat-statistics><div class="process-summary-item"><h4>Öğrencinin gönderdiği mesaj</h4><strong>${stats.student_messages}</strong></div><div class="process-summary-item"><h4>Alınan yanıt</h4><strong>${stats.assistant_messages}</strong></div><div class="process-summary-item"><h4>Sohbet bölümünde geçirilen süre</h4><strong>${processDuration(stats.seconds)}</strong></div></div>${stats.unknown_intervals?'<p class="muted">Bazı süre kayıtları eksik; gösterilen süre eksik olabilir.</p>':''}<p class="muted">Süre, sohbet bölümü uygulama içinde açıkken kaydedilen zamandır.</p>`;
}
function reviewQuestionsV2(out,payload){
    return `<div class="review-questions">${Object.entries(out.questions||{}).map(([id,q])=>`<article class="review-question" data-question-id="${id}"><div class="review-criterion-heading"><h4>${esc(q.title)}</h4><strong data-question-score>${esc(q.score)} / ${esc(q.max)}</strong></div><details><summary>Öğrencinin yanıtını göster</summary><div class="prose">${esc(payload.questions?.[id]?.answer||'Yanıt kaydı bulunamadı.')}</div></details><h5>Puanın gerekçesi ve gelişim önerisi</h5><p class="prose" data-question-feedback>${esc(q.feedback)}</p></article>`).join('')}</div>`;
}
function bindReviewV2(v,appId){
    document.querySelectorAll('[data-v2-accept]').forEach(b=>b.onclick=()=>acceptReviewStep(v.steps.find(s=>s.id===b.dataset.v2Accept),appId));
    document.querySelectorAll('[data-v2-edit]').forEach(b=>b.onclick=()=>editReviewV2(v.steps.find(s=>s.id===b.dataset.v2Edit),v,appId));
    document.querySelectorAll('[data-v2-repeat]').forEach(b=>b.onclick=()=>recalculateReviewStep(v.steps.find(s=>s.id===b.dataset.v2Repeat),appId));
    document.querySelectorAll('[data-v2-reject]').forEach(b=>b.onclick=async()=>{
        if(activeAnalysisSequence)return;b.disabled=true;const s=v.steps.find(s=>s.id===b.dataset.v2Reject);
        try{await api('decision',{step_id:s.id,action:'reject',reason:'Araştırmacı bu bölüm için Reddet düğmesini kullandı.'});await finishReview(s.run_id,appId);}catch(e){notify(e.message);b.disabled=false;}
    });
}
function editReviewV2(s,v,appId){
    const decision=v.decisions.filter(d=>d.step_id===s.id).at(-1),out=processObject(decision&&['accept','edit'].includes(decision.action)?decision.body:s.output),profile=Number(s.step)===2;
    const questions=Object.entries(out.questions||{});
    const dimensions=q=>q.input.rubric.dimensions;
    const dialog=showDialog(`<h2>${reviewSections.find(section=>section.step===Number(s.step)).title} · Düzenle</h2>${profile?field('review-v2-comment','Profil değişimine ilişkin yorum',out.feedback,'textarea'):questions.map(([id,q],i)=>`<fieldset class="review-question"><legend>${esc(q.title)}</legend>${field('review-v2-score-'+i,'Rubrikten hesaplanan puan',q.score,'number')}${dimensions(q).map((d,j)=>{const c=q.criteria[d.id],prefix='review-v2-'+i+'-'+j;return `<fieldset><legend>${esc(d.name)}</legend>${select(prefix+'-level','Düzey',d.levels.map(l=>[l.name,l.name+' ('+l.score+' puan)']),c.level)}${field(prefix+'-reason','Puanlama gerekçesi (zorunlu)',c.reason,'textarea')}<details><summary>Dayanak pasajını düzenle</summary>${select(prefix+'-source','Dayanak yanıt',Object.keys(q.input.sources).map(k=>[k,q.title]),c.source)}${field(prefix+'-quote','Öğrenci yanıtındaki dayanak pasaj',c.quote,'textarea')}</details></fieldset>`;}).join('')}${field('review-v2-feedback-'+i,'Puanın gerekçesi ve gelişim önerisi',q.feedback,'textarea')}</fieldset>`).join('')}<button id="commit" class="spaced">Kaydet ve kabul et</button>`,async()=>{
        const body=profile?{feedback:getValue('review-v2-comment')}:{questions:Object.fromEntries(questions.map(([id,q],i)=>{
            const criteria=structuredClone(q.criteria);
            dimensions(q).forEach((d,j)=>{const prefix='review-v2-'+i+'-'+j,level=d.levels.find(l=>l.name===getValue(prefix+'-level')),reason=getValue(prefix+'-reason');demandUi(reason.trim()!=='','Puanlama gerekçesini doldurun.');criteria[d.id]={...criteria[d.id],level:level.name,score:Number(level.score),reason,source:getValue(prefix+'-source'),quote:getValue(prefix+'-quote')};});
            return [id,{criteria,feedback:getValue('review-v2-feedback-'+i)}];
        }))};
        await api('decision',{step_id:s.id,action:'edit',body,reason:'Araştırmacı bu bölümün yorumunu veya puanını düzenleyerek kabul etti.'});document.querySelector('dialog #dialog-close')?.click();await finishReview(s.run_id,appId);
    });
    questions.forEach(([,q],i)=>{
        const input=dialog.querySelector('#review-v2-score-'+i);input.readOnly=true;input.step='any';
        const update=()=>{let sum=0,weight=0;dimensions(q).forEach((d,j)=>{const level=d.levels.find(l=>l.name===getValue('review-v2-'+i+'-'+j+'-level'));sum+=Number(level.score)*Number(d.weight);weight+=Number(d.weight);});input.value=Math.round(sum/weight*10000)/10000;};
        dimensions(q).forEach((d,j)=>dialog.querySelector('#review-v2-'+i+'-'+j+'-level').onchange=update);update();
    });
}

function reviewReportActions(run,v,{merge,busy,decided}){
    const report=[...v.reports].sort((a,b)=>Number(b.version)-Number(a.version))[0];
    const current=report&&report.run_id===run.id&&merge&&!busy;
    const closed=!!v.application.collection_closed_at&&!Number(v.application.allow_late);
    return `<div class="review-report"><p>${decided<4?'Yorum ve puanları inceleyip kararınızı kaydedin.':!merge?'Kararlar rapora aktarılıyor.':report?'Güncel öğrenci raporu hazır.':'İnceleme tamamlandı. Öğrenci raporunu hazırlayabilirsiniz.'}</p><div class="row"><button data-create-report="${run.id}" ${!merge||busy?'disabled':''}>Öğrenci raporunu hazırla</button><button class="secondary" data-current-report="${report?.id||''}" data-format="docx" ${!current?'disabled':''}>Word</button><button class="secondary" data-current-report="${report?.id||''}" data-format="xlsx" ${!current?'disabled':''}>Excel</button><button class="secondary" data-show-report="${report?.id||''}" ${!current||!closed||report?.published_at?'disabled':''}>${current&&report.published_at?'Öğrenciye gösteriliyor':'Öğrenciye göster'}</button></div>${!closed?'<p class="muted">Öğrenciye göstermek için bu vakada veri toplamayı kapatın.</p>':''}</div>`;
}
