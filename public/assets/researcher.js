'use strict';
const select=(id,label,opts,value='')=>`<label for="${esc(id)}">${esc(label)}</label><select id="${esc(id)}">${opts.map(([v,t])=>`<option value="${esc(v)}" ${String(v)===String(value)?'selected':''}>${esc(t)}</option>`).join('')}</select>`;

const accountStatus=s=>({active:'Etkin',frozen:'Dondurulmuş',archived:'Arşiv'}[s]||s);
const roleTitle=role=>({student:'Öğrenci',researcher:'Araştırmacı',super_admin:'Süper Admin'}[role]||role);
const caseStatus=s=>({active:'Aktif',draft:'Taslak',closed:'Yeni başlangıçlara kapalı',archived:'Arşiv'}[s]||s);
const isSuperAdmin=()=>me?.role==='super_admin';
const ownerName=id=>dash.users.find(u=>u.id===id)?.login||'—';
const caseStudent=id=>dash.users.find(u=>u.id===id)||dash.case_students.find(u=>u.id===id);
const caseStudents=()=>[...dash.users.filter(u=>u.role==='student'),...dash.case_students];
const caseOption=c=>[c.id,isSuperAdmin()?c.title+' · '+ownerName(c.owner_id):c.title];
const definitionOwnerOptions=()=>[
    ...dash.users.filter(u=>u.role==='researcher').map(u=>[u.id,u.login+(u.name?' · '+u.name:'')]),
    [me.id,'Kendi vakalarım']
];
const definitionOwnerField=()=>isSuperAdmin()?select('workspace-owner','Kullanacak araştırmacı',definitionOwnerOptions(),me.id):'';
const latestDefinitions=()=>dash.definitions.filter(d=>!d.deleted_at&&!Number(d.archived)&&!dash.definitions.some(n=>n.parent_id===d.id));
const overviewChoice={caseId:'',query:'',status:''};
function overviewPanel(){
    if(!dash.cases.some(c=>c.id===overviewChoice.caseId))overviewChoice.caseId=dash.cases[0]?.id||'';
    return `<div class="card"><h2>Vaka ve öğrenci seç</h2><p class="muted">Önce vakayı seçin. Ardından öğrenciyi bulun ve çalışmasını açın.</p>${select('overview-case','Vaka',dash.cases.length?dash.cases.map(caseOption):[['','Henüz vaka yok']],overviewChoice.caseId)}<div class="grid"><div>${field('overview-search','Öğrenci ara',overviewChoice.query)}</div><div>${select('overview-status','Çalışma durumu',[['','Tüm durumlar'],['unopened','Henüz başlamadı'],['initial','Başlangıç aşamasında'],['working','Devam ediyor'],['submitted','Teslim edildi'],['late','Ek süre verilenler']],overviewChoice.status)}</div></div><p id="overview-summary" class="muted" aria-live="polite"></p><div id="overview-students" class="student-list"></div></div>`;
}
function bindProcessButtons(container){container.querySelectorAll('[data-process]').forEach(b=>b.onclick=()=>processView(b.dataset.process).catch(e=>notify(e.message)));}
function bindOverview(){
    const render=()=>{
        const cid=overviewChoice.caseId,query=overviewChoice.query.toLocaleLowerCase('tr'),status=overviewChoice.status;
        const rows=caseStudents().map(u=>({u,a:dash.applications.find(a=>a.user_id===u.id&&a.case_id===cid),ass:dash.assignments.find(a=>a.user_id===u.id&&a.case_id===cid)})).filter(({a,ass})=>a||Number(ass?.active));
        const shown=rows.filter(({u,a,ass})=>(u.login+' '+u.name).toLocaleLowerCase('tr').includes(query)&&(!status||(status==='late'?Number(ass?.active)&&Number(ass?.allow_late):status==='unopened'?!a:a?.state===status)));
        $('#overview-summary').textContent=`${rows.length} öğrenci · ${rows.filter(r=>r.a?.state==='submitted').length} teslim · ${shown.length} gösteriliyor`;
        $('#overview-students').innerHTML=shown.map(({u,a,ass})=>{
            const analysis=a?dash.analysis_status.find(s=>s.app_id===a.id):null,report=a?dash.report_status.find(r=>r.app_id===a.id):null;
            return `<article class="student-row" data-overview-row="${esc(u.id)}"><div><h3>${esc(u.login)} · ${esc(u.name)}</h3><p><span class="pill">${a?stateName(a.state)+' · Deneme '+a.attempt_no:'Henüz başlamadı'}</span> ${!Number(ass?.active)?'<span class="pill warn">Atama kaldırılmış</span>':''} ${Number(ass?.allow_late)?'<span class="pill">Ek süre açık</span>':''} <small>${accountStatus(u.status)}</small></p><p class="muted">${analysis?`Analiz: ${analysis.completed_step}/8 · `:''}${report?`Rapor: ${report.published_at?'Yayımlandı':report.approved_at?'Onaylı':'İncelemede'} · `:''}Son etkinlik: ${a?.last_activity?displayTime(a.last_activity):'Henüz yok'}</p></div>${a?`<button data-process="${esc(a.id)}">Çalışmayı aç</button>`:'<span class="muted">Henüz çalışma yok</span>'}</article>`;
        }).join('')||`<p class="empty">${dash.cases.length?'Bu seçimde öğrenci bulunamadı.':'Önce Vakalar ve atamalar bölümünden vaka ekleyin.'}</p>`;
        bindProcessButtons($('#overview-students'));
    };
    $('#overview-case').onchange=e=>{overviewChoice.caseId=e.target.value;render();};
    $('#overview-search').oninput=e=>{overviewChoice.query=e.target.value;render();};
    $('#overview-status').onchange=e=>{overviewChoice.status=e.target.value;render();};render();
}
function assignedStudents(cid){
    const global=isSuperAdmin(),c=dash.cases.find(c=>c.id===cid),assigned=dash.assignments.filter(a=>a.case_id===cid&&(global||Number(a.active)));
    const dialog=showDialog(`<h2>Atanan öğrenciler</h2><p><b>${esc(c.title)}</b> · ${assigned.length} öğrenci</p><div class="student-list">${assigned.map(ass=>{const u=caseStudent(ass.user_id),a=dash.applications.find(a=>a.user_id===ass.user_id&&a.case_id===cid);return `<article class="student-row" data-assigned-student="${esc(ass.user_id)}"><div><h3>${esc(u?.login||ass.user_id)} · ${esc(u?.name)}</h3><p>${a?stateName(a.state):'Henüz başlamadı'} · ${esc(accountStatus(u?.status))}${Number(ass.allow_late)?' · Ek süre açık':''}${Number(ass.active)?'':' · Atama kaldırılmış'}</p></div></article>`;}).join('')||'<p class="empty">Bu vakaya henüz öğrenci atanmamış.</p>'}</div><button id="manage-case-assignments" class="spaced">Bu vakanın atamalarını yönet</button>`);
    handler('#manage-case-assignments',()=>{
        dialog.addEventListener('close',()=>assignmentForm(cid),{once:true});
        dialog.close();
    });
}
function definitionDetails(d){
    const body=JSON.parse(d.body);
    if(d.family==='label')return `<p><b>Grup:</b> ${esc(({user:'Öğrenci mesajı',assistant:'AI yanıtı'})[body.target_role]||'Grup seçilmedi · Düzenleyerek seçin')}</p>${body.target_role==='user'?`<p><b>Konu kapsamı:</b> ${esc(({pedagogical:'Pedagojik vaka ve kavramlar',outside:'Pedagoji dışındaki talepler',any:'Her iki kapsam'})[body.topic_scope]||'Kapsam seçilmedi')}</p>`:''}<p class="prose">${esc(body.description||'Açıklama eklenmemiş.')}</p>`;
    return `<p class="prose">${esc(body.text)}</p>${body.dimensions.map(dim=>`<section class="rubric-dimension-summary"><h4>${esc(dim.name||dim.id)} · ${esc(dim.min)}–${esc(dim.max)} puan · Ağırlık ${esc(dim.weight)}</h4>${dim.levels.map(l=>`<p><b>${esc(l.name)} (${esc(l.score)} puan):</b> ${esc(l.description)}</p>`).join('')}</section>`).join('')}`;
}
const auditNames={profile_updated:'Profil güncellendi',password_changed:'Parola değiştirildi',account_invite:'Kayıt daveti oluşturuldu',account_registered:'Öğrenci kaydoldu',password_reset_issued:'Parola yenileme kodu oluşturuldu',password_reset_completed:'Parola yenilendi',new_attempt:'Yeni deneme açıldı',message_labels_ai:'AI mesaj etiketleri hazırlandı',participant:'Kullanıcı kaydedildi',case:'Vaka sürümü kaydedildi',assign:'Atama güncellendi',assign_bulk:'Toplu atama',late_access:'Ek süre güncellendi',collection:'Veri toplama durumu değişti',definition:'Rubrik / etiket kaydedildi',delete_definition:'Rubrik / etiket silindi',labels:'Mesaj etiketleri incelendi',note:'İç not eklendi',exclude:'Analiz kapsamı değişti',resolve_request:'Takılı AI isteği kapatıldı',resolve_analysis:'Takılı analiz adımı kapatıldı',analysis_run:'Analiz başlatıldı',analysis_step:'Analiz adımı çalıştırıldı',decision:'Araştırmacı kararı kaydedildi',report_edit_feedback:'Rapor geri bildirimi düzenlendi',report_create:'Rapor sürümü oluşturuldu',report_approve:'Rapor onaylandı',report_publish:'Rapor yayımlandı',export:'Veri paketi hazırlandı',download:'Dosya indirildi',save:'Çalışma kaydedildi',initial:'Başlangıç kesinleştirildi',submit:'Son teslim yapıldı',onboarding:'Katılım bilgileri güncellendi',close_study:'Eski global kapanış kaydı'};
function auditEntry(row){
    let d={};try{d=typeof row.detail==='string'?JSON.parse(row.detail):row.detail||{};}catch{}
    let title=auditNames[row.action]||'İşlem kaydedildi';
    if(row.action==='collection')title=d.closed?'Vakada veri toplama kapatıldı':'Vakada veri toplama açıldı';
    if(row.action==='late_access')title=d.open?'Öğrenciye ek süre verildi':'Öğrencinin ek süresi kapatıldı';
    if(['assign','assign_bulk'].includes(row.action))title=d.active?'Öğrenci vakaya atandı':'Öğrencinin ataması kaldırıldı';
    const user=caseStudent(d.user_id||(['participant','onboarding'].includes(row.action)?row.target:null)),ass=dash.assignments.find(a=>a.id===row.target),app=dash.applications.find(a=>a.id===(d.app_id||row.target));
    const c=dash.cases.find(c=>c.id===(d.case_id||ass?.case_id||app?.case_id||(['case','collection'].includes(row.action)?row.target:null)));
    const def=dash.definitions.find(x=>x.id===row.target),actor=dash.users.find(u=>u.id===row.actor);
    const details=[];
    if(c)details.push('Vaka: '+c.title);if(user||d.login)details.push('Kullanıcı: '+(user?.login||d.login));else if(ass||app){const s=caseStudent(ass?.user_id||app?.user_id);if(s)details.push('Öğrenci: '+s.login);}
    if(def)details.push((def.family==='rubric'?'Rubrik: ':'Etiket: ')+def.name+' · v'+def.version);
    if(d.step)details.push('Analiz adımı: '+d.step);
    if(d.format)details.push('Dosya: '+({xlsx:'Excel',docx:'Word',pdf:'PDF',csv:'CSV',json:'JSON',disco:'Disco CSV'}[d.format]||d.format));
    if(d.status)details.push('Hesap durumu: '+accountStatus(d.status));
    return {title,details,actor:actor?.login||'Geçmiş kullanıcı'};
}
function auditPanel(){return `<div class="card"><div class="toolbar"><h2>İşlem geçmişi</h2><button class="secondary" id="download-audit">İşlem geçmişini JSON indir</button></div><p class="muted">${isSuperAdmin()?'Sistemdeki':'Vakalarınıza ve işlemlerinize ait son'} ${dash.audit.length} kayıt. İşlem, hesap ve zaman otomatik tutulur.</p>${field('audit-search','İşlem, vaka veya kullanıcı ara')}<p id="audit-count" class="muted" aria-live="polite"></p><div id="audit-list" class="audit-list"></div><button class="secondary" id="more-audit">Daha fazla göster</button></div>`;}
function bindAudit(){
    let limit=20;
    const render=()=>{const query=getValue('audit-search').toLocaleLowerCase('tr'),found=dash.audit.map(row=>({row,...auditEntry(row)})).filter(e=>(e.title+' '+e.details.join(' ')+' '+e.actor+' '+e.row.reason).toLocaleLowerCase('tr').includes(query));$('#audit-count').textContent=`${Math.min(limit,found.length)} / ${found.length} kayıt gösteriliyor`;$('#more-audit').hidden=limit>=found.length;$('#audit-list').innerHTML=found.slice(0,limit).map(e=>`<article class="audit-entry"><h3>${esc(e.title)}</h3><p>${displayTime(e.row.created_at)} · <b>${esc(e.actor)}</b></p>${e.details.map(t=>`<p>${esc(t)}</p>`).join('')}${e.row.reason?`<p><b>Gerekçe:</b> ${esc(e.row.reason)}</p>`:''}</article>`).join('')||'<p class="empty">Bu aramada işlem bulunamadı.</p>';};
    $('#audit-search').oninput=()=>{limit=20;render();};handler('#more-audit',()=>{limit+=20;render();});render();handler('#download-audit',()=>saveJsonFile('vakalab-islem-gecmisi.json',dash.audit));
}
function saveJsonFile(filename,data){const url=URL.createObjectURL(new Blob([JSON.stringify(data,null,2)],{type:'application/json'})),a=document.createElement('a');a.href=url;a.download=filename;a.click();setTimeout(()=>URL.revokeObjectURL(url),1000);}

