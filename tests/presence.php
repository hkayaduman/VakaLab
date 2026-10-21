<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';require __DIR__.'/Seed.php';
$count=0;function presenceCheck(bool $ok,string $name):void{global $count;demand($ok,$name);echo 'PASS PRESENCE '.(++$count).' '.$name.PHP_EOL;}
function presenceEvents(array $spec):array{
    $out=[];foreach($spec as $i=>[$type,$section,$time,$payload])$out[]=['id'=>'presence-event-'.$i,'app_id'=>'fixture','client_key'=>'presence-key-'.$i,'quality'=>'client_observation_unverified','type'=>$type,'section'=>$section,'client_at'=>'2026-10-21T'.$time.'Z','received_at'=>'2026-10-21T'.$time.'Z','client_seq'=>$i+1,'session_id'=>'presence-session','tab_id'=>'presence-tab','payload'=>js($payload+['view_id'=>'presence-view','app_active'=>true,'visible'=>true,'focused'=>true])];return $out;
}
$spec=[['section_open','initial','10:00:00',[]],['app_presence','initial','10:01:00',['app_active'=>false]],['heartbeat','initial','10:02:00',['app_active'=>false]],['app_presence','initial','10:04:00',[]],['text_changed','initial','10:05:00',['field'=>'decision']],['section_open','final','10:06:00',[]]];
$events=presenceEvents($spec);$original=js($events);$visits=Navigation::build($events)['visits'];
presenceCheck(array_column($visits,'section')===['initial','outside','initial','final'],'outside is an explicit interval between section visits');
presenceCheck(array_column(array_slice($visits,0,3),'duration_seconds')===[60.0,180.0,120.0],'one plus two minutes in section; three minutes outside');
presenceCheck($visits[0]['activity']==='Başlangıç değerlendirmesi görüntüleme'&&$visits[2]['activity']==='Başlangıç değerlendirmesi güncelleme','edits belong only to the resumed interval');
presenceCheck($visits[1]['activity']==='Uygulama dışında'&&$visits[1]['activity_code']==='APP_OUTSIDE','outside has a stable activity name and code');
presenceCheck(js($events)===$original,'raw observations unchanged');
$focus=$events;$focus[1]['type']='focus';$focus[1]['payload']=js(['view_id'=>'presence-view','visible'=>true,'focused'=>false,'app_active'=>false]);
presenceCheck(Navigation::build($focus)['visits'][1]['duration_seconds']===180.0,'leaving for another app also starts outside interval');
$duplicate=$spec;array_splice($duplicate,2,0,[['app_presence','initial','10:01:01',['app_active'=>false]]]);
presenceCheck(count(Navigation::build(presenceEvents($duplicate))['visits'])===4,'duplicate outside notifications do not restart interval');
$open=Navigation::build(presenceEvents(array_slice($spec,0,2)))['visits'];presenceCheck($open[1]['duration_seconds']===null,'missing return never fabricates outside duration');
$locked=Navigation::build(presenceEvents([['section_open','ai','10:00:00',['locked'=>true]],['section_open','initial','10:01:00',[]]]))['visits'][0];
presenceCheck($locked['activity']==='Kilitli AI bölümünü görüntüleme'&&$locked['activity_code']==='AI_LOCKED','locked AI access is distinct from actual chat');
$label=EventLabels::describe(presenceEvents([['section_open','ai','10:00:00',['locked'=>true]]])[0]);presenceCheck($label['type_tr']==='Kilitli AI bölümünü görüntüleme','raw event description also identifies locked AI');
$end=$spec;array_splice($end,4,0,[['workflow_confirmed','initial','10:04:30',['kind'=>'submit','submitted'=>true]]]);
presenceCheck(count(Navigation::build(presenceEvents($end))['visits'])===3,'submission ends tracking including later section entries');
// Actual request and message identifiers are used by the same API/export paths as the UI.
$c=require __DIR__.'/../config/example.php';$c['dsn']='sqlite::memory:';$c['db_user']='';$c['db_password']='';$c['https']=false;
$db=new Db($c);Migration::run($db);Seed::run($db,$c,uid());$app=new App($db,$c);$ex=new Export($app);
$u=$db->one("SELECT * FROM users WHERE id='test-student-3'");$r=$db->one("SELECT * FROM users WHERE id='test-researcher'");$a=$db->one('SELECT * FROM applications WHERE user_id=?',[$u['id']]);
$request=$app->chat($u,$a['id'],['text'=>'SENTETİK bilgi sorusu','key'=>uid()]);
$aiSpec=[['section_open','ai','10:00:00',[]],['message_send','ai','10:00:20',['request_key'=>$request['client_key']]],['app_presence','ai','10:00:30',['app_active'=>false]],['app_presence','ai','10:01:30',['pending_request'=>$request['id']]],['response_visible','ai','10:01:40',['request'=>$request['id'],'status'=>'completed']],['section_open','final','10:02:00',[]]];
$load=function(array $spec)use($db,$a){$db->q('DELETE FROM events WHERE app_id=?',[$a['id']]);foreach(presenceEvents($spec) as $e){$e['id']=uid();$e['client_key']=uid();$e['app_id']=$a['id'];$db->insert('events',$e);}};
$load($aiSpec);$view=$app->view($r,$a['id']);$parts=$view['navigation']['activities'];
$waits=array_values(array_filter($parts,fn($v)=>($v['activity_type']??'')==='ai_wait'));$views=array_values(array_filter($parts,fn($v)=>($v['activity_code']??'')==='AI_VIEW'));
presenceCheck(array_column($waits,'duration_seconds')===[10.0,10.0],'same request wait pauses outside and resumes for ten seconds');
presenceCheck(array_column($views,'duration_seconds')===[20.0,20.0],'return while waiting does not create a false viewing interval');
presenceCheck(count(array_filter($parts,fn($v)=>($v['activity_type']??'')==='ai_question'))===1,'returning does not create a duplicate question');
presenceCheck(count(array_filter($parts,fn($v)=>($v['activity_type']??'')==='ai_response'))===1,'reply recorded once');
presenceCheck(array_sum(array_column(array_filter($view['navigation']['visits'],fn($v)=>$v['section']==='ai'),'duration_seconds'))===60.0,'AI section total is sixty seconds excluding outside minute');
presenceCheck(count(array_unique(array_column($parts,'activity_id')))===count($parts),'presence segments keep unique export identities');
$parse=function($csv){$f=fopen('php://temp','w+');fwrite($f,substr($csv,3));rewind($f);$h=fgetcsv($f,0,',','"','');$rows=[];while(($line=fgetcsv($f,0,',','"',''))!==false)$rows[]=array_combine($h,$line);return $rows;};
$rows=$parse($ex->disco($r,['app_id'=>$a['id']]));
presenceCheck(array_column(array_values(array_filter($rows,fn($v)=>$v['Activity Code']==='AI_WAIT')),'Duration Seconds')===['10','10'],'Disco exports two bounded waits');
$outside=current(array_filter($rows,fn($v)=>$v['Activity Code']==='APP_OUTSIDE'));
presenceCheck($outside['Duration Seconds']==='60'&&$outside['Section']==='Uygulama dışında','Disco contains explicit outside duration');
// Response during absence remains a point and must not open AI viewing while outside.
$hidden=$aiSpec;$hidden[3]=['response_visible','ai','10:01:00',['request'=>$request['id'],'status'=>'completed','app_active'=>false]];$hidden[4]=['app_presence','ai','10:01:30',[]];
$load($hidden);$parts=$app->view($r,$a['id'])['navigation']['activities'];
presenceCheck(array_column(array_values(array_filter($parts,fn($v)=>($v['activity_type']??'')==='ai_wait')),'duration_seconds')===[10.0],'response outside ends pending wait without charging hidden seconds');
$response=current(array_filter($parts,fn($v)=>($v['activity_type']??'')==='ai_response'));
presenceCheck($response['start_at']==='2026-10-21T10:01:00Z'&&str_contains($response['activity'],'Uygulama dışındayken'),'actual hidden reply timestamp and context preserved');
presenceCheck(array_column(array_values(array_filter($parts,fn($v)=>($v['activity_code']??'')==='AI_VIEW')),'duration_seconds')===[20.0,30.0],'viewing resumes only on return after hidden reply');
presenceCheck(count(array_filter($parts,fn($v)=>($v['activity_type']??'')==='ai_response'))===1,'hidden reply not fabricated again on return');
// An open wait must also produce a bounded pause followed by an unknown open end.
$pending=$aiSpec;array_splice($pending,4,1);$load($pending);$parts=$app->view($r,$a['id'])['navigation']['activities'];
$pendingWaits=array_values(array_filter($parts,fn($v)=>($v['activity_type']??'')==='ai_wait'));
presenceCheck(array_column($pendingWaits,'duration_seconds')===[10.0,null],'missing response retains open wait after bounded outside pause');
$late=$aiSpec;$late[1]=['app_presence','ai','10:00:10',['app_active'=>false]];$late[2]=['message_send','ai','10:00:20',['request_key'=>$request['client_key'],'app_active'=>false]];$load($late);$parts=$app->view($r,$a['id'])['navigation']['activities'];
presenceCheck(count(array_filter($parts,fn($v)=>($v['activity_type']??'')==='ai_question'))===1&&count(array_filter($parts,fn($v)=>($v['activity_type']??'')==='ai_question_legacy'))===0,'asynchronous send after departure remains one client-timed question');
presenceCheck(array_column(array_values(array_filter($parts,fn($v)=>($v['activity_type']??'')==='ai_wait')),'duration_seconds')===[10.0],'request started outside only accumulates visible wait after return');
// Repeated departures and exact conservation of visit duration.
$repeat=[['section_open','ai','10:00:00',[]],['message_send','ai','10:00:20',['request_key'=>$request['client_key']]],['app_presence','ai','10:00:30',['app_active'=>false]],['app_presence','ai','10:01:00',['pending_request'=>$request['id']]],['app_presence','ai','10:01:10',['app_active'=>false]],['app_presence','ai','10:01:30',['pending_request'=>$request['id']]],['response_visible','ai','10:01:40',['request'=>$request['id'],'status'=>'completed']],['section_open','final','10:02:00',[]]];
$load($repeat);$repeatView=$app->view($r,$a['id']);$parts=$repeatView['navigation']['activities'];
presenceCheck(array_column(array_values(array_filter($parts,fn($v)=>($v['activity_type']??'')==='ai_wait')),'duration_seconds')===[10.0,10.0,10.0],'two absences produce three waits for the same request');
presenceCheck(array_sum(array_column(array_filter($repeatView['navigation']['visits'],fn($v)=>$v['section']==='ai'),'duration_seconds'))===70.0,'two absences excluded from AI total exactly');
presenceCheck(array_sum(array_column(array_filter($repeatView['navigation']['visits'],fn($v)=>$v['section']==='outside'),'duration_seconds'))===50.0,'outside intervals total fifty seconds');
presenceCheck(count(array_filter($parts,fn($v)=>($v['activity_type']??'')==='ai_question'))===1,'repeated returns never duplicate question');
$repeatCsv=$parse($ex->disco($r,['app_id'=>$a['id']]));
presenceCheck(count(array_unique(array_column($repeatCsv,'Event ID')))===count($repeatCsv),'all repeated wait CSV rows have unique event IDs');
$exact=true;foreach($repeatCsv as $row)if($row['Duration Seconds']!=='')$exact=$exact&&abs((float)$row['Duration Seconds']-(Navigation::time($row['Complete UTC raw'])-Navigation::time($row['Start UTC raw'])))<0.000001;
presenceCheck($exact,'every CSV duration equals its own end minus start');
// Response failure and partial output must retain the same timing rules.
foreach(['failed','partial'] as $status){$variant=$hidden;$variant[3][3]['status']=$status;$load($variant);$parts=$app->view($r,$a['id'])['navigation']['activities'];$point=current(array_filter($parts,fn($v)=>($v['activity_type']??'')==='ai_response'));presenceCheck($point['duration_seconds']===0.0&&!str_starts_with($point['activity'],'AI yanıtı geldi'),'outside '.$status.' response is not labeled successful');presenceCheck(array_sum(array_column(array_filter($parts,fn($v)=>($v['activity_type']??'')==='ai_wait'),'duration_seconds'))===10.0,$status.' wait excludes outside time');}
// Change application section during response preparation, then leave and return.
$cross=[['section_open','ai','10:00:00',[]],['message_send','ai','10:00:10',['request_key'=>$request['client_key']]],['section_open','final','10:00:20',['pending_request'=>$request['id']]],['app_presence','final','10:00:30',['app_active'=>false]],['app_presence','final','10:01:30',['pending_request'=>$request['id']]],['response_visible','final','10:01:40',['request'=>$request['id'],'status'=>'completed']],['section_open','ai','10:02:00',[]],['section_leave','ai','10:02:30',['reason'=>'home']]];
$load($cross);$crossView=$app->view($r,$a['id']);$parts=$crossView['navigation']['activities'];
presenceCheck(array_column(array_values(array_filter($parts,fn($v)=>($v['activity_type']??'')==='ai_wait')),'duration_seconds')===[20.0,10.0],'cross-section wait excludes outside minute without losing final-section work');
presenceCheck(array_sum(array_column(array_filter($crossView['navigation']['visits'],fn($v)=>$v['section']==='final'),'duration_seconds'))===40.0,'final section duration excludes only outside interval');
presenceCheck(count(array_filter($parts,fn($v)=>($v['activity_code']??'')==='AI_VIEW'))===2,'reply in final does not invent AI viewing before actual return');
// Long observation intervals are not subject to the older heartbeat thirty-second limit.
$long=[['section_open','initial','10:00:00',[]],['app_presence','initial','10:01:00',['app_active'=>false]],['app_presence','initial','13:01:00',[]],['section_leave','initial','13:02:00',['reason'=>'home']]];
$longVisits=Navigation::build(presenceEvents($long))['visits'];presenceCheck(array_column($longVisits,'duration_seconds')===[60.0,10800.0,60.0],'three hours outside are retained exactly, never charged to section');
$legacy=presenceEvents($spec);foreach($legacy as &$e){$p=obj($e['payload']);unset($p['app_active']);$p['visible']=!in_array($e['client_seq'],[2,3],true);$e['payload']=js($p);}unset($e);
presenceCheck(array_column(array_slice(Navigation::build($legacy)['visits'],0,3),'duration_seconds')===[60.0,180.0,120.0],'legacy visibility boundaries preserve exact observed durations');
$clock=presenceEvents($spec);$clock[3]['client_at']='2026-10-21T10:00:30Z';$clockVisits=Navigation::build($clock)['visits'];presenceCheck(current(array_filter($clockVisits,fn($v)=>$v['section']==='outside'))['duration_seconds']===null,'backward return timestamp is unknown instead of negative duration');
$missing=presenceEvents($spec);$missing[3]['client_seq']=7;$missing[4]['client_seq']=8;$missing[5]['client_seq']=9;$missingVisits=Navigation::build($missing)['visits'];presenceCheck($missingVisits[1]['missing_events']&&$missingVisits[1]['duration_seconds']===180.0,'missing middle observations are flagged; known outside boundaries stay exact');
$rapid=presenceEvents([['section_open','ai','10:00:00',[]],['app_presence','ai','10:00:00',['app_active'=>false]],['app_presence','ai','10:00:00',[]],['section_leave','ai','10:00:01',['reason'=>'home']]]);presenceCheck(array_column(Navigation::build($rapid)['visits'],'duration_seconds')===[0.0,0.0,1.0],'same-timestamp transitions never produce negative or inflated seconds');
$submitted=presenceEvents([['section_open','confirm','10:00:00',[]],['workflow_confirmed','confirm','10:00:10',['kind'=>'submit','submitted'=>true]],['app_presence','confirm','10:00:20',['app_active'=>false,'submitted'=>true]],['app_presence','confirm','10:01:20',['submitted'=>true]]]);presenceCheck(count(Navigation::build($submitted)['visits'])===1,'departure and return after final submission produce no new durations');
$clockAI=$aiSpec;$clockAI[3][2]='10:00:25';$load($clockAI);$clockView=$app->view($r,$a['id']);$clockWaits=array_values(array_filter($clockView['navigation']['activities'],fn($v)=>($v['activity_type']??'')==='ai_wait'));
presenceCheck(count($clockWaits)>0&&array_sum(array_column($clockWaits,'duration_seconds'))<=80.0,'backward clock at return cannot inflate AI preparation time');
presenceCheck(count(array_filter($clockWaits,fn($v)=>$v['duration_seconds']===null))>0,'AI duration crossing a backward clock is explicitly unknown');
$clockCsv=$parse($ex->disco($r,['app_id'=>$a['id']]));presenceCheck(count(array_filter($clockCsv,fn($v)=>$v['Activity Code']==='AI_WAIT'))===0,'unreliable AI wait is excluded from duration CSV instead of exported as numeric time');
presenceCheck(count(array_filter($clockView['navigation']['visits'],fn($v)=>!empty($v['invalid_clock'])&&$v['duration_seconds']!==null))===0,'all affected section intervals are unknown after backward clock');
$gapSpec=[['section_open','case','10:00:00',['view_id'=>'gap-first']],['section_leave','case','10:01:00',['reason'=>'home','view_id'=>'gap-first']],['section_open','initial','10:04:00',['view_id'=>'gap-second']],['section_leave','initial','10:05:00',['reason'=>'home','view_id'=>'gap-second']]];
$gapVisits=Navigation::build(presenceEvents($gapSpec))['visits'];presenceCheck(array_column($gapVisits,'section')===['case','away','initial'],'home and reopening produce a separate case-away interval');presenceCheck(array_column($gapVisits,'duration_seconds')===[60.0,180.0,60.0],'case-away seconds preserve exact home/reopening boundaries');presenceCheck($gapVisits[1]['activity_code']==='CASE_AWAY'&&$gapVisits[1]['presence_source']==='home_and_reopen','case absence never mislabeled as application absence');presenceCheck(count(Navigation::build(presenceEvents(array_slice($gapSpec,0,2)))['visits'])===1,'unobserved reopening does not fabricate an away end');
$gapReadonly=$gapSpec;$gapReadonly[2][3]['read_only_tab']=true;presenceCheck(count(array_filter(Navigation::build(presenceEvents($gapReadonly))['visits'],fn($v)=>$v['section']==='away'))===0,'read-only parallel tab does not fabricate a case-away interval');
$parallelGap=$gapSpec;array_splice($parallelGap,2,0,[['section_open','initial','10:00:30',['view_id'=>'parallel-view']],['section_leave','initial','10:03:30',['reason'=>'home','view_id'=>'parallel-view']]]);$parallelGapVisits=Navigation::build(presenceEvents($parallelGap))['visits'];presenceCheck(count(array_filter($parallelGapVisits,fn($v)=>$v['section']==='away'&&$v['start_at']==='2026-10-21T10:01:00Z'))===0,'overlapping case window prevents an invented case-away interval');
$staleSpec=$gapSpec;array_unshift($staleSpec,['section_open','final','09:00:00',['view_id'=>'stale-unclosed']]);$staleVisits=Navigation::build(presenceEvents($staleSpec))['visits'];$staleGaps=array_values(array_filter($staleVisits,fn($v)=>$v['section']==='away'));presenceCheck(count($staleGaps)===1&&$staleGaps[0]['duration_seconds']===180.0,'old unclosed trace does not hide an observed home/reopening gap');presenceCheck(count(array_filter($staleVisits,fn($v)=>$v['end_at']===null))===1,'old unclosed trace remains unknown without inventing its end');
echo "TOTAL PRESENCE $count checks passed; isolated in-memory database.\n";
