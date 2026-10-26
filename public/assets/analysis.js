'use strict';
function analysisProgress(runId, view) {
    if(Number(view.analysis_runs?.find(r=>r.id===runId)?.review_version)===2)return reviewProgress(runId,view);
    const steps = view.steps.filter(s => s.run_id === runId);
    const latestComplete = n => steps.filter(s => Number(s.step) === n && s.status === 'completed').sort((a,b)=>Number(b.attempt)-Number(a.attempt))[0];
    const latestDecision = step => step&&view.decisions.filter(d=>d.step_id===step.id).at(-1);
    const merge=latestComplete(8);
    let mergeCurrent=false;
    if(merge){
        let output;try{output=typeof merge.output==='string'?JSON.parse(merge.output):merge.output;}catch{}
        mergeCurrent=[3,4,5,6,7].every(n=>{const decision=latestDecision(latestComplete(n));return decision&&(output?.parts||[]).some(p=>Number(p.step)===n&&p.decision_id===decision.id);});
    }
    const complete = n => n===8?(mergeCurrent?merge:undefined):latestComplete(n);
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
        let status = latest?.status === 'running' ? 'running' : done ? 'completed' :
            !running && n === next && (n !== 8 || !missingDecisions.length) ? 'ready' : 'locked';
        return {n, status, attempts, done, latest, missingDecisions};
    });
}
function reviewProgress(runId,view){
    const order=[1,2,3,5,4,6,8],human=[2,3,4,6],steps=view.steps.filter(s=>s.run_id===runId);
    const completed=n=>steps.filter(s=>Number(s.step)===n&&s.status==='completed').sort((a,b)=>Number(b.attempt)-Number(a.attempt))[0];
    const decision=n=>{const s=completed(n);return s&&view.decisions.filter(d=>d.step_id===s.id).at(-1);};
    const missing=human.filter(n=>!decision(n));let merge=completed(8),output;
    try{output=typeof merge?.output==='string'?JSON.parse(merge.output):merge?.output;}catch{}
    if(!human.every(n=>decision(n)&&(output?.parts||[]).some(p=>Number(p.step)===n&&p.decision_id===decision(n).id)))merge=undefined;
    const done=n=>n===8?merge:completed(n),next=order.find(n=>!done(n)),running=steps.some(s=>s.status==='running');
    return order.map(n=>{const attempts=steps.filter(s=>Number(s.step)===n),latest=[...attempts].sort((a,b)=>Number(b.attempt)-Number(a.attempt))[0],result=done(n);return {n,attempts,latest,done:result,missingDecisions:missing,status:latest?.status==='running'?'running':result?'completed':!running&&n===next&&(n!==8||!missing.length)?'ready':'locked'};});
}
// Each step is a separate request. Never retry an uncertain request automatically.
async function runSequentialAnalysis(runId, {load, execute, update = async()=>{}, canContinue = ()=>true}) {
    for(let i=0;i<8;i++) {
        if(!canContinue())return {status:'paused'};
        const view=await load(), progress=analysisProgress(runId,view);
        if(!canContinue())return {status:'paused'};
        if(progress.some(p=>p.status==='running'))return {status:'running'};
        const next=progress.find(p=>!p.done);
        if(!next)return {status:'completed'};
        if(next.n===8&&next.missingDecisions.length)return {status:'review',missing:next.missingDecisions};
        if(next.status!=='ready')return {status:'paused'};
        await update(view,{step:next.n,phase:'running'});
        if(!canContinue())return {status:'paused'};
        const result=await execute(next.n);
        await update(null,{step:next.n,phase:result.status});
        if(result.status!=='completed')return {status:'failed',step:next.n};
    }
    return {status:'completed'};
}
function analysisViewVersion(view) {
    const rows=(values,keys)=>(values||[]).map(row=>keys.map(key=>row[key]??null)).sort((a,b)=>String(a[0]).localeCompare(String(b[0])));
    return JSON.stringify([rows(view.analysis_runs,['id','review_version']),rows(view.steps,['id','run_id','step','attempt','status','error']),rows(view.decisions,['id','step_id']),rows(view.reports,['id','run_id','version','approved_at','published_at'])]);
}
// Observe existing work only. Never execute or retry an AI request here.
function observeAnalysis({load,isCurrent,hasWork,onChange,initial,interval=2000}) {
    let stopped=false,timer,version=analysisViewVersion(initial),pending=false;
    const schedule=()=>{if(!stopped)timer=setTimeout(tick,interval);};
    const stop=()=>{stopped=true;clearTimeout(timer);};
    async function tick(){
        if(stopped||!isCurrent()){stop();return;}
        try{
            const view=await load();
            if(stopped||!isCurrent()){stop();return;}
            const next=analysisViewVersion(view);
            if(next!==version){pending=(await onChange(view))===false;if(!pending)version=next;}
            if(!pending&&!hasWork(view)){stop();return;}
        }catch{
            // A temporary read failure must not repeat a paid operation.
            if(!isCurrent()){stop();return;}
        }
        schedule();
    }
    schedule();
    return {stop};
}
if (typeof module !== 'undefined') module.exports = {analysisProgress,reviewProgress,runSequentialAnalysis,analysisViewVersion,observeAnalysis};
