<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
require __DIR__.'/Seed.php';
$c=require __DIR__.'/../config/example.php';
$c['dsn']='sqlite::memory:';$c['db_user']='';$c['db_password']='';$c['https']=false;
$db=new Db($c);Migration::run($db);Seed::run($db,$c,uid());
$app=new App($db,$c);$analysis=new Analysis($app);$count=0;
function legacyCheck(bool $ok,string $name): void {global $count;if(!$ok)throw new RuntimeException($name);echo 'PASS LEGACY '.(++$count).' '.$name.PHP_EOL;}
function legacyDenied(callable $fn,string $name): void {try{$fn();}catch(Fault $e){legacyCheck($e->getCode()===422,$name);return;}throw new RuntimeException('Not rejected: '.$name);}
$r=$db->one("SELECT * FROM users WHERE id='test-researcher'");
$a=$db->one("SELECT * FROM applications WHERE user_id='test-student-6' AND case_id='test-case-1'");
$original=obj($a['draft']);$legacy=$original;unset($legacy['final_severity'],$legacy['final_difficulty']);
// Historical data is constructed only inside this disposable in-memory fixture.
$db->q('UPDATE applications SET draft=? WHERE id=?',[js($legacy),$a['id']]);
$db->q("UPDATE snapshots SET body=? WHERE app_id=? AND kind='submit'",[js($legacy),$a['id']]);
$before=$db->one('SELECT * FROM applications WHERE id=?',[$a['id']]);
$snapshots=$db->all('SELECT * FROM snapshots WHERE app_id=? ORDER BY id',[$a['id']]);
$run=$analysis->newRun($r,$a['id'],'test-rubric-v1');
$step=$analysis->step($r,$run['id'],1);
legacyCheck($step['status']==='completed','historical v1 submission passes integrity without later-added field');
legacyCheck((obj($step['output'])['uncollected_fields']??[])===['final_severity','final_difficulty'],'historical missing measurements explicitly reported as uncollected');
for($n=2;$n<=8;$n++){
    $step=$analysis->step($r,$run['id'],$n);
    legacyCheck($step['status']==='completed','historical v1 analysis completes step '.$n);
    if($n>=3&&$n<=7)$analysis->decide($r,$step['id'],['action'=>'accept','reason'=>'SENTETİK eski teslim regresyonu']);
}
$report=$analysis->report($r,'create',['run_id'=>$run['id']]);
legacyCheck((bool)$report['id'],'historical v1 report can be prepared');
$v2=$analysis->newRun($r,$a['id'],'test-rubric-v1',2);
legacyCheck($analysis->step($r,$v2['id'],1)['status']==='completed','historical submission remains compatible with v2');
$profile=(new Review($app))->profile($legacy);
legacyCheck($profile[3]['final']===null&&$profile[3]['change']===null,'uncollected severity stays unknown in profile');
legacyCheck($profile[2]['final']===null&&$profile[2]['change']===null&&!$profile[2]['final_collected'],'uncollected historical difficulty is not imputed');
legacyCheck($before===$db->one('SELECT * FROM applications WHERE id=?',[$a['id']]),'analysis preserves historical draft revision state and submission lock');
legacyCheck($snapshots===$db->all('SELECT * FROM snapshots WHERE app_id=? ORDER BY id',[$a['id']]),'immutable submission snapshots unchanged');
$u=$db->one("SELECT * FROM users WHERE id='test-student-5'");
$open=$db->one('SELECT * FROM applications WHERE user_id=?',[$u['id']]);$draft=obj($open['draft']);unset($draft['final_severity']);
legacyDenied(fn()=>$app->save($u,$open['id'],['revision'=>(int)$open['revision'],'draft'=>$draft],'submit'),'new submission still requires final severity');
legacyCheck($db->one('SELECT state FROM applications WHERE id=?',[$open['id']])['state']!=='submitted','failed new submission does not become submitted');
foreach(['missing'=>null,'out of range'=>11,'fraction'=>1.5] as $name=>$value){
    $bad=$legacy;$bad['final_confidence']=$value;
    $db->q('UPDATE applications SET draft=? WHERE id=?',[js($bad),$a['id']]);
    $run=$analysis->newRun($r,$a['id'],'test-rubric-v1');
    legacyCheck($analysis->step($r,$run['id'],1)['status']==='failed','historical integrity rejects '.$name.' final confidence');
}
$bad=$legacy;$bad['final_reason']=' ';
$db->q('UPDATE applications SET draft=? WHERE id=?',[js($bad),$a['id']]);
$run=$analysis->newRun($r,$a['id'],'test-rubric-v1');
legacyCheck($analysis->step($r,$run['id'],1)['status']==='failed','historical integrity rejects empty final answer');
echo "TOTAL LEGACY $count checks passed. Isolated in-memory SQLite and mock AI only.\n";
