'use strict';
function analysisProgress(runId, view) {
    const steps = view.steps.filter(s => s.run_id === runId);
    const complete = n => steps.find(s => Number(s.step) === n && s.status === 'completed');
    const running = steps.some(s => s.status === 'running');
    const next = Array.from({length: 8}, (_, i) => i + 1).find(n => !complete(n));
    const missingDecisions = [3, 4, 5, 6, 7].filter(n => {
        const s = complete(n);
        return !s || !view.decisions.some(d => d.step_id === s.id);
    });
    return Array.from({length: 8}, (_, i) => {
        const n = i + 1, attempts = steps.filter(s => Number(s.step) === n);
        const latest = [...attempts].sort((a,b) => Number(b.attempt) - Number(a.attempt))[0];
        const done = complete(n);
        let status = done ? 'completed' : latest?.status === 'running' ? 'running' :
            !running && n === next && (n !== 8 || !missingDecisions.length) ? 'ready' : 'locked';
        return {n, status, attempts, done, latest, missingDecisions};
    });
}
if (typeof module !== 'undefined') module.exports = {analysisProgress};
