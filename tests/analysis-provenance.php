<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';require __DIR__.'/Seed.php';
$c=require __DIR__.'/../config/example.php';$c['dsn']='sqlite::memory:';$c['db_user']='';$c['db_password']='';$c['https']=false;
$db=new Db($c);Migration::run($db);Seed::run($db,$c,uid());$app=new App($db,$c);$analysis=new Analysis($app);$n=0;
function verify(bool $ok,string $message): void {global $n;demand($ok,$message);echo 'PASS '.(++$n).' '.$message.PHP_EOL;}
$r=$db->one("SELECT * FROM users WHERE id='test-researcher'");$u=$db->one("SELECT * FROM users WHERE id='test-student-6'");
$a=$db->one("SELECT * FROM applications WHERE user_id=?",[$u['id']]);$run=$db->one('SELECT * FROM analysis_runs WHERE app_id=?',[$a['id']]);
$old=$db->one('SELECT * FROM reports WHERE app_id=? ORDER BY version',[$a['id']]);
$step=$db->one('SELECT * FROM analysis_steps WHERE run_id=? AND step=3',[$run['id']]);$original=$step['output'];$edited=obj($original);
$edited['scores']['reasoning']['score']=2;$edited['scores']['reasoning']['level']='Gelişiyor';$edited['scores']['reasoning']['reason']='Araştırmacı tarafından düzenlenen gerekçe.';
$analysis->decide($r,$step['id'],['action'=>'edit','body'=>$edited,'reason'=>'PRIVATE-RESEARCHER-REASON']);
$base=$analysis->assemble($run);$part=current(array_filter($base['parts'],fn($p)=>$p['step']===3));
verify($part['score_sources']['reasoning']==='researcher_edited','edited score marked researcher origin');
verify($part['score_sources']['perspectives']==='ai_accepted','unchanged criterion still marked AI origin');
verify($db->one('SELECT output FROM analysis_steps WHERE id=?',[$step['id']])['output']===$original,'original AI suggestion remains unchanged');
$report=$analysis->report($r,'create',['run_id'=>$run['id']]);$data=(new Export($app))->reportData($r,$report['id'],true)['report'];
verify(!isset($data['sections']['initial']['criteria'][0]['score_origin']),'student preview does not disclose score origin');
verify(!isset($data['sections']['initial']['criteria'][0]['feedback_origin'],$data['overall_feedback_origin']),'student preview has no feedback origin fields');
verify($data['sections']['initial']['criteria'][0]['score']===2,'student receives researcher reviewed score');
verify(!str_contains(js($data),'PRIVATE-RESEARCHER-REASON')&&!isset($data['parts'],$data['internal']),'origin disclosure does not reveal private reasons or internals');
verify($db->one('SELECT body FROM reports WHERE id=?',[$old['id']])['body']===$old['body'],'previous report snapshot unchanged');
$analysis->decide($r,$step['id'],['action'=>'accept','reason'=>'Explicit restore of original AI suggestion']);
$after=$analysis->assemble($run);$restored=current(array_filter($after['parts'],fn($p)=>$p['step']===3));
verify($restored['score_sources']['reasoning']==='ai_accepted','explicit acceptance restores AI score origin');
verify(obj($report['body'])['student_report']['sections']['initial']['criteria'][0]['score']===2,'created report retains reviewed score after later decisions');
$feedback=['overall_feedback'=>'Araştırmacının öğrenciye düzenlediği genel geri bildirim.'];
foreach($data['sections'] as $key=>$section){$feedback[$key]=[];foreach($section['criteria'] as $criterion)$feedback[$key][$criterion['id']]='Araştırmacının öğrenci yanıtına göre düzenlediği açıklama.';}
$revised=$analysis->report($r,'edit_feedback',['id'=>$report['id'],'feedback'=>$feedback]);$studentRevised=(new Export($app))->reportData($r,$revised['id'],true)['report'];
verify($revised['id']!==$report['id']&&!$revised['approved_at']&&!$revised['published_at'],'feedback edit creates separate unapproved snapshot');
verify($studentRevised['total']===$data['total']&&$studentRevised['sections']['initial']['criteria'][0]['score']===$data['sections']['initial']['criteria'][0]['score'],'feedback edit cannot change scores');
verify($studentRevised['overall_feedback']===$feedback['overall_feedback'],'researcher edited feedback reaches preview');
verify($db->one('SELECT body FROM reports WHERE id=?',[$report['id']])['body']===$report['body'],'feedback editing preserves previous snapshot');
verify(!str_contains(js(StudentReport::blocks(['report'=>$studentRevised,'version'=>$revised['version'],'approved_at'=>null])),'kaynağı'),'student downloadable report has no origin labels');
$denied=false;try{$analysis->report($u,'edit_feedback',['id'=>$revised['id'],'feedback'=>$feedback]);}catch(Fault $e){$denied=$e->getCode()===403;}verify($denied,'student cannot edit report feedback');
$analysis->report($r,'approve',['id'=>$revised['id']]);$denied=false;try{$analysis->report($r,'edit_feedback',['id'=>$revised['id'],'feedback'=>$feedback]);}catch(Fault $e){$denied=$e->getCode()===409;}verify($denied,'approved report feedback cannot be overwritten');
echo "TOTAL REVIEW $n integration checks passed; isolated SQLite/mock.\n";
