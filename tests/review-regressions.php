<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';require __DIR__.'/Seed.php';
$c=require __DIR__.'/../config/example.php';$c['dsn']='sqlite::memory:';$c['db_user']='';$c['db_password']='';$c['https']=false;
if(getenv('VAKALAB_TEST_DSN')){demand(str_contains(getenv('VAKALAB_TEST_DSN'),'_test_')&&str_contains(getenv('VAKALAB_TEST_DSN'),'host=127.0.0.1;'),'Only disposable local test databases');$c['dsn']=getenv('VAKALAB_TEST_DSN');$c['db_user']=getenv('VAKALAB_TEST_USER');$c['db_password']=getenv('VAKALAB_TEST_PASSWORD');}
$db=new Db($c);Migration::run($db);Seed::run($db,$c,uid());$app=new App($db,$c);$admin=new Admin($app);$ex=new Export($app);$n=0;
function regressionCheck(bool $ok,string $name): void {global $n;demand($ok,$name);echo 'PASS REVIEW '.(++$n).' '.$name.PHP_EOL;}
function regressionDenied(callable $fn,string $name): void {try{$fn();}catch(Fault $e){regressionCheck($e->getCode()===409,$name);return;}throw new RuntimeException('Not denied: '.$name);}
function privateFieldsHidden(array $a,string $name): void {regressionCheck(!array_intersect(['excluded','exclusion_reason','assigned','stop_access','case_test','practice','case_status'],array_keys($a))&&!str_contains(js($a),'PRIVATE_REVIEW_REASON'),$name);}
$r=$db->one("SELECT * FROM users WHERE id='test-researcher'");$s=$db->one("SELECT * FROM users WHERE id='test-student-1'");
$a=$app->start($s,'test-case-3');privateFieldsHidden($a,'new start uses student projection');
$admin->mutate($r,'exclude',['id'=>$a['id'],'excluded'=>true,'reason'=>'PRIVATE_REVIEW_REASON']);
privateFieldsHidden($app->start($s,'test-case-3'),'existing start hides researcher fields');
$a=$app->save($s,$a['id'],['revision'=>0,'draft'=>Rules::draft([])]);privateFieldsHidden($a,'autosave hides researcher fields');
$d=Rules::draft(['knowledge'=>5,'confidence'=>5,'difficulty'=>5,'severity'=>5,'problem'=>'Synthetic problem','decision'=>'Synthetic decision','reason'=>'Synthetic reason']);
$a=$app->save($s,$a['id'],['revision'=>0,'draft'=>$d],'initial');privateFieldsHidden($a,'initial confirmation hides researcher fields');
privateFieldsHidden($app->save($s,$a['id'],[],'initial'),'repeated initial confirmation hides researcher fields');
$key='regression-request-20261026';$one=$app->chat($s,$a['id'],['key'=>$key,'text'=>'FIRST QUESTION']);
$two=$app->chat($s,$a['id'],['key'=>$key,'text'=>' FIRST QUESTION ']);regressionCheck($one['id']===$two['id'],'same normalized message and key remain idempotent');
regressionDenied(fn()=>$app->chat($s,$a['id'],['key'=>$key,'text'=>'DIFFERENT QUESTION']),'reused key with different text returns conflict');
regressionCheck((int)$db->one('SELECT COUNT(*) n FROM ai_requests WHERE app_id=?',[$a['id']])['n']===1,'key conflict creates no second request');
$d=array_replace($d,['final_decision'=>'Synthetic final decision','final_reason'=>'Synthetic final reason','reflection'=>'Synthetic reflection','final_confidence'=>6,'final_difficulty'=>4,'final_severity'=>5]);
$a=$app->save($s,$a['id'],['revision'=>$a['revision'],'draft'=>$d],'submit');privateFieldsHidden($a,'submission hides researcher fields');
privateFieldsHidden($app->save($s,$a['id'],[],'submit'),'repeated submission hides researcher fields');privateFieldsHidden($app->view($s,$a['id'])['application'],'student view hides researcher fields');
regressionCheck($app->view($r,$a['id'])['application']['exclusion_reason']==='PRIVATE_REVIEW_REASON','researcher retains private reason');

