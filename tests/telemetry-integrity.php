<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
require __DIR__.'/Seed.php';

$config=require __DIR__.'/../config/example.php';
$config['dsn']='sqlite::memory:';
if($dsn=getenv('VAKALAB_TEST_DSN')){
    demand(str_starts_with($dsn,'mysql:host=127.0.0.1;')&&str_contains($dsn,'dbname=vakalab_test_'),'Separate local database required');
    $config['dsn']=$dsn;
    $config['db_user']=getenv('VAKALAB_TEST_USER');
    $config['db_password']=getenv('VAKALAB_TEST_PASSWORD');
}
$db=new Db($config);
Migration::run($db);
Seed::run($db,$config,uid());
$app=new App($db,$config);
$telemetry=new Telemetry($app);
$export=new Export($app);
$admin=new Admin($app);
$student=$db->one("SELECT * FROM users WHERE id='test-student-5'");
$researcher=$db->one("SELECT * FROM users WHERE id='test-researcher'");
$origin=uid();
$newSession=uid();

function integrityCheck(bool $condition,string $description): void {
    demand($condition,$description);
    echo "PASS $description\n";
}
function integrityEvent(string $origin,int $sequence,string $time,string $section,array $meta=[],string $type='section_open'): array {
    return ['key'=>uid(),'session'=>$origin,'tab'=>'test-tab','seq'=>$sequence,'at'=>$time,'section'=>$section,
        'type'=>$type,'payload'=>$meta+['view_id'=>'test-view','app_active'=>true,'navigation_version'=>3]];
}
function integrityCsv(string $csv): array {
    $stream=fopen('php://temp','w+');
    fwrite($stream,substr($csv,3));
    rewind($stream);
    $headers=fgetcsv($stream,0,',','"','');
    $rows=[];
    while(($cells=fgetcsv($stream,0,',','"',''))!==false)$rows[]=array_combine($headers,$cells);
    fclose($stream);
    return [$headers,$rows];
}

$work=$app->start($student,'test-case-1');
$id=$work['id'];
$future=fn(int $seconds)=>(new DateTimeImmutable("+$seconds seconds"))->format('Y-m-d\TH:i:s.u\Z');
$late=integrityEvent($origin,1,$future(120),'profile');
$queued=integrityEvent($origin,2,$future(123),'confirm');
$marker=integrityEvent($origin,3,$future(127),'confirm',['kind'=>'submit','submitted'=>true],'workflow_confirmed');
$submit=['revision'=>$work['revision'],'draft'=>obj($work['draft']),'submit_telemetry'=>['events'=>[$queued],'marker'=>$marker]];
$invalid=$submit;
$invalid['submit_telemetry']['marker']['at']='bad';
try{$app->save($student,$id,$invalid,'submit',$origin);throw new RuntimeException('Expected invalid marker to prevent commit');}
catch(Fault $error){integrityCheck(!$db->one('SELECT id FROM events WHERE client_key=?',[$queued['key']])&&!$app->view($student,$id)['application']['submitted_at'],'a failed atomic submission rolls back its events');}
$saved=$app->save($student,$id,$submit,'submit',$origin);
integrityCheck((bool)$saved['submitted_at']&&$saved['telemetry_receipt']['accepted']===[$queued['key'],$marker['key']],'clock-skewed pending events and the success marker commit with final submission');
$post=integrityEvent($origin,4,$future(129),'case');
$unknown=integrityEvent(uid(),5,$future(128),'initial');
$receipt=$telemetry->ingest($student,$id,['events'=>[$late,$late,$post,$unknown]],$newSession);
integrityCheck($receipt['accepted']===[$late['key'],$late['key']]&&array_column($receipt['rejected'],'index')===[2]&&$receipt['quarantined']===[$unknown['key']],'replay is durable, deduplicated and checked against the original submission sequence');
$after=$app->view($researcher,$id);
integrityCheck(count($after['events'])===4&&array_column($after['navigation']['visits'],'section')===['profile','confirm'],'raw evidence retains ambiguity and research visits retain the actual pre-submit order');
integrityCheck($db->one('SELECT session_id FROM events WHERE client_key=?',[$late['key']])['session_id']===$origin,'replay retains the original session after sign-in changes');
[, $eventRows]=integrityCsv($export->disco($researcher,['app_id'=>$id,'view'=>'events','all_data'=>1]));
integrityCheck(count(array_filter($eventRows,fn($row)=>$row['Client Sequence']==='1'&&$row['Time Source']==='client_at'))===1,'detailed Disco retains a confirmed earlier event even if its device time is ahead');
$traceRows=array_values(array_filter($eventRows,fn($row)=>$row['Client Sequence']!==''));
integrityCheck(array_column($traceRows,'Client Sequence')===['1','2','3']&&end($traceRows)['Server Confirmation UTC']!=='','detailed Disco orders the confirmed cutoff in the same client stream and preserves the server confirmation timestamp');

