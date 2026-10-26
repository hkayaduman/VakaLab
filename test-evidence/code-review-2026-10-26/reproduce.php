<?php
declare(strict_types=1);
require __DIR__.'/../../app/bootstrap.php';
require __DIR__.'/../../tests/Seed.php';
$c=require __DIR__.'/../../config/example.php';
$c['dsn']='sqlite::memory:';$c['db_user']='';$c['db_password']='';$c['https']=false;
$db=new Db($c);Migration::run($db);Seed::run($db,$c,uid());
$app=new App($db,$c);$admin=new Admin($app);$ex=new Export($app);
$r=$db->one("SELECT * FROM users WHERE id='test-researcher'");
$s=$db->one("SELECT * FROM users WHERE id='test-student-1'");
$a=$app->start($s,'test-case-3');
$marker='PRIVATE_RESEARCHER_REASON_20261026';
$admin->mutate($r,'exclude',['id'=>$a['id'],'excluded'=>true,'reason'=>$marker]);
$view=$app->view($s,$a['id']);
$start=$app->start($s,'test-case-3');
$saved=$app->save($s,$a['id'],['revision'=>0,'draft'=>Rules::draft([])]);
echo js(['finding'=>'student_private_fields','view_hides_reason'=>!isset($view['application']['exclusion_reason']),'start_leaks_reason'=>($start['exclusion_reason']??null)===$marker,'save_leaks_reason'=>($saved['exclusion_reason']??null)===$marker]).PHP_EOL;

$s6=$db->one("SELECT * FROM users WHERE id='test-student-6'");
$a6=$db->one("SELECT * FROM applications WHERE user_id=? AND case_id='test-case-1'",[$s6['id']]);
$label=$admin->mutate($r,'definition',['family'=>'label','name'=>'Synthetic private description','body'=>['target_role'=>'user','topic_scope'=>'pedagogical','description'=>'PRIVATE_LABEL_DESCRIPTION_20261026']]);
(new MessageLabels($app))->generate($r,$a6['id'],'user');
$coded=$ex->dataset($r,['app_id'=>$a6['id'],'include_test'=>1,'coded'=>1]);
echo js(['finding'=>'coded_export_text','private_description_present'=>str_contains(js($coded),'PRIVATE_LABEL_DESCRIPTION_20261026'),'path'=>'label_batches[].detail.label_definitions[].description/body.description']).PHP_EOL;

$analysis=new Analysis($app);$run=$analysis->newRun($r,$a6['id'],'test-rubric-v1',2);
foreach(Review::ORDER as $n){$step=$analysis->step($r,$run['id'],$n);demand($step['status']==='completed','Fixture step failed');if(in_array($n,Review::HUMAN,true))$analysis->decide($r,$step['id'],['action'=>'accept']);}
$report=$analysis->report($r,'create',['run_id'=>$run['id']]);
$admin->mutate($r,'collection',['case_id'=>'test-case-1','closed'=>true]);
$analysis->report($r,'show',['id'=>$report['id']]);
$step=$analysis->currentStep($run['id'],3);
$analysis->decide($r,$step['id'],['action'=>'reject']);
$normalBlocked=false;
try{$analysis->report($r,'show',['id'=>$report['id']]);}catch(Fault $e){$normalBlocked=$e->getCode()===409;}
$analysis->report($r,'approve',['id'=>$report['id']]);
$analysis->report($r,'publish',['id'=>$report['id']]);
$visible=$ex->reportData($s6,$report['id']);
echo js(['finding'=>'stale_v2_publication','normal_show_blocks'=>$normalBlocked,'legacy_approve_publish_bypasses'=>!!$visible['published_at'],'published_initial_score'=>$visible['report']['sections']['initial']['total'],'latest_decision'=>'reject']).PHP_EOL;

$d=Rules::draft(['knowledge'=>5,'confidence'=>5,'difficulty'=>5,'severity'=>5,'problem'=>'Synthetic problem','decision'=>'Synthetic decision','reason'=>'Synthetic reason']);
$app->save($s,$a['id'],['revision'=>0,'draft'=>$d],'initial');
$key='review-unique-request-20261026';
$one=$app->chat($s,$a['id'],['key'=>$key,'text'=>'FIRST SYNTHETIC MESSAGE']);
$two=$app->chat($s,$a['id'],['key'=>$key,'text'=>'SECOND DIFFERENT SYNTHETIC MESSAGE']);
echo js(['finding'=>'changed_message_reused_key','same_request'=>$one['id']===$two['id'],'second_message_persisted'=>(bool)$db->one('SELECT id FROM messages WHERE app_id=? AND content=?',[$a['id'],'SECOND DIFFERENT SYNTHETIC MESSAGE'])]).PHP_EOL;