$s6=$db->one("SELECT * FROM users WHERE id='test-student-6'");$a6=$db->one("SELECT * FROM applications WHERE user_id=? AND case_id='test-case-1'",[$s6['id']]);
$admin->mutate($r,'definition',['family'=>'label','name'=>'Synthetic description regression','body'=>['target_role'=>'user','topic_scope'=>'pedagogical','description'=>'PRIVATE_LABEL_DESCRIPTION','nested'=>['extra'=>'PRIVATE_NESTED_BODY']]]);
(new MessageLabels($app))->generate($r,$a6['id'],'user');
$raw=$ex->dataset($r,['app_id'=>$a6['id'],'include_test'=>1]);$coded=$ex->dataset($r,['app_id'=>$a6['id'],'include_test'=>1,'coded'=>1]);
regressionCheck(str_contains(js($raw),'PRIVATE_LABEL_DESCRIPTION')&&str_contains(js($raw),'PRIVATE_NESTED_BODY'),'raw export preserves historical definition text');
regressionCheck(!str_contains(js($coded),'PRIVATE_LABEL_DESCRIPTION')&&!str_contains(js($coded),'PRIVATE_NESTED_BODY'),'coded export removes nested definition descriptions');
regressionCheck(count($raw['label_batches'])===count($coded['label_batches']),'coded export retains batch history');
foreach($coded['label_batches'] as $i=>$batch){$detail=obj($batch['detail']);$original=obj($raw['label_batches'][$i]['detail']);regressionCheck(($detail['labels']??[])===($original['labels']??[])&&!isset($detail['label_definitions']),'coded labels retain original selections without free text');}
$mid=$db->one("SELECT id FROM messages WHERE app_id=? AND role='user'",[$a6['id']])['id'];
regressionCheck(MessageLabels::state($mid,$coded['labels'],$coded['label_reviews'],$coded['label_batches'])===MessageLabels::state($mid,$raw['labels'],$raw['label_reviews'],$raw['label_batches']),'coded selection precedence matches raw export');

$analysis=new Analysis($app);$run=$analysis->newRun($r,$a6['id'],'test-rubric-v1',2);
foreach(Review::ORDER as $stepNo){$step=$analysis->step($r,$run['id'],$stepNo);demand($step['status']==='completed','Fixture step');if(in_array($stepNo,Review::HUMAN,true))$analysis->decide($r,$step['id'],['action'=>'accept']);}
$report=$analysis->report($r,'create',['run_id'=>$run['id']]);$admin->mutate($r,'collection',['case_id'=>'test-case-1','closed'=>true]);
$analysis->report($r,'approve',['id'=>$report['id']]);$analysis->report($r,'publish',['id'=>$report['id']]);regressionCheck((bool)$ex->reportData($s6,$report['id'])['published_at'],'current v2 report remains compatible with approve and publish');
$step=$analysis->currentStep($run['id'],3);$analysis->decide($r,$step['id'],['action'=>'reject']);
foreach(['show','approve','publish'] as $action)regressionDenied(fn()=>$analysis->report($r,$action,['id'=>$report['id']]),'changed decisions block '.$action);
$db->q('UPDATE reports SET approved_at=? WHERE id=?',[now(),$report['id']]);
regressionDenied(fn()=>$analysis->report($r,'publish',['id'=>$report['id']]),'historically approved stale report cannot publish');
regressionCheck($db->one('SELECT published_at FROM reports WHERE id=?',[$report['id']])['published_at']===null,'failed publication stays withdrawn');
$analysis->step($r,$run['id'],8);$report=$analysis->report($r,'create',['run_id'=>$run['id']]);$analysis->report($r,'show',['id'=>$report['id']]);
regressionCheck($ex->reportData($s6,$report['id'])['report']['sections']['initial']['total']===null,'rebuilt report reflects rejected initial score');
$analysis->newRun($r,$a6['id'],'test-rubric-v1',2);
foreach(['show','approve','publish'] as $action)regressionDenied(fn()=>$analysis->report($r,$action,['id'=>$report['id']]),'newer evaluation blocks older report '.$action);
echo "TOTAL REVIEW REGRESSIONS $n checks passed; synthetic data only.\n";