$work=$app->start($student,'test-case-2');
$id=$work['id'];
$first=integrityEvent($origin,1,'2026-01-01T09:00:00Z','profile');
$second=integrityEvent($origin,2,'2026-01-01T09:00:03Z','initial');
$secondary=integrityEvent(uid(),1,'2026-01-01T09:00:04Z','case',['read_only_tab'=>true]);
$telemetry->ingest($student,$id,['events'=>[$second]],$newSession);
$telemetry->ingest($student,$id,['events'=>[$first,$secondary]],$newSession);
integrityCheck($app->view($student,$id)['application']['last_section']==='initial','a late packet and read-only tab cannot overwrite the resume section');
$admin->mutate($researcher,'collection',['case_id'=>'test-case-2','closed'=>true]);
$futureOpen=integrityEvent($origin,3,$future(1),'profile');
$uncertain=integrityEvent($origin,4,'2026-01-01T09:00:20Z','profile');
$receipt=$telemetry->ingest($student,$id,['events'=>[$futureOpen,$uncertain]],$newSession);
integrityCheck($receipt['accepted']===[]&&$receipt['rejected']===[]&&$receipt['quarantined']===[$futureOpen['key'],$uncertain['key']],'closed collection quarantines arrivals without a verified capture interval');
$admin->mutate($researcher,'late_access',['case_id'=>'test-case-2','user_ids'=>[$student['id']],'open'=>true]);
$lateAccess=integrityEvent($origin,5,$future(2),'profile');
integrityCheck($telemetry->ingest($student,$id,['events'=>[$lateAccess]],$newSession)['accepted']===[$lateAccess['key']],'an authorized extension resumes collection');

$work=$app->start($student,'test-case-3');
$id=$work['id'];
$marks=[
    integrityEvent($origin,1,'2026-02-27T12:00:00Z','case'),
    integrityEvent($origin,4,'2026-02-27T12:00:25Z','profile'),
    integrityEvent($origin,5,'2026-02-27T11:59:50Z','initial',['view_id'=>'new-view']),
    integrityEvent($origin,6,'2026-02-30T12:00:00Z','final',['view_id'=>'new-view']),
];
$telemetry->ingest($student,$id,['events'=>$marks],$newSession);
$view=$app->view($researcher,$id);
$visits=$view['navigation']['visits'];
integrityCheck(array_column($visits,'section')===['case','profile','initial','final']&&$visits[2]['invalid_clock'],'a backward clock across a view change cannot reverse client order');
integrityCheck($visits[0]['quality']==='event_gap'&&$visits[0]['duration_seconds']===null,'missing sequence numbers never become a precise duration');
integrityCheck(Navigation::time($marks[3]['at'])===null&&in_array('invalid_client_time',array_column($view['events'],'quality'),true),'invalid calendar dates are retained as explicitly invalid raw evidence');
$admin->mutate($researcher,'exclude',['id'=>$id,'excluded'=>true,'reason'=>'Sentetik dışlama']);
$db->q('UPDATE cases SET practice=1 WHERE id=?',['test-case-3']);
foreach(['events','visits'] as $profile){
    [$headers,$rows]=integrityCsv($export->disco($researcher,['app_id'=>$id,'view'=>$profile]));
    integrityCheck($rows===[]&&!array_diff(['Test','Practice','Excluded'],$headers),"$profile defaults exclude test, practice and excluded attempts and keep flag headers");
}
[, $rows]=integrityCsv($export->disco($researcher,['app_id'=>$id,'view'=>'events','all_data'=>1]));
integrityCheck(count($rows)===4&&array_unique(array_column($rows,'Excluded'))===['yes']&&array_unique(array_column($rows,'Practice'))===['yes']&&array_unique(array_column($rows,'Test'))===['yes'],'explicit full export retains cohort flags on every row');
$fallback=current(array_filter($rows,fn($row)=>$row['Client UTC raw']===$marks[3]['at']));
integrityCheck($fallback['Time Source']==='received_at_fallback'&&$fallback['Quality']==='invalid_client_time','detailed export uses receipt time for a malformed date and keeps the original timestamp separately');

$other=$db->one("SELECT * FROM users WHERE id='test-student-1'");
$id=$app->start($other,'test-case-4')['id'];
$timelineOrigin=uid();
$normal=[
    integrityEvent($timelineOrigin,1,'2026-03-02T10:00:00Z','case'),
    integrityEvent($timelineOrigin,2,'2026-03-02T10:00:12Z','profile'),
    integrityEvent($timelineOrigin,3,'2026-03-02T10:00:20Z','case'),
    integrityEvent($timelineOrigin,4,'2026-03-02T10:00:25Z','case',['reason'=>'home'],'section_leave'),
];
$telemetry->ingest($other,$id,['events'=>$normal],$timelineOrigin);
$normalVisits=$app->view($researcher,$id)['navigation']['visits'];
integrityCheck(array_column($normalVisits,'section')===['case','profile','case']&&array_column($normalVisits,'duration_seconds')===[12.0,8.0,5.0],'uninterrupted visits preserve both repeated positions and exact section durations');

