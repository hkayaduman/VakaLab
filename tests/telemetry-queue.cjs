'use strict';
const assert=require('node:assert/strict');
const fs=require('node:fs');
const path=require('node:path');
const vm=require('node:vm');
const {fitTelemetryEvent,telemetryBatch,telemetryPayloadBytes}=require('../public/assets/telemetry.js');

const quote=('ş🙂/\u2028\u2029').repeat(2500);
const copy={type:'copy',key:'copy-1',payload:{quote,length:quote.length}};
const next={type:'section_open',key:'next-1',section:'final'};
const fitted=fitTelemetryEvent(copy);
assert.equal(fitted.payload.length,quote.length);
assert.equal(fitted.payload.quote_truncated,true);
assert.ok(quote.startsWith(fitted.payload.quote)&&fitted.payload.quote.length>0);
assert.doesNotMatch(fitted.payload.quote,/[\uD800-\uDBFF]$/);
assert.ok(telemetryPayloadBytes(fitted.payload)<12000);
assert.equal(fitTelemetryEvent({type:'copy',payload:{quote:'kısa',length:4}}).payload.quote,'kısa');
const many=Array.from({length:100},()=>copy);
for(const newest of [false,true]){
    const chunk=telemetryBatch('app',newest?[...many,next]:[next,...many],newest);
    assert.ok(Buffer.byteLength(JSON.stringify({app_id:'app',events:chunk}))<=60000);
    assert.ok(chunk.length>1&&chunk.length<100);
    assert.ok(chunk.some(event=>event.key===next.key));
}
console.log('PASS telemetry quotes and outgoing batches are bounded in UTF-8 bytes');

const storage=new Map();
const sent=[];
const notice={};
const asset=file=>fs.readFileSync(path.join(__dirname,'../public/assets',file),'utf8');
let respond;
const ctx=vm.createContext({
    TextEncoder,URLSearchParams,
    crypto:{randomUUID:()=> 'test-tab'},
    fetch:async (url,request)=>{
        const body=JSON.parse(request.body);
        sent.push(body.events.map(entry=>entry.key));
        return respond(body.events);
    },
    setTimeout:()=>0,clearTimeout:()=>{},
    window:{addEventListener:()=>{}},
    document:{addEventListener:()=>{},querySelector:()=>notice},
    sessionStorage:{getItem:key=>storage.get(key)??null,setItem:(key,value)=>storage.set(key,value)}
});
vm.runInContext(asset('telemetry.js'),ctx);
vm.runInContext(asset('app.js').replace(/\binit\(\)\.catch\(e=>notify\(e\.message\)\);\s*$/,''),ctx);
vm.runInContext(asset('researcher.js'),ctx);
const markup=vm.runInContext("select('criterion','A < B & \"C\"', [['','Düzey seçin']])",ctx);
assert.match(markup,/>A &lt; B &amp; &quot;C&quot;<\/label>/);
console.log('PASS rubric label renders comparison signs and quotes as text');

const queueKey='vakalab:student:app:version:events';
ctx.entries=[copy,next];
vm.runInContext("me={id:'student'}; work={application:{id:'app'},version:{id:'version'}}; events=entries",ctx);
const ok=data=>({ok:true,json:async()=>({data})});
const remaining=()=>JSON.parse(storage.get(queueKey)).map(event=>event.key);

(async()=>{
    respond=()=>{throw new Error('offline');};
    await vm.runInContext('flushEvents()',ctx);
    assert.equal(vm.runInContext('events.length',ctx),2);

    respond=()=>ok({accepted:[next.key],rejected:[{index:0,reason:'Olay çok uzun'}]});
    await vm.runInContext('flushEvents()',ctx);
    assert.deepEqual(remaining(),[]);
    assert.match(notice.textContent,/Olay çok uzun/);

    vm.runInContext('events=entries',ctx);
    respond=chunk=>chunk.some(entry=>entry.key===copy.key)
        ?{ok:false,status:413,json:async()=>({error:'Request too large'})}
        :ok({accepted:chunk.map(entry=>entry.key),rejected:[]});
    await vm.runInContext('drainEvents()',ctx);
    assert.deepEqual(sent.slice(-3),[[copy.key,next.key],[copy.key],[next.key]]);
    assert.deepEqual(remaining(),[]);
    console.log('PASS queue retries offline, clears permanent rejects and delivers subsequent events');
})().catch(error=>{console.error(error);process.exitCode=1;});
