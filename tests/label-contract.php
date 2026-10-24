<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';require __DIR__.'/Seed.php';
if(($argv[1]??'')==='change-during-call'){
    $path=realpath($argv[2]??'');demand($path&&dirname($path)===realpath(__DIR__.'/tmp')&&str_starts_with(basename($path),'label_race_'),'Yalnız ayrı test DB dosyası');
    $child=require __DIR__.'/../config/example.php';$child['dsn']='sqlite:'.$path;$child['db_user']='';$child['db_password']='';$db=new Db($child);usleep(350000);
    $db->insert('definitions',['id'=>'synthetic-race-label','owner_id'=>'test-researcher','family'=>'label','name'=>'AI özet verdi','version'=>1,'body'=>js(['description'=>'Yalnız AI özet mesajları.']),'archived'=>0,'created_at'=>now()]);exit;
}
$c=require __DIR__.'/../config/example.php';$c['dsn']='sqlite::memory:';$c['db_user']='';$c['db_password']='';$c['https']=false;
$db=new Db($c);Migration::run($db);Seed::run($db,$c,uid());$app=new App($db,$c);$service=new MessageLabels($app);$n=0;
function labelCheck(bool $ok,string $name): void {global $n;demand($ok,$name);echo 'PASS LABEL CONTRACT '.(++$n).' '.$name.PHP_EOL;}
$r=$db->one("SELECT * FROM users WHERE id='test-researcher'");$a=$db->one("SELECT * FROM applications WHERE user_id='test-student-4'");
$before=$db->all('SELECT * FROM messages ORDER BY id');
$first=$service->generate($r,$a['id']);
labelCheck($first['directive']===MessageLabels::DIRECTIVE&&$first['label_definitions']!==[],'batch records active prompt version and exact label definitions');
labelCheck($first['label_definitions'][0]['description']==='SENTETİK başlangıç tanımı'&&is_array($first['label_definitions'][0]['body']),'batch metadata preserves name, description and original definition body');
labelCheck($service->generate($r,$a['id'])['batch_id']===$first['batch_id'],'unchanged current definitions reuse cache');
$definition=$first['label_definitions'][0];$body=$definition['body'];$body['description']='Yalnız öğrencinin gerekçe sorduğu mesajlarda kullanılır.';
$db->q('UPDATE definitions SET body=? WHERE id=?',[js($body),$definition['id']]);
$changed=$service->generate($r,$a['id']);
labelCheck($changed['batch_id']!==$first['batch_id']&&$changed['label_definitions'][0]['description']===$body['description'],'editing description invalidates cache and sends updated definition');
$db->q('UPDATE definitions SET name=? WHERE id=?',['Güncel etiket adı',$definition['id']]);$renamed=$service->generate($r,$a['id']);
labelCheck($renamed['batch_id']!==$changed['batch_id']&&$renamed['label_definitions'][0]['name']==='Güncel etiket adı','editing name invalidates cache and sends current name');
$admin=new Admin($app);$created=$admin->mutate($r,'definition',['family'=>'label','name'=>'AI gerekçe sundu','body'=>['target_role'=>'assistant','description'=>'AI yanıtı önerinin gerekçesini açıkladığında kullanılır; öğrenci sorularında kullanılmaz.']]);
$added=$service->generate($r,$a['id']);$new=array_values(array_filter($added['label_definitions'],fn($d)=>$d['id']===$created['id']));
labelCheck($added['batch_id']!==$renamed['batch_id']&&count($new)===1,'new assistant-response label is included in next request');
$messages=$db->all('SELECT id,seq,role,content,status FROM messages WHERE app_id=? ORDER BY seq',[$a['id']]);
$map=[$messages[0]['id']=>[],$messages[1]['id']=>[$created['id']]];
labelCheck(MessageLabels::validate(['labels'=>$map],$messages,$added['label_definitions'])===$map,'known assistant label is valid for assistant message; no forced student-only tagging');
$schema=Ai::responseFormat(['task'=>'message_labels','messages'=>$messages,'label_definitions'=>$added['label_definitions']],true)['json_schema']['schema']['properties']['labels'];
labelCheck($schema['required']===array_column($messages,'id')&&$schema['additionalProperties']===false,'strict label response covers exact message IDs only');
labelCheck($schema['properties'][$messages[1]['id']]['items']['enum']===[$created['id']],'strict label response allows only current provided label IDs for assistant too');
$wrong=$map;$wrong[$messages[0]['id']]=[$created['id']];$blocked=false;
try{MessageLabels::validate(['labels'=>$wrong],$messages,$added['label_definitions']);}catch(Fault $e){$blocked=$e->getCode()===502;}
labelCheck($blocked,'assistant-only label is rejected on a student message');
$wrong=$map;$wrong[$messages[1]['id']]=[$definition['id']];$blocked=false;
try{MessageLabels::validate(['labels'=>$wrong],$messages,$added['label_definitions']);}catch(Fault $e){$blocked=$e->getCode()===502;}
labelCheck($blocked,'student-only label is rejected on an assistant message');
$blocked=false;try{$admin->mutate($r,'labels',['message_id'=>$messages[0]['id'],'labels'=>[$created['id']],'reason'=>'Synthetic wrong-group test']);}catch(Fault $e){$blocked=$e->getCode()===422;}
labelCheck($blocked,'manual API also rejects wrong-group label');
$blocked=false;try{$admin->mutate($r,'definition',['family'=>'label','name'=>'No group','body'=>['description'=>'Synthetic']]);}catch(Fault $e){$blocked=$e->getCode()===422;}
labelCheck($blocked,'creating a label requires an explicit group');
$empty=Ai::responseFormat(['task'=>'message_labels','messages'=>[$messages[1]],'label_definitions'=>[$definition]],true);
labelCheck($empty['json_schema']['schema']['properties']['labels']['properties'][$messages[1]['id']]['maxItems']===0,'schema requires empty list when no matching role candidates exist');
$ungrouped=$definition;unset($ungrouped['body']['target_role']);
labelCheck(MessageLabels::candidates([$ungrouped],'user')===[],'legacy ungrouped definitions are never guessed from label names');
$roleChanged=$added['label_definitions'];$roleChanged[0]['body']['target_role']=$roleChanged[0]['body']['target_role']==='user'?'assistant':'user';
labelCheck(MessageLabels::inputHash($messages,$roleChanged,'synthetic-model')!==MessageLabels::inputHash($messages,$added['label_definitions'],'synthetic-model'),'changing target group invalidates the labeling cache');
$beforeRejected=(int)$db->one('SELECT COUNT(*) n FROM label_reviews')['n'];
$blocked=false;try{$admin->mutate($r,'labels',['message_id'=>$messages[1]['id'],'labels'=>[$definition['id']],'reason'=>'Synthetic wrong-group test']);}catch(Fault $e){$blocked=true;}
labelCheck($blocked&&(int)$db->one('SELECT COUNT(*) n FROM label_reviews')['n']===$beforeRejected,'rejected manual selection leaves no partial review');
$legacy=$db->one("SELECT id FROM applications WHERE user_id='test-student-6'");$history=$app->view($r,$legacy['id']);
labelCheck(count($history['label_batches'])===1,'legacy analysis labeling has one existing batch before cache reuse');
$legacyInput=(new Analysis($app))->payload($db->one('SELECT * FROM analysis_runs WHERE app_id=?',[$legacy['id']]),6);
labelCheck(is_array($legacyInput['label_definitions'][0]['body'])&&array_key_exists('description',$legacyInput['label_definitions'][0]),'legacy analysis sends the same complete current definition structure');
labelCheck($before===$db->all('SELECT * FROM messages ORDER BY id'),'label updates never rewrite student or AI messages');
$path=__DIR__.'/tmp/label_race_'.uid().'.sqlite';$race=$c;$race['dsn']='sqlite:'.$path;$raceDb=new Db($race);Migration::run($raceDb);Seed::run($raceDb,$race,uid());
$race['ai']['mode']='delay';$raceApp=new App($raceDb,$race);$raceResearcher=$raceDb->one("SELECT * FROM users WHERE id='test-researcher'");$raceCase=$raceDb->one("SELECT id FROM applications WHERE user_id='test-student-4'");
$labelCount=(int)$raceDb->one('SELECT COUNT(*) n FROM message_labels')['n'];
$process=proc_open([PHP_BINARY,__FILE__,'change-during-call',$path],[0=>['pipe','r'],1=>['pipe','w'],2=>['pipe','w']],$pipes,__DIR__.'/..');demand(is_resource($process),'Sentetik değişiklik işlemi başlatılamadı');fclose($pipes[0]);
$blocked=false;try{(new MessageLabels($raceApp))->generate($raceResearcher,$raceCase['id']);}catch(Fault $e){$blocked=$e->getCode()===409;}
$childOut=stream_get_contents($pipes[1]);$childErr=stream_get_contents($pipes[2]);fclose($pipes[1]);fclose($pipes[2]);$exit=proc_close($process);demand($exit===0,'Sentetik tanım değişikliği başarısız');
labelCheck($blocked,'adding a label while AI response is pending rejects the stale result');
labelCheck((int)$raceDb->one('SELECT COUNT(*) n FROM message_labels')['n']===$labelCount,'stale response cannot persist partial or old-definition labels');
echo "TOTAL LABEL CONTRACT $n checks passed. Isolated SQLite/mock only.\n";
