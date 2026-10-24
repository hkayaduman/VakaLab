// Real HTTP/PHP fatal-error regression, using only a disposable local database.
const fs=require('node:fs'),path=require('node:path'),crypto=require('node:crypto'),assert=require('node:assert/strict');
const {spawn}=require('node:child_process');
const root=path.resolve(__dirname,'..'),dir=path.join(root,'tests/tmp/runtime-'+crypto.randomUUID());fs.mkdirSync(dir,{recursive:true});
const php=process.env.PHP_BIN||path.join(root,'.tools/php/php.exe'),url='http://127.0.0.1:18779/';
fs.writeFileSync(path.join(dir,'index.php'),`<?php
require '${root.replaceAll('\\','/')}/app/bootstrap.php';
ApiRuntime::start();header('Content-Type: application/json');
$db=new Db(['dsn'=>'sqlite:'.__DIR__.'/runtime.sqlite']);
$db->q('CREATE TABLE IF NOT EXISTS analysis_steps (id TEXT PRIMARY KEY,status TEXT,error TEXT)');
$mode=$_GET['mode']??'health';
if($mode==='health'){echo js(['limit'=>(int)ini_get('max_execution_time')]);exit;}
if($mode==='status'){echo js($db->all('SELECT * FROM analysis_steps ORDER BY id'));exit;}
$db->q("INSERT OR IGNORE INTO analysis_steps VALUES ('preserved','completed',NULL)");
if($mode==='budget'){
 ApiRuntime::providerBudget(2);sleep(2);ApiRuntime::providerBudget(2);sleep(2);
 echo js(['limit'=>(int)ini_get('max_execution_time'),'completed'=>true]);exit;
}
$db->q("INSERT INTO analysis_steps VALUES (?,'running',NULL)",[$mode]);ApiRuntime::trackStep($db,$mode);
if($mode==='fatal')trigger_error('PRIVATE_FATAL_TEST_DETAIL',E_USER_ERROR);
if($mode==='timeout'){set_time_limit(1);while(true){hash('sha256','synthetic cpu work');}}
`);
const log=fs.openSync(path.join(dir,'server.log'),'w');
const server=spawn(php,['-d','max_execution_time=1','-d','display_errors=1','-S','127.0.0.1:18779','-t',dir],{cwd:root,windowsHide:true,stdio:['ignore',log,log]});
const get=async mode=>{const r=await fetch(url+'?mode='+mode,{signal:AbortSignal.timeout(12000)}),text=await r.text();return {status:r.status,text,data:JSON.parse(text)};};
(async()=>{try{
 let ready=false;for(let i=0;i<60;i++){try{await get('health');ready=true;break;}catch{}await new Promise(r=>setTimeout(r,100));}assert(ready);
 assert.equal((await get('health')).data.limit,1);
 const budget=await get('budget');assert.equal(budget.status,200);assert.equal(budget.data.completed,true);assert(budget.data.limit>=17);
 const fatal=await get('fatal');assert.equal(fatal.status,500);assert.equal(typeof fatal.data.error,'string');assert(!fatal.text.includes('PRIVATE_FATAL_TEST_DETAIL'));assert(!fatal.text.includes('<br'));
 const timeout=await get('timeout');assert.equal(timeout.status,500);assert(timeout.data.error.includes('süresi'));assert(!timeout.text.includes('<br'));
 const rows=(await get('status')).data;assert.equal(rows.find(r=>r.id==='fatal').status,'failed');assert.equal(rows.find(r=>r.id==='timeout').error,'execution_timeout');assert.equal(rows.find(r=>r.id==='preserved').status,'completed');
 console.log('TOTAL API RUNTIME: per-call deadline reset, fatal JSON, timeout JSON, owned-step recovery and completed-result preservation passed. Isolated PHP HTTP/SQLite; no provider call.');
}finally{server.kill();fs.closeSync(log);}})().catch(e=>{console.error(e);process.exitCode=1;});
