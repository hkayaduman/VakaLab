<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';require __DIR__.'/Seed.php';
$n=0;function discoCheck(bool $ok,string $name): void {global $n;demand($ok,$name);echo 'PASS DISCO '.(++$n).' '.$name.PHP_EOL;}
function discoRows(string $csv): array {$f=fopen('php://temp','w+');fwrite($f,substr($csv,3));rewind($f);$header=fgetcsv($f,0,',','"','');$rows=[];while(($row=fgetcsv($f,0,',','"',''))!==false)$rows[]=array_combine($header,$row);fclose($f);return [$header,$rows];}
$c=require __DIR__.'/../config/example.php';$c['dsn']='sqlite::memory:';$c['db_user']='';$c['db_password']='';
if(getenv('VAKALAB_TEST_DSN')){demand(str_contains(getenv('VAKALAB_TEST_DSN'),'_test_'),'Separate test database required');$c['dsn']=getenv('VAKALAB_TEST_DSN');$c['db_user']=getenv('VAKALAB_TEST_USER');$c['db_password']=getenv('VAKALAB_TEST_PASSWORD');}
$db=new Db($c);Migration::run($db);Seed::run($db,$c,uid());$app=new App($db,$c);$admin=new Admin($app);$ex=new Export($app);
$r=$db->one("SELECT * FROM users WHERE id='test-researcher'");$u=$db->one("SELECT * FROM users WHERE id='test-student-6'");$first=$app->start($u,'test-case-1');
$next=$admin->mutate($r,'new_attempt',['app_id'=>$first['id'],'reason'=>'Synthetic Disco separation regression']);$second=$db->one('SELECT * FROM applications WHERE id=?',[$next['id']]);
$d=obj($first['draft']);$initial=Rules::draft(array_intersect_key($d,array_flip(['knowledge','confidence','difficulty','severity','problem','decision','reason'])));
$second=$app->save($u,$second['id'],['revision'=>0,'draft'=>$initial],'initial');
$app->chat($u,$second['id'],['key'=>uid(),'text'=>'SENTETİK ikinci deneme sorusu']);
$second=$app->save($u,$second['id'],['revision'=>$second['revision'],'draft'=>$d],'submit');
$stamp=fn(int $day,int $second)=>sprintf('2026-10-%02dT10:%02d:%02d.000000Z',$day,intdiv($second,60),$second%60);
foreach([$first,$second] as $i=>$a){$id=$a['id'];$day=20+$i;
    $db->q('DELETE FROM events WHERE app_id=?',[$id]);
    $db->q('UPDATE applications SET initial_at=?,submitted_at=? WHERE id=?',[$stamp($day,90),$stamp($day,180),$id]);
    foreach(['initial'=>90,'submit'=>180] as $kind=>$at)$db->q('UPDATE snapshots SET created_at=? WHERE app_id=? AND kind=?',[$stamp($day,$at),$id,$kind]);
    $db->q('UPDATE messages SET created_at=? WHERE app_id=?',[$stamp($day,120),$id]);
    $db->q('UPDATE ai_requests SET started_at=?,completed_at=? WHERE app_id=?',[$stamp($day,110),$stamp($day,120),$id]);
    foreach($db->all('SELECT id FROM ai_requests WHERE app_id=?',[$id]) as $request)$db->q('UPDATE ai_attempts SET started_at=?,completed_at=? WHERE request_id=?',[$stamp($day,110),$stamp($day,120),$request['id']]);
    foreach([['section_open','case',0,[]],['section_open','profile',90,[]],['workflow_confirmed','profile',90,['kind'=>'initial']],['section_open','confirm',100,[]],['workflow_confirmed','confirm',180,['kind'=>'submit']],['section_open','case',240,['submitted'=>true]]] as $seq=>[$type,$section,$at,$payload]){
        $db->insert('events',['id'=>uid(),'app_id'=>$id,'client_key'=>uid(),'tab_id'=>'disco-test','session_id'=>'disco-test','client_seq'=>$seq+1,'client_at'=>$stamp($day,$at),'received_at'=>$stamp($day,$at),'type'=>$type,'section'=>$section,'payload'=>js($payload+['view_id'=>'disco-test']),'quality'=>'observed']);
    }
}
$before=[];foreach(['applications','snapshots','messages','events','analysis_runs','reports'] as $table)$before[$table]=$db->all('SELECT * FROM '.$table.' ORDER BY id');
foreach(['visits','events'] as $view){
    [$header,$rows]=discoRows($ex->disco($r,['case_id'=>'test-case-1','user_id'=>$u['id'],'view'=>$view]));
    discoCheck(in_array('Deneme numarası',$header,true),$view.' has explicit attempt column');
    discoCheck(count(array_unique(array_column($rows,'Case ID')))===2,$view.' contains exactly two independent cases');
    discoCheck(count(array_unique(array_column($rows,'Participant')))===1&&count(array_unique(array_column($rows,'Vaka ID')))===1,$view.' retains shared participant and pedagogical case');
    foreach([$first,$second] as $i=>$a){$part=array_values(array_filter($rows,fn($row)=>$row['Case ID']===$a['id']));
        discoCheck(count($part)>0&&array_unique(array_column($part,'Deneme numarası'))===[(string)($i+1)],$view.' every row identifies attempt '.($i+1));
        discoCheck(count(array_filter($part,fn($row)=>$view==='visits'?($row['Activity Code']??'')==='SUBMITTED':$row['Activity']==='Son teslim yapıldı'))===1,$view.' each attempt has its own submission');
        discoCheck(!array_filter($part,fn($row)=>$row['Complete Timestamp']>sprintf('2026-10-%02d 10:03:00.000',20+$i)),$view.' excludes post-submission navigation');
        [$oneHeader,$one]=discoRows($ex->disco($r,['app_id'=>$a['id'],'view'=>$view]));
        discoCheck(array_unique(array_column($one,'Case ID'))===[$a['id']]&&array_unique(array_column($one,'Deneme numarası'))===[(string)($i+1)],$view.' individual download contains only selected attempt');
        if($view==='visits'){
            $durations=array_map(fn($row)=>(float)($row['Duration Seconds']??0),$part);
            discoCheck(max($durations)<=90&&array_sum($durations)===170.0,'visits never count the day between attempts');
            discoCheck($part[0]['Sequence']==='1'&&$part[0]['Previous Activity']===''&&end($part)['Next Activity']==='','sequence and previous/next links restart per attempt');
        }
    }
    [$empty,$noRows]=discoRows($ex->disco($r,['case_id'=>'test-case-6','view'=>$view]));
    discoCheck(!$noRows&&in_array('Deneme numarası',$empty,true),$view.' empty CSV retains attempt column');
}
$data=$ex->dataset($r,['case_id'=>'test-case-1','user_id'=>$u['id'],'include_test'=>1,'coded'=>1]);discoCheck(array_column($data['applications'],'attempt_no')===[1,2],'coded data preserves attempt numbers');
foreach($before as $table=>$old)discoCheck($old===$db->all('SELECT * FROM '.$table.' ORDER BY id'),'exports preserve '.$table);
echo "TOTAL DISCO $n checks passed.\n";
