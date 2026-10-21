<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';require __DIR__.'/Seed.php';
$count=0;
function verify(bool $ok,string $message):void{global $count;if(!$ok)throw new RuntimeException($message);echo 'PASS EXPORT '.(++$count).' '.$message.PHP_EOL;}
$c=require __DIR__.'/../config/example.php';$c['dsn']='sqlite::memory:';$c['db_user']='';$c['db_password']='';$c['https']=false;
$db=new Db($c);Migration::run($db);Seed::run($db,$c,uid());$app=new App($db,$c);$ex=new Export($app);
$student=$db->one("SELECT * FROM users WHERE id='test-student-1'");$researcher=$db->one("SELECT * FROM users WHERE id='test-researcher'");$a=$app->start($student,'test-case-3');
verify(array_keys(EventLabels::TYPES)===Telemetry::TYPES,'all accepted event types have Turkish descriptions');
$batch=[];foreach(Telemetry::TYPES as $i=>$type)$batch[]=['key'=>uid(),'tab'=>'test-tab','seq'=>$i+1,'at'=>gmdate('Y-m-d\TH:i:s\Z',time()+$i),'type'=>$type,'section'=>array_keys(EventLabels::SECTIONS)[$i%7],'payload'=>['synthetic'=>'SECRET SYNTHETIC TEXT','visible'=>true]];
(new Telemetry($app))->ingest($student,$a['id'],['events'=>$batch],'test-session');
$original=$db->all('SELECT * FROM events WHERE app_id=? ORDER BY client_seq',[$a['id']]);
$coded=$ex->dataset($researcher,['app_id'=>$a['id'],'include_test'=>1,'coded'=>1]);$raw=$ex->dataset($researcher,['app_id'=>$a['id'],'include_test'=>1,'coded'=>0]);
verify(count($coded['events'])===count($original),'no event filtering or duplication');
foreach($coded['events'] as $i=>$e){verify($e['type']===$original[$i]['type']&&$e['section']===$original[$i]['section']&&$e['client_at']===$original[$i]['client_at']&&$e['received_at']===$original[$i]['received_at'],'original codes and UTC preserved: '.$e['type']);}
verify($coded['events'][10]['type_tr']==='Periyodik durum kaydı'&&$coded['events'][10]['event_category']==='Teknik durum','heartbeat clearly marked as technical observation');
verify($coded['events'][0]['section_tr']==='Vaka metni','section code translated');
verify(!str_contains(js($coded),'SECRET SYNTHETIC TEXT'),'coded output still removes synthetic private content');
verify(str_contains(js($raw),'SECRET SYNTHETIC TEXT'),'raw output retains synthetic event payload');
verify($db->all('SELECT * FROM events WHERE app_id=? ORDER BY client_seq',[$a['id']])===$original,'stored event history unchanged');
verify(count(array_filter($coded['dictionary'],fn($r)=>str_starts_with($r['field'],'events.type.')))===count(Telemetry::TYPES),'dictionary documents all event codes');
verify(count(array_filter($coded['dictionary'],fn($r)=>str_starts_with($r['field'],'events.section.')))===count(EventLabels::SECTIONS),'dictionary documents all sections');
$csv=Export::csv($coded['events']);verify(str_contains($csv,'type_tr')&&str_contains($csv,'event_description')&&str_contains($csv,'Periyodik durum kaydı'),'CSV includes readable Turkish columns');
$json=obj(js($coded));verify($json['events'][10]['type_tr']==='Periyodik durum kaydı','JSON includes readable descriptions');
$path=__DIR__.'/tmp/event_labels_'.uid().'.xlsx';
try{Export::xlsx($coded,$path);$book=\PhpOffice\PhpSpreadsheet\IOFactory::load($path);$rows=$book->getSheetByName('events')->toArray();$header=$rows[0];$idx=array_search('type_tr',$header,true);verify($idx!==false&&$rows[11][$idx]==='Periyodik durum kaydı','generated XLSX reopened with Turkish event names');verify($book->getSheetByName('dictionary')->getHighestRow()>20,'XLSX contains explanatory dictionary');$book->disconnectWorksheets();}finally{if(is_file($path))unlink($path);}
$unknown=EventLabels::describe(['type'=>'future','section'=>'future']);verify($unknown['type']==='future'&&$unknown['type_tr']==='Tanımlanmamış olay','unknown future codes preserved with fallback');
$db->q('UPDATE events SET client_at=? WHERE id=?',['2026-20-20TinvalidZ',$original[0]['id']]);
$disco=$ex->disco($researcher,['view'=>'events','case_id'=>'test-case-3']);$stream=fopen('php://temp','w+');fwrite($stream,substr($disco,3));rewind($stream);$head=fgetcsv($stream,0,',','"','');$rows=[];while(($row=fgetcsv($stream,0,',','"',''))!==false)$rows[]=array_combine($head,$row);
$first=array_values(array_filter($rows,fn($row)=>$row['Event ID']===$original[0]['id']));
verify(count($first)===1&&$first[0]['Time Source']==='received_at_fallback','Disco invalid client clock falls back without losing event');
verify($db->one('SELECT client_at FROM events WHERE id=?',[$original[0]['id']])['client_at']==='2026-20-20TinvalidZ','Disco leaves original malformed timestamp unchanged');
$empty=explode("\n",substr($ex->disco($researcher,['view'=>'events','case_id'=>'test-case-6']),3))[0];verify(array_slice(str_getcsv($empty,',','"',''),0,2)===['Case ID','Activity'],'empty Disco export still has importable headers');
verify(count($raw['cases'])===1&&$raw['cases'][0]['id']==='test-case-3'&&count($raw['case_versions'])===1,'single-application full export keeps its case/version scope');
verify(!str_contains(js($raw),'"password"'),'full export never includes password hash columns');
echo 'TOTAL EXPORT '.$count.' checks passed. Separate in-memory synthetic database.'.PHP_EOL;
