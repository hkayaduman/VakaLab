<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';require __DIR__.'/Seed.php';
$c=require __DIR__.'/../config/example.php';$c['dsn']='sqlite::memory:';$c['db_user']='';$c['db_password']='';$c['https']=false;
$db=new Db($c);Migration::run($db);Seed::run($db,$c,uid());$app=new App($db,$c);$analysis=new Analysis($app);$n=0;
function verifyV2(bool $ok,string $name): void {global $n;demand($ok,$name);echo 'PASS '.(++$n).' '.$name.PHP_EOL;}
function denyV2(callable $fn,string $name): void {try{$fn();}catch(Fault){verifyV2(true,$name);return;}throw new RuntimeException($name);}
$r=$db->one("SELECT * FROM users WHERE id='test-researcher'");$u=$db->one("SELECT * FROM users WHERE id='test-student-6'");$a=$db->one('SELECT * FROM applications WHERE user_id=?',[$u['id']]);$old=$db->all('SELECT * FROM analysis_runs');$oldReports=$db->all('SELECT * FROM reports');
$db->q('DELETE FROM events WHERE app_id=?',[$a['id']]);$base=strtotime($a['submitted_at'])-200;$events=[];
foreach([[0,'ai'],[120,'final']] as $i=>[$seconds,$section])$events[]=['key'=>uid(),'type'=>'section_open','section'=>$section,'tab'=>'review-v2-fixture','seq'=>$i+1,'at'=>gmdate('Y-m-d\TH:i:s\Z',$base+$seconds),'payload'=>['view_id'=>'review-v2-time','app_active'=>true,'visible'=>true,'focused'=>true,'locked'=>false]];
(new Telemetry($app))->ingest($u,$a['id'],['events'=>$events],'review-v2-fixture');
$run=$analysis->newRun($r,$a['id'],'test-rubric-v1',2);$rid=$run['id'];verifyV2(Review::version($db,$rid)===2,'new contract recorded without schema change');
$steps=[];foreach([1,2,3,5,4,6] as $step){$steps[$step]=$analysis->step($r,$rid,$step);verifyV2($steps[$step]['status']==='completed','prepared section '.$step);}
$profile=obj($steps[2]['output']);verifyV2(isset($profile['feedback'],$profile['profile'])&&!isset($profile['score'],$profile['scores']),'profile has comparison and feedback without score');
verifyV2($profile['profile'][1]['change']===2&&$profile['profile'][0]['final_collected']===false&&$profile['profile'][0]['change']===null,'real confidence difference; no invented missing profile measurement');
foreach([3=>3,4=>2,6=>1] as $step=>$count){$out=obj($steps[$step]['output']);verifyV2(count($out['questions'])===$count,'separate question scores for section '.$step);foreach($out['questions'] as $id=>$q){verifyV2(array_keys($q['input']['sources'])===[$id]&&!isset($q['input']['profile'],$q['input']['messages']),'isolated answer payload '.$id);verifyV2($q['score']===0&&$q['max']===4&&strlen($q['feedback'])>10,'rubric score and improvement feedback '.$id);}}
$stats=obj($steps[5]['output']);verifyV2($stats['statistics']['seconds']===120&&$stats['statistics']['student_messages']===1&&$stats['statistics']['assistant_messages']===1,'chat counts and 120-second navigation interval are computed exactly');verifyV2(isset($stats['statistics']['student_messages'])&&!isset($stats['feedback'],$stats['questions'],$stats['scores'])&&$steps[5]['model']==='system','chat has only computed statistics without AI scoring/comment');
denyV2(fn()=>$analysis->decide($r,$steps[5]['id'],['action'=>'accept']),'chat statistics have no human score/feedback decision');
denyV2(fn()=>$analysis->step($r,$rid,5,true),'chat statistics cannot be sent to AI for rewriting');
$feedback='Başlangıç ve nihai güven değerleriniz arasında iki puanlık artış var; bu değişim tek başına başarı kanıtı değildir.';
$analysis->decide($r,$steps[2]['id'],['action'=>'edit','body'=>['feedback'=>$feedback]]);
foreach([3,4,6] as $step){
    $accepted=$analysis->decide($r,$steps[$step]['id'],['action'=>'accept']);
    $out=obj($steps[$step]['output']);$edited=['questions'=>[]];
    foreach($out['questions'] as $id=>$q){
        $criteria=$q['criteria'];foreach($criteria as &$criterion){$criterion['level']='Gelişiyor';$criterion['score']=2;$criterion['reason']='Karar kısmen somut vaka kanıtlarıyla destekleniyor.';}unset($criterion);
        $edited['questions'][$id]=['criteria'=>$criteria,'score'=>99,'feedback'=>'Bu sorudaki kararınızı somut vaka kanıtlarıyla ilişkilendirerek daha açık gerekçelendirebilirsiniz.'];
    }
    $decision=$analysis->decide($r,$steps[$step]['id'],['action'=>'edit','body'=>$edited]);
    verifyV2(obj($decision['body'])['questions'][array_key_first($edited['questions'])]['score']===2,'accepted AI result can be edited; aggregate is computed from criteria '.$step);
    verifyV2($db->one('SELECT body FROM decisions WHERE id=?',[$accepted['id']])['body']===$accepted['body'],'previous acceptance preserved '.$step);
    $invalid=$edited;$first=array_key_first($invalid['questions']);$criterion=array_key_first($invalid['questions'][$first]['criteria']);
    $invalid['questions'][$first]['criteria'][$criterion]['score']=4;
    denyV2(fn()=>$analysis->decide($r,$steps[$step]['id'],['action'=>'edit','body'=>$invalid]),'inconsistent researcher level and score rejected '.$step);
    $invalid=$edited;$invalid['questions'][$first]['criteria'][$criterion]['reason']='  ';
    denyV2(fn()=>$analysis->decide($r,$steps[$step]['id'],['action'=>'edit','body'=>$invalid]),'blank researcher scoring reason rejected '.$step);
}
$merge=$analysis->step($r,$rid,8);verifyV2($merge['status']==='completed'&&count(obj($merge['output'])['parts'])===4,'only four human reviews required for report');
$report=$analysis->report($r,'create',['run_id'=>$rid]);$student=(new Export($app))->reportData($r,$report['id'],true)['report'];
verifyV2($student['format']==='student-v2'&&array_keys($student['sections'])===['initial','final','reflection'],'student report includes separately scored reflection');
verifyV2($student['overall_feedback']===$feedback,'accepted profile comment reaches report unchanged');
verifyV2(count($student['sections']['initial']['criteria'])===3&&count($student['sections']['final']['criteria'])===2&&count($student['sections']['reflection']['criteria'])===1,'report keeps each question separate');
verifyV2($student['sections']['reflection']['criteria'][0]['score']===2&&$student['total']===null,'reflection score preserved; no invented overall aggregate');
verifyV2($student['sections']['initial']['criteria'][0]['answer']===obj($a['draft'])['problem'],'report includes the original reviewed student answer');
$oldBody=obj($report['body']);foreach($oldBody['student_report']['sections'] as &$oldSection)foreach($oldSection['criteria'] as &$oldCriterion)unset($oldCriterion['answer']);unset($oldSection,$oldCriterion);
$oldProjection=StudentReport::projection($oldBody,['problem'=>'LATER DRAFT MUST NOT APPEAR']);
verifyV2($oldProjection['sections']['initial']['criteria'][0]['answer']===$student['sections']['initial']['criteria'][0]['answer'],'old reports recover answer from reviewed sources rather than live draft');
verifyV2(!isset($oldProjection['sections']['initial']['criteria'][0]['input']),'answer projection does not expose private scoring input');
verifyV2(!isset($student['parts'],$student['internal'],$student['chat_statistics']),'student report excludes internal review and chat commentary');
verifyV2(array_column($db->all('SELECT * FROM reports WHERE id<>?',[$report['id']]),'body')===array_column($oldReports,'body'),'previous report snapshots untouched');
$fresh=$analysis->step($r,$rid,2,true);verifyV2($fresh['id']!==$steps[2]['id']&&$fresh['status']==='completed','profile comment can be regenerated independently');
denyV2(fn()=>$analysis->assemble($run),'new profile feedback requires a new decision');
verifyV2($analysis->currentStep($rid,3)['id']===$steps[3]['id']&&$analysis->currentStep($rid,6)['id']===$steps[6]['id'],'other scores unaffected by profile rewrite');
$analysis->decide($r,$fresh['id'],['action'=>'reject']);verifyV2($analysis->step($r,$rid,8)['status']==='completed','profile rejection is a valid explicit review decision');
$bad=['questions'=>['problem'=>['score'=>99,'feedback'=>'Geçersiz puan için açıklama metni.']]];denyV2(fn()=>$analysis->decide($r,$steps[3]['id'],['action'=>'edit','body'=>$bad]),'edited question scores remain bounded by rubric');
verifyV2(count($db->all('SELECT * FROM analysis_runs'))===count($old)+1,'legacy runs preserved');
denyV2(fn()=>(new Export($app))->reportData($u,$report['id']),'unpublished review report is unavailable to student');
$changes=['overall_feedback'=>$feedback];foreach($student['sections'] as $key=>$section)foreach($section['criteria'] as $q)$changes[$key][$q['id']]='Bu yanıt için geliştirme önerisi: kararınızı somut vaka kanıtlarıyla destekleyerek gerekçenizi açıklaştırabilirsiniz.';
$revised=$analysis->report($r,'edit_feedback',['id'=>$report['id'],'feedback'=>$changes]);$revisedStudent=(new Export($app))->reportData($r,$revised['id'],true)['report'];
verifyV2($revised['id']===$report['id']&&!$revised['approved_at']&&$revisedStudent['sections']['reflection']['criteria'][0]['feedback']===$changes['reflection']['reflection'],'reflection feedback edit updates the single unapproved report');
verifyV2($revisedStudent['sections']['reflection']['criteria'][0]['score']===2&&count($db->all('SELECT id FROM reports'))===count($oldReports)+1,'report feedback editing preserves scores without adding another report');
denyV2(fn()=>$analysis->report($r,'show',['id'=>$revised['id']]),'one-click student visibility respects open collection');$analysis->report($r,'approve',['id'=>$revised['id']]);denyV2(fn()=>$analysis->report($r,'edit_feedback',['id'=>$revised['id'],'feedback'=>$changes]),'approved review report cannot be overwritten');
denyV2(fn()=>$analysis->report($r,'publish',['id'=>$revised['id']]),'review report cannot be published while collection is open');
$db->q('UPDATE cases SET collection_closed_at=? WHERE id=?',[now(),$a['case_id']]);$db->q('UPDATE assignments SET allow_late=0 WHERE case_id=?',[$a['case_id']]);
$analysis->report($r,'show',['id'=>$revised['id']]);verifyV2((new Export($app))->reportData($u,$revised['id'])['report']===$revisedStudent,'student receives only the approved published review projection');
$db->q('UPDATE cases SET collection_closed_at=NULL WHERE id=?',[$a['case_id']]);denyV2(fn()=>(new Export($app))->reportData($u,$revised['id']),'reopening collection hides published review report');
$again=$analysis->report($r,'create',['run_id'=>$rid]);verifyV2($again['id']===$report['id']&&count($db->all('SELECT id FROM reports'))===count($oldReports)+1,'preparing again overwrites the same report');
$db->q('UPDATE cases SET collection_closed_at=? WHERE id=?',[now(),$a['case_id']]);$analysis->report($r,'show',['id'=>$again['id']]);
$fresh=$analysis->step($r,$rid,6,true);denyV2(fn()=>(new Export($app))->reportData($u,$again['id']),'recalculation withdraws old student report pending review');
$analysis->decide($r,$fresh['id'],['action'=>'accept']);$analysis->step($r,$rid,8);
$updated=$db->one('SELECT * FROM reports WHERE id=?',[$again['id']]);verifyV2(!$updated['approved_at']&&!$updated['published_at']&&count($db->all('SELECT id FROM reports'))===count($oldReports)+1,'new human review automatically replaces the single report and requires showing again');
$analysis->report($r,'show',['id'=>$updated['id']]);verifyV2(count($app->view($u,$a['id'])['reports'])===1,'student sees exactly one current report');
echo "TOTAL V2 $n checks passed; isolated SQLite/mock.\n";
