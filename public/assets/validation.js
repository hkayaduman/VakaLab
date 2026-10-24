'use strict';
// Required fields mirror the server rules; the server remains authoritative.
const requiredFields = {
    knowledge: {section:'profile', label:'Ön bilgi', score:true},
    confidence: {section:'profile', label:'Başlangıç güveni', score:true},
    difficulty: {section:'profile', label:'Algılanan zorluk', score:true},
    severity: {section:'profile', label:'Yanlış kararın sonucunun ciddiyeti', score:true},
    problem: {section:'initial', label:'Temel problem değerlendirmesi'},
    decision: {section:'initial', label:'Başlangıç kararı'},
    reason: {section:'initial', label:'Başlangıç kararının gerekçesi'},
    final_decision: {section:'final', label:'Nihai karar'},
    final_reason: {section:'final', label:'Nihai kararın gerekçesi'},
    final_confidence: {section:'final', label:'Nihai güven', score:true},
    final_difficulty: {section:'final', label:'Nihai algılanan zorluk', score:true},
    final_severity: {section:'final', label:'Nihai algılanan ciddiyet', score:true},
    reflection: {section:'reflection', label:'Yansıtma'}
};
let validationScope = null;
function missingFields(scope) {
    if (!work) return [];
    return Object.keys(requiredFields).filter(key => {
        const rule = requiredFields[key];
        if ((scope === 'initial') !== ['profile','initial'].includes(rule.section)) return false;
        const value = work.draft[key];
        return rule.score ? !Number.isInteger(value) || value < 0 || value > 10 : typeof value !== 'string' || !value.trim();
    });
}
function canSubmit() {
    return !!work && !!work.application.initial_at && !locked() && !busy &&
        !work.application.active_request && !saving && !dirty && navigator.onLine && missingFields('submit').length === 0;
}
function goToRequiredField(key, scope) {
    const rule = requiredFields[key];
    if (!rule || !work || locked()) return;
    validationScope = scope;
    scrolls[section] = window.scrollY;
    section = rule.section;
    renderWork();
    event('section_open', {reason:'validation', field:key});
    const input = document.getElementById(key);
    if (input) {
        input.focus({preventScroll:true});
        const headerHeight = document.querySelector('.workspace-top')?.getBoundingClientRect().height || 0;
        // Leave the label and field below the sticky navigation, including on mobile.
        window.scrollTo({top:Math.max(0,window.scrollY + input.getBoundingClientRect().top - headerHeight - 80), behavior:'instant'});
    }
}
function validateBeforeConfirm(scope) {
    const missing = missingFields(scope);
    if (!missing.length) return true;
    goToRequiredField(missing[0], scope);
    return false;
}
function updateValidationFeedback() {
    if (!work) return;
    const missing = validationScope ? missingFields(validationScope) : [];
    for (const [key, rule] of Object.entries(requiredFields)) {
        const input = document.getElementById(key);
        if (!input) continue;
        const invalid = !input.disabled && missing.includes(key);
        input.classList.toggle('field-invalid', invalid);
        input.setAttribute('aria-invalid', String(invalid));
        const id = key + '-required-error';
        let message = document.getElementById(id);
        if (invalid) {
            if (!message) {
                message = document.createElement('p');
                message.id = id;
                message.className = 'field-error';
                input.after(message);
            }
            message.textContent = rule.score ? rule.label + ': 0–10 arasında bir değer seçin.' : rule.label + ': bu alanı doldurun.';
            input.setAttribute('aria-describedby', id);
        } else {
            if (message) message.remove();
            input.removeAttribute('aria-describedby');
        }
    }
    document.querySelectorAll('[data-section]').forEach(button => {
        const count = missing.filter(key => requiredFields[key].section === button.dataset.section).length;
        button.classList.toggle('has-field-errors', count > 0);
        let badge = button.querySelector('.error-count');
        if (count) {
            if (!badge) { badge = document.createElement('span'); badge.className = 'error-count'; button.append(badge); }
            badge.textContent = ' · ' + count + ' eksik';
        } else if (badge) badge.remove();
    });
    const summary = document.getElementById('validation-summary');
    if (summary) {
        const summaryMissing = section === 'confirm' && work.application.initial_at && work.application.state !== 'submitted' ? missingFields('submit') : [];
        summary.hidden = summaryMissing.length === 0;
        const html = summaryMissing.length ? '<strong>Eksik alanları tamamlayın</strong><p>Aşağıdaki bağlantılardan ilgili alana gidebilirsiniz.</p><ul>' +
            summaryMissing.map(key => '<li><button type="button" class="field-link" data-missing-field="' + key + '">' + esc(names[requiredFields[key].section] + ' → ' + requiredFields[key].label) + '</button></li>').join('') + '</ul>' : '';
        if (summary.innerHTML !== html) summary.innerHTML = html;
        summary.querySelectorAll('[data-missing-field]').forEach(button => button.onclick = () => goToRequiredField(button.dataset.missingField, 'submit'));
    }
    updateSubmitGate();
}
function updateSubmitGate() {
    if (!work) return;
    const button = document.getElementById('submit');
    if (button) button.disabled = !canSubmit();
    const help = document.getElementById('submit-help');
    if (!help) return;
    const a = work.application;
    const missing = missingFields('submit');
    if (a.state === 'submitted') help.textContent = 'Vaka teslim edildi. Yanıtlarınız yalnızca görüntülenebilir.';
    else if (locked()) help.textContent = 'Bu sekmede teslim yapılamaz. Salt okunur veya sürüm çakışması açıklamasını kontrol edin.';
    else if (!a.initial_at) help.textContent = 'Önce vaka profilinizi ve başlangıç değerlendirmenizi kesinleştirin.';
    else if (missing.length) {
        help.textContent = 'Son teslim için yukarıdaki zorunlu alanları tamamlayın.';
    } else if (busy || a.active_request) help.textContent = 'AI yanıtı sürüyor. Yanıt tamamlandığında teslim açılacaktır.';
    else if (!navigator.onLine) help.textContent = 'Bağlantı yok. Teslim için yeniden bağlanıp değişikliklerinizi kaydedin.';
    else if (dirty || saving) help.textContent = 'Son değişikliklerin sunucuya kaydedilmesi bekleniyor.';
    else help.textContent = 'Zorunlu alanlar tamamlandı ve sunucuya kaydedildi. Son teslim yapabilirsiniz.';
}
