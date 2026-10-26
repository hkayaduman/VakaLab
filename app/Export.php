<?php
declare(strict_types=1);
final class Export {
    public function __construct(private App $app) {}
    public function reportData(array $u,string $id,bool $studentPreview=false): array {
        $db=$this->app->db;$r=$db->one('SELECT * FROM reports WHERE id=?',[$id]);demand((bool)$r,'Rapor yok',404);$access=$this->app->access($u,$r['app_id']);
        if($u['role']!=='researcher')demand($r['approved_at']&&$r['published_at']&&Scope::collectionClosed($access),'Rapor henüz yayımlanmadı',403);
        if($u['role']!=='researcher'){$latest=$db->one('SELECT id,body FROM reports WHERE app_id=? ORDER BY version DESC LIMIT 1',[$r['app_id']]);if($latest&&(obj($latest['body'])['review_version']??1)>=2)demand($latest['id']===$r['id'],'Güncel raporu kullanın',403);}
        $body=obj($r['body']);if($u['role']!=='researcher'||$studentPreview){$a=$db->one('SELECT draft FROM applications WHERE id=?',[$r['app_id']]);$body=StudentReport::projection($body,obj($a['draft']));}return ['id'=>$r['id'],'version'=>$r['version'],'created_at'=>$r['created_at'],'approved_at'=>$r['approved_at'],'published_at'=>$r['published_at'],'report'=>$body];
    }
    public function dataset(array $u,array $p): array {
        Auth::researcher($u);$db=$this->app->db;$where=['c.owner_id=?'];$args=[$u['id']];
        if(empty($p['include_test']))$where[]='u.test=0 AND c.test=0';if(empty($p['include_practice']))$where[]='c.practice=0';if(empty($p['include_excluded']))$where[]='a.excluded=0';
        if(!empty($p['case_id'])){Scope::owned($db,$u,'cases',$p['case_id']);$where[]='c.id=?';$args[]=$p['case_id'];}if(!empty($p['app_id'])){$where[]='a.id=?';$args[]=$p['app_id'];}if(!empty($p['user_id'])){$where[]='a.user_id=?';$args[]=$p['user_id'];}
        $apps=$db->all('SELECT a.*,u.login,u.name FROM applications a JOIN users u ON u.id=a.user_id JOIN cases c ON c.id=a.case_id WHERE '.implode(' AND ',$where),$args);$coded=!empty($p['coded']);
        $out=['cases'=>[],'case_versions'=>[],'participants'=>[],'assignments'=>[],'reports'=>[],'research_notes'=>[],'applications'=>[],'events'=>[],'server_events'=>[],'visits'=>[],'navigation_visits'=>[],'navigation_activities'=>[],'navigation_milestones'=>[],'messages'=>[],'labels'=>[],'label_reviews'=>[],'label_batches'=>[],'drafts'=>[],'snapshots'=>[],'ai_requests'=>[],'ai_attempts'=>[],'analyses'=>[],'decisions'=>[],'definitions'=>[],'dictionary'=>[['field'=>'times','meaning'=>'UTC ISO 8601; client_at ayrı, received_at sunucu'],['field'=>'quality','meaning'=>'Gözlenen görünürlük okuma/düşünme değildir; AI süreleri öğrenciyle toplanmaz'],['field'=>'coded','meaning'=>'Kodlanmış çıktı metinleri ve kimlik eşlemesini içermez']]];
        if(!$coded){
            $out['cases']=array_values(array_filter($db->all('SELECT * FROM cases WHERE owner_id=?',[$u['id']]),fn($c)=>(empty($p['case_id'])||$c['id']===$p['case_id'])&&(empty($p['app_id'])||in_array($c['id'],array_column($apps,'case_id'),true))&&(!empty($p['include_test'])||!$c['test'])&&(!empty($p['include_practice'])||!$c['practice'])));
            $cids=array_column($out['cases'],'id');
            $out['case_versions']=array_values(array_filter($db->all('SELECT v.* FROM case_versions v JOIN cases c ON c.id=v.case_id WHERE c.owner_id=?',[$u['id']]),fn($v)=>in_array($v['case_id'],$cids,true)));
            $out['assignments']=array_values(array_filter($db->all('SELECT s.* FROM assignments s JOIN cases c ON c.id=s.case_id WHERE c.owner_id=?',[$u['id']]),fn($s)=>in_array($s['case_id'],$cids,true)&&(empty($p['user_id'])||$s['user_id']===$p['user_id'])&&(empty($p['app_id'])||in_array($s['user_id'],array_column($apps,'user_id'),true))));
            $sids=array_column($out['assignments'],'user_id');
            $out['participants']=array_values(array_filter($db->all("SELECT id,login,name,status,metadata,test FROM users WHERE owner_id=? AND role='student'",[$u['id']]),fn($s)=>in_array($s['id'],$sids,true)));
        }
        $out['dictionary']=array_merge($out['dictionary'],EventLabels::dictionary());
        foreach($apps as $a){$id=$a['id'];foreach($db->all("SELECT id,detail,created_at FROM audit WHERE target=? AND action='message_labels_ai' ORDER BY created_at,id",[$id]) as $batch){if($coded)$batch['detail']=js(array_intersect_key(obj($batch['detail']),array_flip(['batch_id','labels','target_role','message_scope'])));$out['label_batches'][]=$batch+['app_id'=>$id];}if(!$coded){foreach(['reports','research_notes'] as $t)foreach($db->all('SELECT * FROM '.$t.' WHERE app_id=?',[$id]) as $row)$out[$t][]=$row;}$code='P-'.substr(hash('sha256',$a['user_id']),0,12);$out['applications'][]=$coded?['app_id'=>$id,'participant'=>$code,'case_id'=>$a['case_id'],'attempt_no'=>(int)$a['attempt_no'],'state'=>$a['state'],'revision'=>$a['revision'],'submitted_at'=>$a['submitted_at']]:$a;
            foreach($db->all('SELECT id,action,target,detail,created_at FROM audit WHERE target=? ORDER BY created_at',[$id]) as $serverEvent){if($coded)$serverEvent['detail']='[çıkarıldı]';$out['server_events'][]=$serverEvent;}
            foreach($db->all('SELECT t.* FROM ai_attempts t JOIN ai_requests r ON r.id=t.request_id WHERE r.app_id=?',[$id]) as $attempt)$out['ai_attempts'][]=$attempt;
            $events=$db->all('SELECT * FROM events WHERE app_id=? ORDER BY received_at,client_seq',[$id]);$navigation=Navigation::build($events,$a['submitted_at']);foreach($navigation['visits'] as $v)$out['navigation_visits'][]=['app_id'=>$id]+$v;foreach($navigation['milestones'] as $m)$out['navigation_milestones'][]=['app_id'=>$id]+$m;foreach($events as $e){$e=EventLabels::describe($e);if($coded){$e['payload']='[metin ve cihaz ayrıntıları çıkarıldı]';unset($e['session_id'],$e['tab_id']);}$out['events'][]=$e;}
            foreach(Telemetry::visits($events)['visits'] as $v)$out['visits'][]=['app_id'=>$id]+$v;
            foreach($db->all('SELECT * FROM messages WHERE app_id=? ORDER BY seq',[$id]) as $m){if($coded)$m['content']='[kodlanmış çıktıda metin çıkarıldı]';$out['messages'][]=$m;
                foreach($db->all('SELECT * FROM message_labels WHERE message_id=?',[$m['id']]) as $l){if($coded){unset($l['actor']);$l['reason']='[çıkarıldı]';}$out['labels'][]=$l;}
                foreach($db->all('SELECT * FROM label_reviews WHERE message_id=?',[$m['id']]) as $review){if($coded){unset($review['actor']);$review['reason']='[çıkarıldı]';}$out['label_reviews'][]=$review;}}
            foreach(['draft_revisions'=>'drafts','snapshots'=>'snapshots','ai_requests'=>'ai_requests','analysis_runs'=>'analyses'] as $table=>$key){foreach($db->all('SELECT * FROM '.$table.' WHERE app_id=?',[$id]) as $row){if($coded){foreach(['body','payload','error'] as $k)if(isset($row[$k]))$row[$k]='[çıkarıldı]';}$out[$key][]=$row;}}
            foreach($db->all('SELECT d.* FROM decisions d JOIN analysis_steps s ON s.id=d.step_id JOIN analysis_runs r ON r.id=s.run_id WHERE r.app_id=?',[$id]) as $d){if($coded){unset($d['actor']);$d['body']='[çıkarıldı]';$d['reason']='[çıkarıldı]';}$out['decisions'][]=$d;}
            foreach($db->all('SELECT s.* FROM analysis_steps s JOIN analysis_runs r ON r.id=s.run_id WHERE r.app_id=?',[$id]) as $s){if($coded){$s['input']='[çıkarıldı]';$s['output']='[çıkarıldı]';}$out['analyses'][]=$s;}
        }
        if(!$coded){
            $ids=array_merge(array_column($out['labels'],'definition_id'),array_column($out['analyses'],'rubric_id'),array_column($out['cases'],'rubric_id'));
            $defs=$db->all('SELECT * FROM definitions WHERE owner_id=?',[$u['id']]);
            do{$before=count($ids);foreach($defs as $d)if(in_array($d['id'],$ids,true)&&$d['parent_id']&&!in_array($d['parent_id'],$ids,true))$ids[]=$d['parent_id'];}while(count($ids)>$before);
            $out['definitions']=array_values(array_filter($defs,fn($d)=>in_array($d['id'],$ids,true)));
        }
        $navigationDefinitions=$db->all("SELECT id,name FROM definitions WHERE owner_id=? AND family='label'",[$u['id']]);$out['navigation_visits']=Navigation::labelVisits($out['navigation_visits'],$out,$navigationDefinitions);$out['navigation_activities']=Navigation::activities($out['navigation_visits'],$out,$navigationDefinitions);
        $db->audit($u['id'],'export','dataset','',['coded'=>$coded,'count'=>count($apps),'include_test'=>!empty($p['include_test'])]);return $out;
    }
    public static function csv(array $rows): string {
        $f=fopen('php://temp','w+');fwrite($f,"\xEF\xBB\xBF");$keys=[];foreach($rows as $r)$keys=array_values(array_unique(array_merge($keys,array_keys($r))));fputcsv($f,$keys,',','"','');
        foreach($rows as $r)fputcsv($f,array_map(fn($k)=>Rules::csv(is_array($r[$k]??null)?js($r[$k]):(string)($r[$k]??'')),$keys),',','"','');rewind($f);return stream_get_contents($f);
    }
    public function disco(array $u,array $p): string {
        $view=$p['view']??'visits';demand(in_array($view,['visits','events'],true),'Disco görünümü geçersiz');if($view==='visits')return $this->navigationCsv($u,$p);
        // One process instance is one student application, never the pedagogical case alone.
        $data=$this->dataset($u,$p+['include_test'=>1,'include_practice'=>1,'include_excluded'=>1,'coded'=>1]);
        $apps=array_column($data['applications'],null,'app_id');$rows=[];$definitions=array_column($this->app->db->all("SELECT id,name FROM definitions WHERE family='label' AND owner_id=?",[$u['id']]),'name','id');
        $append=function(array $a,string $activity,string $start,string $end,string $resource,string $event,string $quality,string $source,int $seq=0,array $extra=[])use(&$rows){
            try{$s=new DateTimeImmutable($start);$e=new DateTimeImmutable($end);}catch(Throwable){return;}
            $fmt=fn($d)=>$d->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s.v');
            $extra+=['Message ID'=>'','Message Role'=>'','AI Labels'=>'','Researcher Labels'=>'','Effective Labels'=>'','Label Source'=>'','Label Status'=>''];
            $rows[]=['Case ID'=>$a['app_id'],'Activity'=>$activity,'Start Timestamp'=>$fmt($s),'Complete Timestamp'=>$fmt($e),'Resource'=>$resource,
                'Vaka ID'=>$a['case_id'],'Deneme numarası'=>(int)$a['attempt_no'],'Participant'=>$a['participant'],'Event ID'=>$event,'Sequence'=>$seq,'Time Zone'=>'UTC',
                'Time Source'=>$source,'Quality'=>$quality,'Start UTC raw'=>$start,'Complete UTC raw'=>$end]+$extra;
        };
        foreach($data['events'] as $event){
            $finished=$apps[$event['app_id']]['submitted_at']??null;$eventTime=Navigation::time($event['client_at']);if($finished&&($eventTime===null||$eventTime>Navigation::time($finished)))continue;
            if(!in_array($event['type'],['section_open','text_changed','profile_changed','question_changed','copy','paste','response_visible','recovered'],true))continue;
            $time=$event['client_at'];$source='client_at';
            if(!$time||!preg_match('/^\d{4}-\d{2}-\d{2}T.*(?:Z|[+-]\d{2}:\d{2})$/',$time)){$time=$event['received_at'];$source='received_at_fallback';}
            try{new DateTimeImmutable($time);}catch(Throwable){$time=$event['received_at'];$source='received_at_fallback';}
            $a=$apps[$event['app_id']];$append($a,$event['section_tr'].' · '.$event['type_tr'],$time,$time,$a['participant'],$event['id'],$event['quality'],$source,(int)$event['client_seq']);
        }
        foreach($data['snapshots'] as $snap){
            $a=$apps[$snap['app_id']];$append($a,$snap['kind']==='initial'?'Başlangıç kesinleştirildi':'Son teslim yapıldı',$snap['created_at'],$snap['created_at'],$a['participant'],$snap['id'],'server_confirmed','server');
        }
        foreach($data['messages'] as $message){
            $a=$apps[$message['app_id']];$state=MessageLabels::state($message['id'],$data['labels'],$data['label_reviews'],array_values(array_filter($data['label_batches'],fn($b)=>$b['app_id']===$a['app_id'])));
            $names=fn($ids)=>implode(' | ',array_map(fn($id)=>$definitions[$id]??$id,$ids));
            $append($a,$message['role']==='user'?($state['effective']?'AI isteği · '.$names($state['effective']):'AI isteği · Etiketlenmedi'):'AI yanıtı',$message['created_at'],$message['created_at'],$message['role']==='user'?$a['participant']:'AI',$message['id'],'server_recorded','server',(int)$message['seq'],['Message ID'=>$message['id'],'Message Role'=>$message['role'],'AI Labels'=>$names($state['ai']),'Researcher Labels'=>$names($state['researcher']),'Effective Labels'=>$names($state['effective']),'Label Source'=>$state['source'],'Label Status'=>$state['reviewed']?'Araştırmacı tarafından incelendi':($state['ai_known']?'Araştırmacı incelemesi bekliyor':'AI etiketleme yapılmadı')]);
        }
        foreach($data['ai_requests'] as $request){
            $a=$apps[$request['app_id']];
            // Keep each attempt separately, including failures; never infer completion for a running request.
            foreach($data['ai_attempts'] as $attempt)if($attempt['request_id']===$request['id']){
                if(empty($attempt['completed_at']))continue;
                $append($a,'AI yanıtı · '.$attempt['status'],$attempt['started_at'],$attempt['completed_at'],'AI',$attempt['id'],'server_provider_interval','server');
            }
        }
        usort($rows,fn($a,$b)=>[$a['Case ID'],$a['Start Timestamp'],$a['Sequence'],$a['Event ID']]<=>[$b['Case ID'],$b['Start Timestamp'],$b['Sequence'],$b['Event ID']]);
        if(!$rows){$f=fopen('php://temp','w+');fwrite($f,"\xEF\xBB\xBF");fputcsv($f,['Case ID','Activity','Start Timestamp','Complete Timestamp','Resource','Vaka ID','Deneme numarası','Participant','Event ID','Sequence','Time Zone','Time Source','Quality','Start UTC raw','Complete UTC raw','Message ID','Message Role','AI Labels','Researcher Labels','Effective Labels','Label Source','Label Status'],',','"','');rewind($f);return stream_get_contents($f);}
        return self::csv($rows);
    }
    private function navigationCsv(array $u,array $p): string {
        $data=$this->dataset($u,$p+['include_test'=>1,'include_practice'=>1,'include_excluded'=>1,'coded'=>1]);$apps=array_column($data['applications'],null,'app_id');$rows=[];
        $format=fn($t)=>(new DateTimeImmutable($t))->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:s.v');
        foreach($data['navigation_activities'] as $v){
            if($v['duration_seconds']===null&&($v['activity_type']??'')!=='ai_question_legacy')continue;$a=$apps[$v['app_id']];$questions=$v['ai_question_count']??0;
            $rows[]=['Case ID'=>$v['app_id'],'Activity'=>$v['activity'],'Start Timestamp'=>$format($v['start_at']),'Complete Timestamp'=>$format($v['end_at']??$v['start_at']),'Resource'=>$a['participant'],'Vaka ID'=>$a['case_id'],'Deneme numarası'=>(int)$a['attempt_no'],'Participant'=>$a['participant'],'Event ID'=>$v['activity_id']??$v['visit_id'],'Parent Visit ID'=>$v['visit_id'],'Message ID'=>$v['message_id']??'','Message Sequence'=>$v['message_sequence']??'','Activity Code'=>$v['activity_code'],'Row Type'=>$v['activity_type']??'visit','Measurement Version'=>$v['measurement_version']??'legacy_message','Presence Source'=>$v['presence_source']??'unobserved','Timeline Lane'=>$v['timeline_lane']??'student','Response Section'=>$v['response_section']??'','Duration Seconds'=>$v['duration_seconds'],'Section'=>EventLabels::SECTIONS[$v['section']]??$v['section'],'Edited Fields'=>implode(' | ',$v['edited_fields']),'Trace ID'=>$v['trace_id'],'Visit Number'=>$v['visit_number'],'Sequence'=>$v['sequence'],'_order'=>$v['start_sequence'],'_boundary_order'=>$v['boundary_order']??0,'End Reason'=>$v['end_reason'],'Next Section'=>$v['next_section']??'','Next Section Name'=>$v['next_section_name']??'','Locked'=>$v['locked']?'yes':'no','Time Zone'=>'UTC','Time Source'=>$v['timestamp_source']??'client_navigation','Quality'=>$v['quality'],'Background Observed'=>$v['background_seen']===null?'unknown':($v['background_seen']?'yes':'no'),'Read Only Tab'=>$v['read_only_tab']?'yes':'no','Overlapping Tab'=>$v['overlapping_tab']?'yes':'no','AI Question Count'=>$questions,'Response Arrival Observed'=>isset($v['response_observed'])?($v['response_observed']?'yes':'no'):'not_applicable','AI Label Match Quality'=>($v['activity_type']??'')==='ai_question_legacy'?'server_message_matched':(!empty($v['message_id'])?'request_key_matched':'not_observed_for_this_visit'),'AI Labels'=>implode(' | ',$v['ai_labels']??[]),'Researcher Labels'=>implode(' | ',$v['researcher_labels']??[]),'Effective Labels'=>implode(' | ',$v['effective_labels']??[]),'Label Source'=>implode(' | ',$v['label_sources']??[]),'Start UTC raw'=>$v['start_at'],'Complete UTC raw'=>$v['end_at']??$v['start_at']];
        }

        foreach($apps as $id=>$a){
            foreach(['initial','submit'] as $kind){$client=array_values(array_filter($data['navigation_milestones'],fn($m)=>$m['app_id']===$id&&$m['kind']===$kind));$snapshot=current(array_filter($data['snapshots'],fn($s)=>$s['app_id']===$id&&$s['kind']===$kind));$m=$client[0]??($snapshot?['id'=>$snapshot['id'],'at'=>$snapshot['created_at'],'trace_id'=>'','source'=>'server_snapshot']:null);if(!$m||Navigation::time($m['at'])===null)continue;
                $rows[]=['Case ID'=>$id,'Activity'=>$kind==='initial'?'Başlangıcı kesinleştirme':'Teslim','Start Timestamp'=>$format($m['at']),'Complete Timestamp'=>$format($m['at']),'Resource'=>$a['participant'],'Vaka ID'=>$a['case_id'],'Deneme numarası'=>(int)$a['attempt_no'],'Participant'=>$a['participant'],'Event ID'=>$m['id'],'Activity Code'=>$kind==='initial'?'INITIAL_CONFIRMED':'SUBMITTED','Duration Seconds'=>0,'Section'=>$kind==='initial'?'Başlangıç değerlendirmesi':'Son teslim','Trace ID'=>$m['trace_id'],'Visit Number'=>'','Sequence'=>0,'End Reason'=>'confirmed','Time Zone'=>'UTC','Time Source'=>$m['source'],'Quality'=>'confirmed_milestone','Start UTC raw'=>$m['at'],'Complete UTC raw'=>$m['at'],'_order'=>$m['sequence']??PHP_INT_MAX];
            }
        }
        // At equal boundaries a completed visit precedes its marker, which precedes the next visit.
        usort($rows,fn($a,$b)=>[$a['Case ID'],Navigation::time($a['Start UTC raw']),$a['_order'],$a['_boundary_order']??0,$a['Event ID']]<=>[$b['Case ID'],Navigation::time($b['Start UTC raw']),$b['_order'],$b['_boundary_order']??0,$b['Event ID']]);
        $sequence=[];foreach($rows as $i=>&$row){$id=$row['Case ID'];unset($row['_order'],$row['_boundary_order']);$row['Sequence']=($sequence[$id]??0)+1;$sequence[$id]=$row['Sequence'];$row['Previous Activity']=$i&&$rows[$i-1]['Case ID']===$id?$rows[$i-1]['Activity']:'';$row['Next Activity']=isset($rows[$i+1])&&$rows[$i+1]['Case ID']===$id?$rows[$i+1]['Activity']:'';$row['Excluded Unclosed Visits']=count(array_filter($data['navigation_visits'],fn($v)=>$v['app_id']===$id&&$v['duration_seconds']===null));}unset($row);
        if(!$rows){$f=fopen('php://temp','w+');fwrite($f,"\xEF\xBB\xBF");fputcsv($f,['Case ID','Activity','Start Timestamp','Complete Timestamp','Resource','Vaka ID','Deneme numarası','Participant','Activity Code','Duration Seconds','Sequence','Effective Labels'],',','"','');rewind($f);return stream_get_contents($f);}
        return self::csv($rows);
    }
    public static function xlsx(array $data,string $path): void {
        demand(class_exists('PhpOffice\\PhpSpreadsheet\\Spreadsheet'),'Vendor kurulmadı',503);$book=new \PhpOffice\PhpSpreadsheet\Spreadsheet();$index=0;
        foreach($data as $name=>$rows){$sheet=$index++===0?$book->getActiveSheet():$book->createSheet();$sheet->setTitle(substr($name,0,31));$keys=[];foreach($rows as $r)$keys=array_values(array_unique(array_merge($keys,array_keys($r))));$all=[$keys];foreach($rows as $r)$all[]=array_map(fn($k)=>is_array($r[$k]??null)?js($r[$k]):(string)($r[$k]??''),$keys);
            foreach($all as $ri=>$row)foreach($row as $ci=>$value)$sheet->setCellValueExplicit([$ci+1,$ri+1],$value,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);$sheet->freezePane('A2');$sheet->getDefaultColumnDimension()->setWidth(24);}
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($book))->save($path);$book->disconnectWorksheets();clearstatcache(true,$path);
    }
    public static function reportXlsx(array $reports,string $path): void {
        $rows=[];
        foreach($reports as $r){$d=$r['report'];if(!in_array($d['format']??'',['student-v1','student-v2','student-v3'],true)){$d=$d['student_report']??StudentReport::build($d,[]);}
            $base=['Katılımcı'=>$d['participant'],'Vaka'=>$d['case_title'],'Rubrik'=>$d['rubric_name']];
            if(($d['format']??'')==='student-v3'){
                $rows[]=$base+['Bölüm'=>'Bütüncül değerlendirme raporu','Soru'=>'','Puan'=>$d['total'],'En yüksek puan'=>$d['max'],'Geri bildirim'=>$d['overall_feedback']];
                foreach($d['profile'] as $profile)$rows[]=$base+['Bölüm'=>'Başlangıç–nihai profil karşılaştırması','Soru'=>$profile['title'],'Başlangıç'=>$profile['initial'],'Nihai'=>$profile['final_collected']?$profile['final']:'Bu aşamada sorulmadı','Değişim'=>$profile['change']];
            }else
            $rows[]=$base+['Bölüm'=>'Vaka profilindeki değişim','Soru'=>'','Puan'=>'','En yüksek puan'=>'','Geri bildirim'=>$d['overall_feedback']];
            foreach($d['sections'] as $section){
                if(($d['report_layout']??'')==='questions-v1'){
                    $rows[]=$base+['Bölüm'=>$section['section'],'Soru'=>$section['title'],'Öğrencinin yanıtı'=>$section['answer'],'Puan'=>$section['total'],'En yüksek puan'=>$section['max'],'Geri bildirim'=>$section['feedback']];
                    foreach($section['criteria'] as $criterion)$rows[]=$base+['Bölüm'=>$section['section'],'Soru'=>$section['title'],'Ölçüt'=>$criterion['name'],'Düzey'=>$criterion['level'],'Puan'=>$criterion['score'],'En yüksek puan'=>$criterion['max'],'Ağırlık'=>$criterion['weight'],'Geri bildirim'=>$criterion['feedback']];
                }else foreach($section['criteria'] as $q)$rows[]=$base+['Bölüm'=>$section['title'],'Soru'=>$q['name'],'Puan'=>$q['score']??'Puanlanmadı','En yüksek puan'=>$q['max']??'','Geri bildirim'=>$q['feedback']];
            }
        }
        self::xlsx(['Öğrenci raporu'=>$rows],$path);
    }
    public static function reportFile(array $reports,string $format,string $path): void {
        if($format==='docx'){
            demand(class_exists('PhpOffice\\PhpWord\\PhpWord'),'Vendor kurulmadı',503);
            \PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);
            $w=new \PhpOffice\PhpWord\PhpWord();$w->setDefaultFontName('Calibri');$w->setDefaultFontSize(11);
            $w->setDefaultParagraphStyle(['spaceAfter'=>100,'lineHeight'=>1.12,'widowControl'=>true]);
            $w->addTitleStyle(0,['name'=>'Calibri','bold'=>true,'size'=>23,'color'=>'000000'],['spaceAfter'=>180,'keepNext'=>true]);
            $w->addTitleStyle(1,['name'=>'Calibri','bold'=>true,'size'=>14,'color'=>'000000'],['spaceBefore'=>220,'spaceAfter'=>110,'keepNext'=>true]);
            foreach($reports as $r){
                $s=$w->addSection(['pageSizeW'=>11906,'pageSizeH'=>16838,'marginTop'=>1000,'marginBottom'=>1000,'marginLeft'=>1100,'marginRight'=>1100]);
                $s->addTitle('VakaLab öğrenci değerlendirme raporu',0);
                $blocks=self::blocks($r);
                foreach($blocks as $blockIndex=>$block){
                    $type=$block['type'];
                    if($type==='table'){
                        $t=$s->addTable(['borderSize'=>4,'borderColor'=>'DCE4DE','cellMargin'=>130,'layout'=>'fixed','width'=>9706,'unit'=>'dxa']);
                        $width=(int)floor(9706/count($block['rows'][0]));
                        foreach($block['rows'] as $index=>$row){
                            $t->addRow(null,['tblHeader'=>$index===0,'cantSplit'=>true]);
                            foreach($row as $cell)$t->addCell($width,['bgColor'=>$index===0?'E6F0E9':'FFFFFF','valign'=>'center'])->addText((string)$cell,['bold'=>$index===0,'color'=>'183B40'],['spaceAfter'=>30,'spaceBefore'=>30]);
                        }
                        $s->addTextBreak(1,null,['spaceAfter'=>0,'spaceBefore'=>0,'lineHeight'=>0.4]);
                    }elseif(in_array($type,['heading','section'],true)){
                        $s->addTitle($block['text'],1);
                    }else{
                        $font=match($type){'score'=>['bold'=>true,'size'=>24,'color'=>'246852'],'label'=>['bold'=>true,'size'=>11,'color'=>'000000'],'question-score','subtotal'=>['bold'=>true,'size'=>11,'color'=>'246852'],default=>['color'=>'283E42']};
                        $paragraph=['spaceAfter'=>120,'keepNext'=>in_array($type,['score','label','answer','question-score'],true)||($blocks[$blockIndex+1]['type']??'')==='subtotal'];
                        if($type==='score')$paragraph+=['spaceBefore'=>180,'shading'=>['fill'=>'E6F0E9']];
                        if($type==='label')$paragraph+=['spaceBefore'=>100];
                        if($type==='answer')$paragraph+=['shading'=>['fill'=>'F3F5F7'],'indentation'=>['left'=>140,'right'=>140]];
                        if($type==='feedback')$paragraph+=['shading'=>['fill'=>'FFF8E9'],'indentation'=>['left'=>140,'right'=>140]];
                        if(in_array($type,['question-score','subtotal'],true))$paragraph+=['shading'=>['fill'=>'E6F0E9']];
                        $s->addText($block['text'],$font,$paragraph);
                    }
                }
            }
            (new \PhpOffice\PhpWord\Writer\Word2007($w))->save($path);
        }elseif($format==='pdf'){
            demand(class_exists('Mpdf\\Mpdf'),'Vendor kurulmadı',503);$m=new \Mpdf\Mpdf(['tempDir'=>__DIR__.'/../storage','default_font'=>'dejavusans','default_font_size'=>10]);$escape=fn($x)=>htmlspecialchars((string)$x,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');
            $html='<style>h1{color:#183b40;font-size:22pt}h2{color:#246852;font-size:13pt;margin-top:22px}h2,h3{page-break-after:avoid}h3{font-size:10pt;color:#000;margin-top:10px;margin-bottom:5px}p{line-height:1.5;margin:8px 0}div{margin:0;padding:0}table{border-collapse:collapse;width:100%;margin:12px 0}td{border:1px solid #dce4de;padding:9px}tr:first-child{background:#edf3ef;font-weight:bold}</style>';
            foreach($reports as $i=>$r){
                if($i)$html.='<pagebreak />';$html.='<h1>VakaLab — Vaka Raporu</h1>';$keep=false;
                foreach(self::blocks($r) as $b){
                    // Keep consecutive question/answer labels with their following paragraph.
                    if($b['type']==='label'&&!$keep){$html.='<div style="page-break-inside:avoid">';$keep=true;}
                    if($b['type']==='table'){$html.='<table>';foreach($b['rows'] as $row){$html.='<tr>';foreach($row as $cell)$html.='<td>'.$escape($cell).'</td>';$html.='</tr>';}$html.='</table>';}
                    else{$tag=in_array($b['type'],['heading','section'],true)?'h2':($b['type']==='label'?'h3':'p');$style=$b['type']==='section'?' style="page-break-before:always"':'';$html.="<$tag$style>".nl2br($escape($b['text']))."</$tag>";}
                    if($keep&&$b['type']!=='label'){$html.='</div>';$keep=false;}
                }
                if($keep)$html.='</div>';
            }
            $m->WriteHTML($html);$m->Output($path,\Mpdf\Output\Destination::FILE);
        }else throw new Fault('Dosya biçimi geçersiz');
    }
    private static function blocks(array $r): array {
        $d=$r['report'];if(in_array($d['format']??'',['student-v1','student-v2','student-v3'],true))return StudentReport::blocks($r);if(($d['review_version']??1)===3){$copy=$r;$copy['report']=StudentReport::projection($d,[]);return StudentReport::blocks($copy);}if(($d['review_version']??1)===2){$copy=$r;$copy['report']=StudentReport::projection($d,[]);$blocks=StudentReport::blocks($copy);$blocks[]=['type'=>'heading','text'=>'Araştırmacı iç inceleme eki'];foreach($d['parts'] as $part)$blocks[]=['type'=>'paragraph','text'=>'Bölüm '.$part['step'].' · Karar: '.$part['action']];foreach($d['internal']['notes']??[] as $note)$blocks[]=['type'=>'paragraph','text'=>$note['body']];return $blocks;}$b=[];$p=function(string $s)use(&$b){$b[]=['type'=>'paragraph','text'=>$s];};$h=function(string $s)use(&$b){$b[]=['type'=>'heading','text'=>$s];};$score=fn($x)=>$x===null?'Karar verilemedi':(string)$x;
        $p('Rapor sürümü: '.$r['version'].' · Oluşturma (UTC): '.$r['created_at']);$h('Katılımcı ve vaka');$p('Katılımcı: '.($d['participant']??'—').' · '.($d['case_title']??'Vaka').' · Vaka sürümü: '.($d['case_version']??'—'));$p('Rubrik: '.($d['rubric_name']??'—').' · Sürüm: '.($d['rubric_number']??'—'));$p($d['notice']);
        $h('Veri bütünlüğü ve teknik sınırlar');$p($d['limitations']??'Teknik ölçümler sınırlıdır.');$h('Başlangıç profili');$labels=['knowledge'=>'Ön bilgi','confidence'=>'Başlangıç güveni','difficulty'=>'Algılanan zorluk','severity'=>'Algılanan ciddiyet'];foreach($d['profile']??[] as $k=>$v)$p(($labels[$k]??$k).': '.$v.' / 10');
        $h('Başlangıç ve nihai güven');$c=$d['confidence'];$b[]=['type'=>'table','rows'=>[['Başlangıç','Nihai','Fark'],[$c['initial_confidence'],$c['final_confidence'],sprintf('%+d',$c['difference'])]]];
        $titles=[3=>'Başlangıç rubriği',4=>'Nihai rubriği',5=>'Dönüşüm karşılaştırması',6=>'Etkileşim ve etiketler',7=>'Yansıtma karşılaştırması'];$actions=['accept'=>'Kabul edildi','edit'=>'Düzenlenerek onaylandı','reject'=>'Reddedildi','uncertain'=>'Karar verilemedi'];$dims=array_column($d['dimensions']??[],'name','id');
        foreach($d['parts'] as $part){$h($titles[$part['step']]);$p('Araştırmacı kararı: '.$actions[$part['action']]);$approved=$part['approved'];if(isset($approved['scores'])){$rows=[['Boyut','Onaylı puan','Düzey']];foreach($approved['scores'] as $key=>$s){$rows[]=[$dims[$key]??$key,$s['score'],$s['level']];}$b[]=['type'=>'table','rows'=>$rows];foreach($approved['scores'] as $key=>$s){$p(($dims[$key]??$key).' — '.$s['reason']);$p('Kanıt: “'.$s['quote'].'”');}$p('Ağırlıklı ortalama: '.$score($approved['weighted_mean']??null));}elseif(isset($approved['summary'])){$p($approved['summary']);foreach($approved['evidence']??[] as $e)$p('Kanıt: “'.$e['quote'].'”');}$p('Karar gerekçesi: '.$part['reason']);}
        $h('Onaylı puan karşılaştırması');$b[]=['type'=>'table','rows'=>[['Başlangıç ortalaması','Nihai ortalama','Fark'],[$score($d['initial_mean']),$score($d['final_mean']),$score($d['difference'])]]];
        if(isset($d['internal'])){$h('Araştırmacı iç eki — öğrenci kopyasına dahil edilmez');foreach($d['internal']['notes'] as $note){$p('İç not: '.$note['body']);$p('Gerekçe: '.$note['reason'].' · '.$note['created_at']);}foreach($d['internal']['suggestions'] as $s){$p('Adım '.$s['step'].' · Deneme '.$s['attempt'].' · '.$s['status']);$p('AI önerisi / uygulama hesabı: '.$s['output']);}}
        if(isset($d['unicode_long_text'])){$h('Uzun metin sınama eki (sentetik)');$p($d['unicode_long_text']);}return $b;
    }
}
