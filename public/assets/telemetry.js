'use strict';

const telemetryEncoder=new TextEncoder();
const telemetryPayloadLimit=12000;
const telemetryBatchLimit=60000;
const telemetryQuoteLimit=6000;
let telemetryDatabase;

async function telemetryStore(mode,action){
    if(typeof indexedDB==='undefined')return;
    telemetryDatabase??=new Promise((resolve,reject)=>{
        const directory=new URL('.',location.href).pathname;
        const request=indexedDB.open('vakalab-telemetry:'+directory,1);
        request.onupgradeneeded=()=>{
            const store=request.result.createObjectStore('events',{keyPath:['owner','appId','event.key']});
            store.createIndex('owner','owner');
        };
        request.onsuccess=()=>resolve(request.result);
        request.onerror=()=>reject(request.error);
        request.onblocked=()=>reject(new Error('Olay kuyruğuna erişilemiyor'));
    });
    try{
        const db=await telemetryDatabase;
        return await new Promise((resolve,reject)=>{
            const tx=db.transaction('events',mode);
            const request=action(tx.objectStore('events'));
            tx.oncomplete=()=>resolve(request?.result);
            tx.onabort=()=>reject(tx.error);
        });
    }catch{
        // The current tab still has its sessionStorage queue when IndexedDB is unavailable.
    }
}

function persistTelemetry(owner,appId,entries){
    return telemetryStore('readwrite',store=>{
        for(const event of entries)store.put({owner,appId,event});
    });
}

function deleteTelemetry(owner,appId,keys){
    return telemetryStore('readwrite',store=>{
        for(const key of keys)store.delete([owner,appId,key]);
    });
}

function readTelemetry(owner){
    return telemetryStore('readonly',store=>store.index('owner').getAll(owner));
}

function pendingTelemetryRanges(events){
    const streams=new Map();
    for(const event of events){
        if(!event.session||!event.tab||!Number.isInteger(event.seq)||event.seq<1)continue;
        const key=JSON.stringify([event.session,event.tab]);
        if(!streams.has(key))streams.set(key,new Set());
        streams.get(key).add(event.seq);
    }
    const ranges=[];
    for(const [key,values] of streams){
        const [session,tab]=JSON.parse(key);
        let current;
        for(const seq of [...values].sort((a,b)=>a-b)){
            if(current&&seq===current.last+1){current.last=seq;continue;}
            if(ranges.length===48)return {ranges,truncated:true};
            current={session,tab,first:seq,last:seq};
            ranges.push(current);
        }
    }
    return {ranges,truncated:false};
}

// Match the PHP JSON encoder used for payloads stored in the event log.
function telemetryPayloadBytes(payload){
    const json=JSON.stringify(payload).replaceAll('/','\\/').replaceAll('\u2028','\\u2028').replaceAll('\u2029','\\u2029');
    return telemetryEncoder.encode(json).length;
}

function fitTelemetryEvent(entry){
    const original=entry?.payload;
    if(entry?.type!=='copy'||typeof original?.quote!=='string')return entry;
    const quote=original.quote;
    const payload={...original,length:original.length??quote.length};
    if(quote.length<=telemetryQuoteLimit&&telemetryPayloadBytes(payload)<telemetryPayloadLimit&&payload.length<=quote.length)return entry;

    payload.quote_truncated=true;
    let first=0,last=Math.min(quote.length,telemetryQuoteLimit);
    while(first<last){
        const middle=Math.ceil((first+last)/2);
        payload.quote=quote.slice(0,middle).replace(/[\uD800-\uDBFF]$/,'');
        if(telemetryPayloadBytes(payload)<telemetryPayloadLimit)first=middle;
        else last=middle-1;
    }
    payload.quote=quote.slice(0,first).replace(/[\uD800-\uDBFF]$/,'');
    return {...entry,payload};
}

function telemetryBatch(appId,queue,newest=false){
    const chunk=[];
    let bytes=telemetryEncoder.encode(JSON.stringify({app_id:appId,events:[]})).length;
    for(let i=0;i<Math.min(100,queue.length);i++){
        const entry=fitTelemetryEvent(queue[newest?queue.length-1-i:i]);
        const size=telemetryEncoder.encode(JSON.stringify(entry)).length+(i?1:0);
        if(bytes+size>telemetryBatchLimit){
            // A legacy event above the local batch budget can be handled individually by the regular sender.
            if(!newest&&!chunk.length)chunk.push(entry);
            break;
        }
        bytes+=size;
        chunk.push(entry);
    }
    return newest?chunk.reverse():chunk;
}

if(typeof module!=='undefined')module.exports={fitTelemetryEvent,telemetryBatch,telemetryPayloadBytes};
