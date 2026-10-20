'use strict';
// Reviews are complete selections, including an explicitly empty selection.
function labelReviewState(view, messageId) {
    const rows = (view.labels || []).filter(l => l.message_id === messageId);
    const reviews = new Map((view.label_reviews || []).filter(r => r.message_id === messageId)
        .map(r => [r.id, {...r, ids: []}]));
    for (const row of rows.filter(l => l.source === 'researcher')) {
        if (!reviews.has(row.review_id)) reviews.set(row.review_id, {...row, id: row.review_id, ids: []});
        reviews.get(row.review_id).ids.push(row.definition_id);
    }
    const history = [...reviews.values()].sort((a, b) => a.created_at.localeCompare(b.created_at)||String(a.id).localeCompare(String(b.id)));
    history.forEach((r, i) => {
        r.ids = [...new Set(r.ids)];
        r.before = i ? history[i - 1].ids : [];
        r.added = r.ids.filter(id => !r.before.includes(id));
        r.removed = r.before.filter(id => !r.ids.includes(id));
    });
    const aiRows=rows.filter(l=>l.source==='ai').sort((a,b)=>String(a.created_at||'').localeCompare(String(b.created_at||''))||String(a.id||'').localeCompare(String(b.id||'')));
    const last=aiRows.at(-1);let ai=[...new Set(aiRows.filter(l=>l.review_id===last?.review_id).map(l=>l.definition_id))],aiKnown=!!last;
    for(const batch of [...(view.label_batches||[])].sort((a,b)=>a.created_at.localeCompare(b.created_at)||a.id.localeCompare(b.id))){const detail=typeof batch.detail==='string'?JSON.parse(batch.detail):batch.detail;if(detail.labels&&messageId in detail.labels&&(!last||batch.created_at>=last.created_at)){ai=detail.labels[messageId];aiKnown=true;}}
    return {history, current: history.at(-1)?.ids || [],ai,aiKnown};
}
if (typeof module !== 'undefined') module.exports = {labelReviewState};