$before=now();
$skew=integrityEvent($timelineOrigin,5,$future(120),'profile',['capture_from'=>$before,'capture_until'=>$before,'view_id'=>'collection-view']);
$admin->mutate($researcher,'collection',['case_id'=>'test-case-4','closed'=>true]);
$receipt=$telemetry->ingest($other,$id,['events'=>[$skew]],$timelineOrigin);
integrityCheck($receipt['accepted']===[$skew['key']],'server-anchored pre-close event survives a two minute client clock skew');
$inside=now();
$closed=integrityEvent($timelineOrigin,6,$inside,'initial',['capture_from'=>$inside,'capture_until'=>$inside,'view_id'=>'collection-view']);
$admin->mutate($researcher,'late_access',['case_id'=>'test-case-4','user_ids'=>[$other['id']],'open'=>true]);
$duringExtension=now();
$extension=integrityEvent($timelineOrigin,7,$duringExtension,'final',['capture_from'=>$duringExtension,'capture_until'=>$duringExtension,'view_id'=>'collection-view']);
$admin->mutate($researcher,'late_access',['case_id'=>'test-case-4','user_ids'=>[$other['id']],'open'=>false]);
$revoked=now();
$afterExtension=integrityEvent($timelineOrigin,8,$revoked,'ai',['capture_from'=>$revoked,'capture_until'=>$revoked,'view_id'=>'collection-view']);
$admin->mutate($researcher,'collection',['case_id'=>'test-case-4','closed'=>false]);
$after=now();
$reopened=integrityEvent($timelineOrigin,9,$after,'reflection',['capture_from'=>$after,'capture_until'=>$after,'view_id'=>'collection-view']);
$receipt=$telemetry->ingest($other,$id,['events'=>[$closed,$extension,$afterExtension,$reopened]],$timelineOrigin);
integrityCheck($receipt['accepted']===[$extension['key'],$reopened['key']]&&$receipt['quarantined']===[$closed['key'],$afterExtension['key']],'replaying after reopen respects historical closure, extension and revocation');
$byKey=array_column($app->view($researcher,$id)['events'],null,'client_key');
$extensionWindow=obj($byKey[$extension['key']]['payload'])['collection_window'];
$reopenWindow=obj($byKey[$reopened['key']]['payload'])['collection_window'];
integrityCheck($extensionWindow!==$reopenWindow,'extension and reopened collection produce separate navigation windows');

$work=$app->start($student,'test-case-5');
$id=$work['id'];
$draft=obj($app->view($student,$saved['id'])['application']['draft']);
$initialOnly=Rules::draft([]);
foreach(['knowledge','confidence','difficulty','severity','problem','decision','reason'] as $name)$initialOnly[$name]=$draft[$name];
$work=$app->save($student,$id,['revision'=>$work['revision'],'draft'=>$initialOnly],'initial');
$prior=uid();$terminal=uid();$previous=[];
for($i=1;$i<=110;$i++){
    $at=(new DateTimeImmutable('2026-02-01T09:00:00Z'))->modify("+$i seconds")->format('Y-m-d\TH:i:s.u\Z');
    $previous[]=integrityEvent($prior,$i,$at,$i%2?'case':'profile');
}
$confirmation=integrityEvent($terminal,1,now(),'confirm',['kind'=>'submit','submitted'=>true],'workflow_confirmed');
$app->save($student,$id,['revision'=>$work['revision'],'draft'=>$draft,'submit_telemetry'=>[
    'events'=>array_slice($previous,-98),'marker'=>$confirmation,
    'pending'=>['ranges'=>[['session'=>$prior,'tab'=>'test-tab','first'=>1,'last'=>110]]]
]],'submit',$terminal);
$beforeReplay=$app->view($researcher,$id)['navigation'];
integrityCheck($beforeReplay['coverage']['expected_missing_events']===12&&$beforeReplay['coverage']['incomplete_prefix_streams']===1&&$beforeReplay['visits'][0]['missing_before'],'the retained trace exposes its missing prefix before the old queue resumes');
$receipt=$telemetry->ingest($student,$id,['events'=>array_slice($previous,0,12)],$terminal);
$final=$app->view($researcher,$id)['navigation'];
integrityCheck(count($receipt['accepted'])===12&&$final['coverage']['expected_missing_events']===0&&$final['coverage']['withheld_events']===0&&$final['visits'][0]['start_sequence']===1,'the original pre-submit queue can replay across authentication sessions without losing its research prefix');
[, $completeCsv]=integrityCsv($export->disco($researcher,['app_id'=>$id,'view'=>'events','all_data'=>1]));
integrityCheck(count(array_filter($completeCsv,fn($row)=>$row['Client Sequence']==='1'&&$row['Activity']!=='Son teslim yapıldı'))===1&&$completeCsv[0]['Expected Missing Events']==='0','detailed export exposes queue coverage and the original first transition');
