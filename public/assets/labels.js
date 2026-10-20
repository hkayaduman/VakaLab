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
    const history = [...reviews.values()].sort((a, b) => a.created_at.localeCompare(b.created_at));
    history.forEach((r, i) => {
        r.ids = [...new Set(r.ids)];
        r.before = i ? history[i - 1].ids : [];
        r.added = r.ids.filter(id => !r.before.includes(id));
        r.removed = r.before.filter(id => !r.ids.includes(id));
    });
    return {history, current: history.at(-1)?.ids || [],
        ai: [...new Set(rows.filter(l => l.source === 'ai').map(l => l.definition_id))]};
}
if (typeof module !== 'undefined') module.exports = {labelReviewState};