function accountsPanel(){
    const global=isSuperAdmin();
    return `<div class="toolbar"><h2>Kullanıcılar</h2><div class="row"><button id="add-participant">+ Kullanıcı ekle</button>${global?'<button class="secondary" id="bulk-participants">Toplu kullanıcı ekle</button>':''}</div></div>
    ${global?field('account-search','Hesap, rol veya ekleyen kullanıcıyı ara'):'<p class="muted">Burada eklediğiniz kullanıcıları yönetebilirsiniz.</p>'}
    <div class="card table-wrap"><table><thead><tr><th>Hesap</th><th>Ad soyad</th><th>Tür</th>${global?'<th>Ekleyen hesap</th>':''}<th>Durum</th><th></th></tr></thead><tbody id="account-rows"></tbody></table></div>
    <details id="account-reset-panel" class="card spaced"><summary>Parola yenileme talepleri</summary><button id="refresh-reset-requests" class="secondary spaced">Talepleri yenile</button><div id="account-reset-list" aria-live="polite"></div></details>`;
}
function bindAccounts(){
    const global=isSuperAdmin(),accounts=global?dash.users:dash.users.filter(u=>u.owner_id===me.id);
    const render=()=>{
        const query=getValue('account-search').trim().toLocaleLowerCase('tr');
        const shown=query?accounts.filter(u=>[u.login,u.name,roleTitle(u.role),ownerName(u.owner_id)].join(' ').toLocaleLowerCase('tr').includes(query)):accounts;
        $('#account-rows').innerHTML=shown.map(u=>`<tr><td>${esc(u.login)}</td><td>${esc(u.name)}</td><td>${esc(roleTitle(u.role))}</td>${global?`<td>${esc(ownerName(u.owner_id))}</td>`:''}<td>${esc(accountStatus(u.status))}</td><td>${u.role==='super_admin'?'':`<button class="secondary" data-edit-user="${esc(u.id)}">Düzenle</button>`}</td></tr>`).join('')||`<tr><td colspan="${global?6:5}">Eşleşen kullanıcı yok.</td></tr>`;
        document.querySelectorAll('[data-edit-user]').forEach(b=>b.onclick=()=>participantForm(accounts.find(u=>u.id===b.dataset.editUser)));
    };
    $('#account-search')?.addEventListener('input',render);
    handler('#add-participant',()=>participantForm());
    handler('#bulk-participants',bulkParticipantsForm);
    const resetPanel=$('#account-reset-panel');
    resetPanel.addEventListener('toggle',()=>{if(resetPanel.open)loadResetRequests();});
    handler('#refresh-reset-requests',loadResetRequests);
    render();
}
function showAccountCode(title,{code,expires_at}){
    const dialog=showDialog(`<h2>${esc(title)}</h2><p>Bu kodu yalnız ilgili kişiye iletin. Son kullanım ${displayTime(expires_at)}.</p>
    <label for="issued-account-code">Tek kullanımlık kod</label><input id="issued-account-code" value="${esc(code)}" readonly>
    <button class="secondary spaced" id="copy-account-code">Kodu kopyala</button>`);
    dialog.querySelector('#copy-account-code').onclick=async()=>{
        const input=dialog.querySelector('#issued-account-code');
        try{await navigator.clipboard.writeText(input.value);notify('Kod kopyalandı.');}
        catch{input.focus();input.select();}
    };
}
async function loadResetRequests(){
    const list=$('#account-reset-list');
    if(!list)return;
    list.textContent='Talepler yükleniyor…';
    try{
        const requests=await api('password_reset_requests');
        if(!list.isConnected)return;
        list.innerHTML=requests.map(u=>`<article class="student-row"><div><h3>${esc(u.login)} · ${esc(u.name)}</h3>
        <p>Talep ${displayTime(u.requested_at)}${u.expires_at?' · Kod son kullanımı '+displayTime(u.expires_at):''}</p></div>
        <button type="button" class="secondary" data-issue-reset="${esc(u.id)}">${u.expires_at?'Yeni kod oluştur':'Kod oluştur'}</button></article>`).join('')||'<p>Henüz parola yenileme talebi yok.</p>';
        list.querySelectorAll('[data-issue-reset]').forEach(button=>button.onclick=()=>{
            const user=requests.find(row=>row.id===button.dataset.issueReset);
            const dialog=showDialog(`<h2>Parola yenileme kodu</h2><p>${esc(user.login)} hesabının sahibini doğruladıktan sonra kodu oluşturun. Kod 30 dakika geçerlidir.</p><button id="commit" class="spaced">Yenileme kodu oluştur</button>`,async()=>{
                const result=await api('password_reset_code',{user_id:user.id});
                dialog.addEventListener('close',()=>showAccountCode('Parola yenileme kodu hazır',result),{once:true});
                loadResetRequests();
            });
        });
    }catch(e){list.textContent=e.message;}
}
function assessmentHistory(v){
    const reports=[...v.reports].sort((a,b)=>Number(b.version)-Number(a.version));
    const reportRows=reports.map(r=>`<article class="process-entry">
        <h3>Rapor · Sürüm ${esc(r.version)}</h3>
        <p>Oluşturma ${displayTime(r.created_at)} · Onay ${displayTime(r.approved_at)} · Yayın ${displayTime(r.published_at)}</p>
        <div class="row"><button class="secondary" data-student-report="${esc(r.id)}">Raporu önizle</button>
        <button class="secondary" data-report="${esc(r.id)}" data-format="json">Rapor kaydını indir</button></div>
    </article>`).join('');
    const runs=[...v.analysis_runs].reverse().map(run=>`<details>
        <summary>Değerlendirme · ${displayTime(run.created_at)} · Sürüm ${esc(run.review_version)}</summary>
        ${v.steps.filter(step=>step.run_id===run.id).map(step=>`<article class="process-entry">
            <h4>Adım ${esc(step.step)} · Deneme ${esc(step.attempt)} · ${esc(processStatus(step.status))}</h4>
            ${step.error?`<p>${esc(processError(step.error))}</p>`:''}
            <details><summary>Girdi ve sonuç</summary>${pretty(processObject(step.input))}${pretty(processObject(step.output))}</details>
            ${v.decisions.filter(d=>d.step_id===step.id).map(d=>`<details><summary>Araştırmacı kararı · ${esc(d.action)} · ${displayTime(d.created_at)}</summary>
                <p class="prose">${esc(d.reason)}</p>${pretty(processObject(d.body))}
            </details>`).join('')}
        </article>`).join('')||'<p>Henüz değerlendirme adımı yok.</p>'}
    </details>`).join('');
    return `${reportRows||'<p>Henüz rapor yok.</p>'}${runs||'<p>Henüz değerlendirme yok.</p>'}`;
}
async function adminHome(){
    dash=await api('dashboard');
    $('#root').innerHTML=`<div class="eyebrow">${roleTitle(me.role)} paneli · ${esc(me.login)}</div><h1>${isSuperAdmin()?'Sistem yönetimi':'Araştırmacı paneli'}</h1><p class="intro">${isSuperAdmin()?'Hesapları yönetin ve araştırma kayıtlarını görüntüleyin.':'Vakalarınızı, katılımcılarınızı ve değerlendirmelerinizi yönetin.'}</p><nav class="tabs">${[['overview','Genel durum'],['participants','Kullanıcılar'],['cases','Vakalar ve atamalar'],['definitions','Rubrikler ve etiketler'],['exports','Veri indirme'],['system','İşlem geçmişi']].map(([k,l])=>`<button data-admin-tab="${k}" class="${adminTab===k?'active':''}">${l}</button>`).join('')}</nav><div id="admin-body" class="spaced"></div>`;
    document.querySelectorAll('[data-admin-tab]').forEach(b=>b.onclick=()=>{adminTab=b.dataset.adminTab;adminHome().catch(e=>notify(e.message));});renderAdmin();
}
function renderAdmin(){
    const global=isSuperAdmin(),defs=global?dash.definitions:latestDefinitions();let html='';
    if(adminTab==='overview')html=overviewPanel();
    if(adminTab==='participants')html=accountsPanel();
    if(adminTab==='cases')html=`<div class="toolbar"><h2>Vakalar ve atamalar</h2><div class="row"><button id="add-case">+ Vaka ekle</button><button class="secondary" id="assignment">Toplu atama / Ek süre</button></div></div><div class="cards">${dash.cases.map(c=>`<div class="card"><span class="pill">${caseStatus(c.status)} · v${c.version}</span><h2>${esc(c.title)}</h2>${global?`<p class="muted">Araştırmacı · ${esc(ownerName(c.owner_id))}</p>`:''}<p>${c.collection_closed_at?'Veri toplama kapalı · '+displayTime(c.collection_closed_at):'Veri toplama açık'}</p><div class="row"><button class="secondary" data-assigned="${esc(c.id)}">Atanan öğrencileri göster (${dash.assignments.filter(a=>a.case_id===c.id&&(global||Number(a.active))).length})</button><button class="secondary" data-preview="${esc(c.id)}">${global?'Vaka ayrıntıları':'Öğrenci önizlemesi'}</button><button class="secondary" data-edit-case="${esc(c.id)}">Düzenle</button><button class="${c.collection_closed_at?'':'danger'}" data-collection="${esc(c.id)}">${c.collection_closed_at?'Veri toplamayı aç':'Veri toplamayı kapat'}</button></div></div>`).join('')||(global?'<div class="empty">Henüz vaka yok.</div>':'<div class="empty">İlk vakanızı ekleyin.</div>')}</div>`;
    if(adminTab==='definitions')html=`<div class="toolbar"><h2>Rubrikler ve etiketler</h2><div class="row"><button id="add-rubric">+ Rubrik</button><button class="secondary" id="add-label">+ Etiket</button></div></div>${defs.map(d=>`<div class="card"><div class="toolbar"><h3>${esc(d.name)} <span class="pill">${d.family==='rubric'?'Rubrik':'Etiket · '+(({user:'Öğrenci mesajı',assistant:'AI yanıtı'})[JSON.parse(d.body).target_role]||'Grup seçilmedi')} · v${d.version}</span></h3>${global?`<p class="muted">${esc(ownerName(d.owner_id))}${d.deleted_at?' · Silindi':Number(d.archived)?' · Arşiv':''}</p>`:''}${!d.deleted_at&&!dash.definitions.some(n=>n.parent_id===d.id)?`<div class="row"><button class="secondary" data-edit-def="${esc(d.id)}">Düzenle</button><button class="danger" data-delete-def="${esc(d.id)}">Sil</button></div>`:''}</div><details><summary>Tanım</summary>${definitionDetails(d)}</details></div>`).join('')||'<div class="empty">Henüz rubrik veya etiket yok.</div>'}`;
    if(adminTab==='exports')html=`<div class="card"><h2>Vaka verilerini indir</h2>${select('export-case','Hangi vaka?',[['','Tüm vakalar'],...dash.cases.map(caseOption)])}<p class="muted">Seçilen kapsamdaki tüm kayıtlar indirilir; test, deneme ve analiz dışı kayıtlar durum bilgileriyle birlikte korunur. Excel ve JSON metinleri ve kimlikleri içerir.</p><div class="row spaced"><button data-export="xlsx">Excel indir</button><button class="secondary" data-export="json">Tüm verileri JSON indir</button></div><hr><h3>Disco için süreç verisi</h3><p>Her öğrenci-vaka denemesi ayrı süreçtir. Aynı öğrencinin tekrarları farklı Case ID ve Deneme numarası ile ayrılır; Participant aynı kalır. Geçiş ve süre dosyası her kapanan bölüm ziyaretini giriş/çıkış zamanı ve geçen saniyesiyle verir. Son teslimden sonrası sayılmaz. Ayrıntılı dosya mesajları, soru etiketlerini ve AI yanıt beklemelerini ayrıca içerir; öğrenci kimliği kodlanır. Periyodik teknik kayıtlar bu dosyaya girmez; AI süreleri ayrı kaynakla gösterilir.</p><label class="inline-check"><input id="disco-all-data" type="checkbox">Test, pratik ve analiz dışı denemeleri de dahil et</label><button data-export="disco">Disco: geçişler ve süreler</button><button class="secondary" id="export-disco-events">Disco: ayrıntılı olaylar ve AI</button><details class="spaced"><summary>Disco içe aktarma eşleştirmesi</summary><p>Case ID → Case, Deneme numarası → ek özellik (filtreleme), Activity → Activity, Start Timestamp ve Complete Timestamp → Timestamp, Resource → Resource. Zaman biçimi: yyyy-MM-dd HH:mm:ss.SSS (UTC). Quality ve Time Source gözlemin kaynağını belirtir; etkinlik aralıkları okuma/düşünme süresi değildir.</p></details></div>`;
    if(adminTab==='system')html=auditPanel();
    $('#admin-body').innerHTML=html;
    if(adminTab==='overview')bindOverview();
    handler('#export-disco-events',()=>download({format:'disco',view:'events',case_id:getValue('export-case'),all_data:$('#disco-all-data').checked?1:0}));
    if(adminTab==='system')bindAudit();
    document.querySelectorAll('[data-assigned]').forEach(b=>b.onclick=()=>assignedStudents(b.dataset.assigned));
    document.querySelectorAll('[data-process]').forEach(b=>b.onclick=()=>processView(b.dataset.process).catch(e=>notify(e.message)));
    if(adminTab==='participants')bindAccounts();
    handler('#add-case',()=>caseForm());document.querySelectorAll('[data-edit-case]').forEach(b=>b.onclick=()=>caseForm(dash.cases.find(c=>c.id===b.dataset.editCase)));
    document.querySelectorAll('[data-preview]').forEach(b=>b.onclick=()=>{const c=dash.cases.find(c=>c.id===b.dataset.preview);showDialog(`<h2>${esc(c.title)} · v${c.version}</h2><div class="prose case-body">${caseBodyMarkup(c)}</div><p class="prose">${esc(c.instructions)}</p>${global&&c.notes?`<h3>Araştırmacı notları</h3><p class="prose">${esc(c.notes)}</p>`:''}`);});
    document.querySelectorAll('[data-collection]').forEach(b=>b.onclick=async()=>{const c=dash.cases.find(c=>c.id===b.dataset.collection);if(!confirm(`${c.title}: veri toplama ${c.collection_closed_at?'açılsın':'kapatılsın'} mı? Mevcut kayıtlar korunur; kişiye verilen ek süreler kapanır.`))return;try{await api('admin',{action:'collection',case_id:c.id,closed:!c.collection_closed_at});await adminHome();}catch(e){notify(e.message);}});
    handler('#assignment',assignmentForm);handler('#add-rubric',()=>definitionForm('rubric'));handler('#add-label',()=>definitionForm('label'));
    document.querySelectorAll('[data-edit-def]').forEach(b=>b.onclick=()=>{const d=dash.definitions.find(d=>d.id===b.dataset.editDef);definitionForm(d.family,d);});
    document.querySelectorAll('[data-delete-def]').forEach(b=>b.onclick=async()=>{const d=dash.definitions.find(d=>d.id===b.dataset.deleteDef);if(!confirm(`${d.name} silinsin mi? Yeni seçimlerden kaldırılır; önceki analiz, etiket ve rapor kayıtları korunur.`))return;try{await api('admin',{action:'delete_definition',id:d.id});await adminHome();}catch(e){notify(e.message);}});
    document.querySelectorAll('[data-export]').forEach(b=>b.onclick=()=>download({format:b.dataset.export,case_id:getValue('export-case'),all_data:b.dataset.export==='disco'?Number($('#disco-all-data').checked):1}));
}
function participantForm(u){
    const m=u?JSON.parse(u.metadata):{};
    showDialog(`<h2>${u?'Kullanıcıyı düzenle':'Kullanıcı ekle'}</h2>${u?`<p>${esc(roleTitle(u.role))}</p>`:select('p-role','Hesap türü',[['student','Öğrenci'],['researcher','Araştırmacı']])}${field('p-login','Öğrenci numarası / kullanıcı adı',u?.login)}${field('p-name','Ad soyad',u?.name)}${field('p-password',u?'Yeni parola (boşsa korunur)':'Parola (en az 12 karakter)','','password')}${select('p-status','Hesap durumu',[['active','Etkin'],['frozen','Dondurulmuş'],['archived','Arşiv']],u?.status||'active')}<details class="spaced"><summary>Ek bilgiler (isteğe bağlı)</summary>${field('p-program','Program',m.program)}${field('p-year','Sınıf',m.year)}${field('p-notes','Özel not',m.researcher_notes,'textarea')}</details><button class="spaced" id="commit">Kaydet</button>`,async()=>{
        const role=u?.role||getValue('p-role');
        const identity=u?{id:u.id}:{};
        await api('admin',{action:'participant',...identity,role,login:getValue('p-login'),name:getValue('p-name'),password:getValue('p-password'),status:getValue('p-status'),metadata:{program:getValue('p-program'),year:getValue('p-year'),researcher_notes:getValue('p-notes')}});
        await adminHome();
    });
}
function bulkParticipantsForm(){
    const maxRows=100;
    const dialog=showDialog(`<h2>Toplu kullanıcı ekle</h2>
        <p>Her satırda bir öğrenci hesabı oluşturulur. Parola en az 12 karakter olmalıdır.</p>
        <div class="table-wrap"><table class="bulk-accounts">
        <thead><tr><th scope="col">Satır</th><th scope="col">Kullanıcı adı</th><th scope="col">Şifre</th><th scope="col">İşlem</th></tr></thead>
        <tbody id="bulk-account-rows"></tbody></table></div>
        <button type="button" class="secondary spaced" id="add-account-row">+ Satır ekle</button>
        <button class="spaced" id="commit">Kaydet</button>`,async()=>{
        const rows=[...body.rows].map(row=>({
            login:row.querySelector('[data-bulk-login]').value,
            password:row.querySelector('[data-bulk-password]').value
        }));
        const {count}=await api('admin',{action:'participants_bulk',rows});
        notify(`${count} öğrenci hesabı oluşturuldu.`);
        await adminHome();
    });
    const body=dialog.querySelector('#bulk-account-rows'),add=dialog.querySelector('#add-account-row');
    const refreshRows=()=>{
        add.disabled=body.rows.length>=maxRows;
        [...body.rows].forEach((row,index)=>{
            const number=index+1;
            row.cells[0].textContent=number;
            row.querySelector('[data-bulk-login]').setAttribute('aria-label',`${number}. satır kullanıcı adı`);
            row.querySelector('[data-bulk-password]').setAttribute('aria-label',`${number}. satır şifre`);
            const remove=row.querySelector('button');
            remove.disabled=body.rows.length===1;
            remove.setAttribute('aria-label',`${number}. satırı kaldır`);
        });
    };
    const addRow=()=>{
        const row=body.insertRow();
        row.innerHTML='<td></td><td><input type="text" data-bulk-login maxlength="100" autocomplete="off" autocapitalize="off" spellcheck="false"></td><td><input type="password" data-bulk-password autocomplete="new-password"></td><td><button type="button" class="secondary">Kaldır</button></td>';
        row.querySelector('button').onclick=()=>{row.remove();refreshRows();add.focus();};
        refreshRows();
        return row;
    };
    for(let i=0;i<3;i++)addRow();
    add.onclick=()=>addRow().querySelector('[data-bulk-login]').focus();
    body.querySelector('[data-bulk-login]').focus();
}
function caseEditor(c){
    return `<label id="case-body-label">Vaka metni</label>
        <div class="case-editor">
            <div class="case-editor-tools" role="group" aria-label="Vaka metni biçimi">
                <button type="button" class="secondary" data-case-format="bold" aria-label="Kalın" aria-pressed="false" title="Kalın"><strong>B</strong></button>
                <button type="button" class="secondary" data-case-format="italic" aria-label="İtalik" aria-pressed="false" title="İtalik"><em>I</em></button>
                <button type="button" class="secondary" data-case-format="underline" aria-label="Altı çizili" aria-pressed="false" title="Altı çizili"><u>U</u></button>
                <button type="button" class="secondary" data-case-format="insertUnorderedList" aria-label="Madde işaretli liste" aria-pressed="false">• Liste</button>
            </div>
            <div id="c-body" class="case-body case-editor-input" contenteditable="true" role="textbox" aria-labelledby="case-body-label" aria-multiline="true">${caseBodyMarkup(c)}</div>
        </div>`;
}
function bindCaseEditor(dialog){
    const editor=dialog.querySelector('#c-body');
    const buttons=[...dialog.querySelectorAll('[data-case-format]')];
    let range=null;
    const remember=()=>{
        const selection=window.getSelection();
        if(!selection.rangeCount||!editor.contains(selection.anchorNode)||!editor.contains(selection.focusNode)){
            if(!range)buttons.forEach(button=>button.setAttribute('aria-pressed','false'));
            return;
        }
        range=selection.getRangeAt(0).cloneRange();
        for(const button of buttons)button.setAttribute('aria-pressed',String(document.queryCommandState(button.dataset.caseFormat)));
    };
    document.addEventListener('selectionchange',remember);
    dialog.addEventListener('close',()=>document.removeEventListener('selectionchange',remember),{once:true});
    for(const button of buttons){
        button.addEventListener('mousedown',event=>event.preventDefault());
        button.onclick=()=>{
            remember();
            editor.focus();
            if(range&&editor.contains(range.commonAncestorContainer)){
                const selection=window.getSelection();
                selection.removeAllRanges();
                selection.addRange(range);
            }
            document.execCommand(button.dataset.caseFormat);
            remember();
        };
    }
    editor.addEventListener('paste',event=>{
        event.preventDefault();
        document.execCommand('insertText',false,event.clipboardData.getData('text/plain'));
    });
    editor.addEventListener('drop',event=>event.preventDefault());
}
function caseForm(c){
    const renderRubrics=()=>{
        const owner=c?c.owner_id:me.id;
        const rubrics=dash.definitions.filter(d=>d.owner_id===owner&&d.family==='rubric'&&!d.deleted_at&&!Number(d.archived)&&
            (d.id===c?.rubric_id||!dash.definitions.some(n=>n.parent_id===d.id)));
        $('#case-rubric-field').innerHTML=select('c-rubric','Rubrik',[['','Henüz bağlı değil'],...rubrics.map(d=>[d.id,d.name+' · v'+d.version])],c?.rubric_id||'');
    };
    const dialog=showDialog(`<h2>${c?'Vakayı düzenle':'Vaka oluştur'}</h2>${field('c-title','Başlık',c?.title)}${field('c-position','Sıra',c?.position||1,'number')}${caseEditor(c)}${field('c-instructions','Öğrenci yönergeleri',c?.instructions,'textarea')}${select('c-status','Durum',[['draft','Taslak'],['active','Aktif'],['closed','Yeni başlangıçlara kapalı'],['archived','Arşiv']],c?.status||'draft')}<div id="case-rubric-field"></div><details class="spaced"><summary>Ek ayarlar</summary>${field('c-notes','Özel notlar',c?.notes,'textarea')}<label class="inline-check"><input type="checkbox" id="c-practice" ${Number(c?.practice)?'checked':''}>Deneme vakası</label><label class="inline-check"><input type="checkbox" id="c-stop" ${Number(c?.stop_access)?'checked':''}>Vaka görüntüleme erişimini de durdur</label></details><button class="spaced" id="commit">Vakayı kaydet</button>`,async()=>{await api('admin',{action:'case',...(c?{id:c.id}:{}),title:getValue('c-title'),body_html:$('#c-body').innerHTML,instructions:getValue('c-instructions'),notes:getValue('c-notes'),position:Number(getValue('c-position')),status:getValue('c-status'),rubric_id:getValue('c-rubric'),practice:$('#c-practice').checked,stop_access:$('#c-stop').checked});await adminHome();});
    bindCaseEditor(dialog);
    renderRubrics();
}
function assignmentForm(caseId){
    if(!dash.cases.length){notify('Önce bir vaka ekleyin.');return;}
    const students=caseStudents();
    showDialog(`<h2>Toplu atama ve ek süre</h2>${select('a-case','Vaka',dash.cases.map(caseOption),caseId||dash.cases[0].id)}${select('a-active','İşlem',[['1','Seçilenlere ata'],['0','Seçilenlerin atamasını kaldır'],['late-open','Kapalı vakada seçilenlere ek süre ver'],['late-close','Seçilenlerin ek süresini kapat']])}<p class="muted">Ek süre yalnız veri toplaması kapalı vakalarda kullanılır. Teslim edilmiş yanıtlar yeniden açılmaz.</p><label class="inline-check"><input type="checkbox" id="select-all-students">Tümünü seç</label><div class="selection-list" id="assign-students"></div><button class="spaced" id="commit">Seçilenlere uygula</button>`,async()=>{const ids=[...document.querySelectorAll('[data-assign-student]:checked')].map(n=>n.value);if(!ids.length)throw new Error('En az bir öğrenci seçin.');const mode=getValue('a-active'),cid=getValue('a-case');await api('admin',{action:mode.startsWith('late')?'late_access':'assign_bulk',case_id:cid,user_ids:ids,active:mode==='1',open:mode==='late-open'});await adminHome();notify(`${ids.length} öğrenci için işlem tamamlandı.`);});
    const selectAll=$('#select-all-students'),list=$('#assign-students');
    const renderStudents=()=>{
        const selectedCase=dash.cases.find(c=>c.id===getValue('a-case'));
        const assignments=new Map(dash.assignments.filter(a=>a.case_id===selectedCase.id).map(a=>[a.user_id,a]));
        const eligible=students.filter(u=>isSuperAdmin()||u.owner_id===me.id||Number(assignments.get(u.id)?.active));
        const checked=new Set([...list.querySelectorAll('[data-assign-student]:checked')].map(n=>n.value));
        list.innerHTML=eligible.map(u=>{
            const a=assignments.get(u.id);
            const status=a&&Number(a.active)?(Number(a.allow_late)?'Atanmış / Ek süre açık':'Atanmış'):'Atanmamış';
            return `<label class="inline-check"><input type="checkbox" data-assign-student value="${esc(u.id)}" ${checked.has(u.id)?'checked':''}>${esc(u.login)} · ${esc(u.name)} <small data-assignment-status="${esc(u.id)}">· ${status}</small></label>`;
        }).join('')||'<p>Bu vakaya atanabilecek öğrenci yok.</p>';
        const selected=eligible.filter(u=>checked.has(u.id)).length;
        selectAll.checked=selected>0&&selected===eligible.length;
        selectAll.indeterminate=selected>0&&selected<eligible.length;
        selectAll.disabled=!eligible.length;
        $('#commit').disabled=!eligible.length;
    };
    renderStudents();
    $('#a-case').onchange=renderStudents;
    selectAll.onchange=()=>list.querySelectorAll('[data-assign-student]').forEach(n=>n.checked=selectAll.checked);
    list.onchange=()=>{
        const all=[...list.querySelectorAll('[data-assign-student]')],selected=all.filter(n=>n.checked).length;
        selectAll.checked=selected===all.length;
        selectAll.indeterminate=selected>0&&selected<all.length;
    };
}
function definitionForm(family,d){
    if(family==='rubric'){rubricForm(d);return;}
    showDialog(`<h2>Etiket ${d?'düzenle':'ekle'}</h2>${d?'':definitionOwnerField()}${field('d-name','Ad',d?.name)}${select('d-target-role','Etiket grubu',[['','Grup seçin'],['user','Öğrenci mesajı'],['assistant','AI yanıtı']],d?JSON.parse(d.body).target_role||'':'')}<p class="muted">Bu etiket yalnız seçtiğiniz gruptaki mesajlarda kullanılabilir.</p>${select('d-topic-scope','Konu kapsamı (öğrenci etiketleri)',[['','Kapsam seçin'],['pedagogical','Pedagojik vaka ve kavramlar'],['outside','Pedagoji dışındaki talepler'],['any','Her iki kapsam']],d?JSON.parse(d.body).topic_scope||'':'pedagogical')}${field('d-body','Açıklama',d?JSON.parse(d.body).description:'','textarea')}<button class="spaced" id="commit">Kaydet</button>`,async()=>{await api('admin',{action:'definition',family:'label',name:getValue('d-name'),body:{description:getValue('d-body'),target_role:getValue('d-target-role'),topic_scope:getValue('d-topic-scope')||null},parent_id:d?.id||null,owner_id:getValue('workspace-owner')||me.id});await adminHome();});
}
function rubricForm(def){
    const original=def?JSON.parse(def.body):{text:'',dimensions:[]};
    showDialog(`<h2>${def?'Rubriği düzenle':'Rubrik oluştur'}</h2>${def?'':definitionOwnerField()}<p class="muted">1. Rubriğe ad ve açıklama verin. 2. Ölçmek istediğiniz boyutları ekleyin. 3. Her boyutun düzeylerini puan ve açıklamalarıyla tanımlayın. Sistem teknik yapıyı kendisi oluşturur.</p>${field('d-name','Rubrik adı',def?.name)}${field('r-text','Genel değerlendirme yönergesi',original.text,'textarea')}<p class="muted">Örneğin hangi kanıtların değerlendirileceğini ve puanlama yaklaşımını açıklayın.</p><div id="rubric-dimensions"></div><button type="button" class="secondary spaced" id="add-dimension">+ Değerlendirme boyutu ekle</button><p class="muted">Ağırlık 1 tüm boyutları eşit etkiler. Ağırlık 2, aynı puan ölçeğindeki bir boyutun ortalamaya iki kat etki etmesini sağlar.</p><p id="rubric-count" aria-live="polite"></p><button class="spaced" id="commit">Rubriği kaydet</button>`,async()=>{
        const error=(el,message)=>{el.setAttribute('aria-invalid','true');el.focus();throw new Error(message);};
        document.querySelectorAll('dialog [aria-invalid]').forEach(el=>el.removeAttribute('aria-invalid'));
        if(!getValue('d-name').trim())error($('#d-name'),'Rubrik adını doldurun.');
        if(!getValue('r-text').trim())error($('#r-text'),'Genel değerlendirme yönergesini doldurun.');
        const readText=(row,key,label)=>{const el=row.querySelector(`[data-rubric-field="${key}"]`),value=el.value.trim();if(!value)error(el,label+' alanını doldurun.');return value;};
        const readNumber=(row,key,label)=>{const el=row.querySelector(`[data-rubric-field="${key}"]`);if(el.value.trim()===''||!Number.isFinite(Number(el.value)))error(el,label+' için geçerli bir sayı girin.');return Number(el.value);};
        const dimensions=[...$('#rubric-dimensions').children].map((row,index)=>{
            const label=`${index+1}. boyut`,name=readText(row,'name',label+' adı'),min=readNumber(row,'min',label+' en düşük puan'),max=readNumber(row,'max',label+' en yüksek puan'),weight=readNumber(row,'weight',label+' ağırlık');
            if(max<=min)error(row.querySelector('[data-rubric-field="max"]'),label+': en yüksek puan en düşük puandan büyük olmalı.');
            if(weight<=0)error(row.querySelector('[data-rubric-field="weight"]'),label+': ağırlık sıfırdan büyük olmalı.');
            const levelNames=new Set();
            const levels=[...row.querySelector('.rubric-levels').children].map((level,i)=>{
                const prefix=label+` ${i+1}. düzey`,name=readText(level,'level-name',prefix+' adı'),score=readNumber(level,'score',prefix+' puanı'),description=readText(level,'description',prefix+' açıklaması');
                if(score<min||score>max)error(level.querySelector('[data-rubric-field="score"]'),prefix+': puan boyut aralığı içinde olmalı.');
                if(levelNames.has(name))error(level.querySelector('[data-rubric-field="level-name"]'),label+': düzey adları birbirinden farklı olmalı.');levelNames.add(name);
                return {...level.rubricSource,name,score,description};
            });
            return {...row.rubricSource,id:row.rubricSource.id,name,min,max,weight,levels};
        });
        await api('admin',{action:'definition',family:'rubric',name:getValue('d-name').trim(),body:{...original,text:getValue('r-text').trim(),dimensions},parent_id:def?.id||null,owner_id:getValue('workspace-owner')||me.id});await adminHome();notify('Rubrik kaydedildi.');
    });
    const makeField=(key,label,value,type='text',placeholder='')=>{const id='rubric-'+crypto.randomUUID();return `<label for="${id}">${label}</label>${type==='textarea'?`<textarea id="${id}" data-rubric-field="${key}" placeholder="${esc(placeholder)}">${esc(value??'')}</textarea>`:`<input id="${id}" data-rubric-field="${key}" type="${type}" ${type==='number'?'step="any"':''} value="${esc(value??'')}" placeholder="${esc(placeholder)}">`}`;};
    const update=()=>{const dimensions=[...$('#rubric-dimensions').children];dimensions.forEach((row,i)=>{row.querySelector('legend').textContent=`${i+1}. Değerlendirme boyutu`;row.querySelector('[data-remove-dimension]').disabled=dimensions.length===1;const levels=[...row.querySelector('.rubric-levels').children];levels.forEach((l,n)=>{l.querySelector('[data-level-title]').textContent=`${n+1}. başarı düzeyi`;l.querySelector('[data-remove-level]').disabled=levels.length===1;});});$('#rubric-count').textContent=`${dimensions.length} boyut · ${document.querySelectorAll('[data-rubric-level]').length} başarı düzeyi`;};
    const addLevel=(row,level={name:'',score:'',description:''})=>{const node=document.createElement('div');node.className='rubric-level';node.setAttribute('data-rubric-level','');node.rubricSource=structuredClone(level);node.innerHTML=`<div class="toolbar"><h4 data-level-title></h4><button type="button" class="secondary" data-remove-level>Düzeyi kaldır</button></div><div class="grid"><div>${makeField('level-name','Düzey adı',level.name,'text','Örn. Gelişiyor')}</div><div>${makeField('score','Bu düzeyin puanı',level.score,'number')}</div></div>${makeField('description','Bu düzeyde beklenen performans',level.description,'textarea','Bu puanı hangi kanıt veya davranış hak eder?')}`;node.querySelector('[data-remove-level]').onclick=()=>{node.remove();update();};row.querySelector('.rubric-levels').append(node);};
    const addDimension=(dimension={id:'dimension_'+crypto.randomUUID().replaceAll('-',''),name:'',min:0,max:4,weight:1,levels:[{name:'',score:0,description:''},{name:'',score:4,description:''}]})=>{
        const row=document.createElement('fieldset');row.className='rubric-dimension';row.setAttribute('data-rubric-dimension','');row.rubricSource=structuredClone(dimension);
        row.innerHTML=`<legend></legend><div class="toolbar"><p class="muted">Bir boyut, ayrı değerlendirdiğiniz bir ölçüttür.</p><button type="button" class="secondary" data-remove-dimension>Boyutu kaldır</button></div>${makeField('name','Boyut adı',dimension.name,'text','Örn. Gerekçelendirme')}<div class="rubric-numbers"><div>${makeField('min','En düşük puan',dimension.min,'number')}</div><div>${makeField('max','En yüksek puan',dimension.max,'number')}</div><div>${makeField('weight','Ağırlık',dimension.weight,'number')}</div></div><h3>Başarı düzeyleri</h3><div class="rubric-levels"></div><button type="button" class="secondary" data-add-level>+ Başarı düzeyi ekle</button>`;
        dimension.levels.forEach(l=>addLevel(row,l));row.querySelector('[data-add-level]').onclick=()=>{addLevel(row);update();};row.querySelector('[data-remove-dimension]').onclick=()=>{row.remove();update();};$('#rubric-dimensions').append(row);update();
    };
    if(original.dimensions.length)original.dimensions.forEach(d=>addDimension(d));else addDimension();$('#add-dimension').onclick=()=>addDimension();
}
