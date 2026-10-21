<?php
declare(strict_types=1);
// Presence-bounded visits. Outside-app intervals are explicit, never charged to a section.
final class Navigation {
    public const TABS=['case'=>'Vaka','profile'=>'Vaka profili','initial'=>'Başlangıç değerlendirmesi','ai'=>'AI sohbeti','final'=>'Nihai değerlendirme','reflection'=>'Yansıtma','confirm'=>'Son teslim','outside'=>'Uygulama dışında','away'=>'Vaka dışında'];
    public static function time(?string $value): ?float {
        if(!$value||!preg_match('/^\d{4}-\d{2}-\d{2}T.*(?:Z|[+-]\d{2}:\d{2})$/',$value))return null;
        try{$date=new DateTimeImmutable($value);$errors=DateTimeImmutable::getLastErrors();if($errors&&($errors['warning_count']||$errors['error_count']))return null;return (float)$date->format('U.u');}catch(Throwable){return null;}
    }
    // Derive visit names from actual submitted messages; a typed draft is still viewing.
    public static function labelVisits(array $visits,array $data,array $definitions): array {
        $names=array_column($definitions,'name','id');
        $resolve=fn($ids)=>array_values(array_unique(array_map(fn($id)=>$names[$id]??'Adı bulunamayan etiket',$ids)));
        foreach($visits as &$v){
            if($v['locked']||($v['section']!=='ai'&&empty($v['request_keys'])))continue;
            $requests=array_filter($data['ai_requests'],fn($r)=>in_array($r['client_key'],$v['request_keys'],true)&&(!isset($v['app_id'])||$r['app_id']===$v['app_id']));
            $requests=array_values($requests);usort($requests,fn($a,$b)=>array_search($a['client_key'],$v['request_keys'],true)<=>array_search($b['client_key'],$v['request_keys'],true));
            $requestIds=array_column($requests,'id');$ai=[];$researcher=[];$effective=[];$sources=[];$questions=0;$activities=[];
            $messages=array_values(array_filter($data['messages'],fn($m)=>$m['role']==='user'&&in_array($m['request_id']??null,$requestIds,true)&&(!isset($v['app_id'])||$m['app_id']===$v['app_id'])));
            usort($messages,fn($a,$b)=>[array_search($a['request_id'],$requestIds,true),(int)$a['seq']]<=>[array_search($b['request_id'],$requestIds,true),(int)$b['seq']]);
            foreach($messages as $m){
                $batches=array_values(array_filter($data['label_batches'],fn($b)=>!isset($b['app_id'])||$b['app_id']===$m['app_id']));
                $state=MessageLabels::state($m['id'],$data['labels'],$data['label_reviews'],$batches);
                $request=current(array_filter($requests,fn($r)=>$r['id']===$m['request_id']));$send=current(array_filter($v['message_sends']??[],fn($e)=>$e['request_key']===$request['client_key']));
                $activity='AI sohbeti · '.($state['effective']?implode(' + ',$resolve($state['effective'])):'Mesaj gönderdi · '.($state['source']==='none'?'Etiket henüz yok':($state['source']==='researcher'?'Etiket seçilmedi':'Eşleşen etiket yok')));
                $activities[]=['activity'=>$activity,'request_id'=>$m['request_id'],'message_id'=>$m['id'],'message_sequence'=>(int)$m['seq'],'at'=>$send['at']??null,'event_id'=>$send['event_id']??null,'sequence'=>$send['sequence']??null,'ai_labels'=>$resolve($state['ai']),'researcher_labels'=>$resolve($state['researcher']),'effective_labels'=>$resolve($state['effective']),'label_source'=>$state['source']];
                $ai=array_merge($ai,$state['ai']);$researcher=array_merge($researcher,$state['researcher']);$effective=array_merge($effective,$state['effective']);$sources[]=$state['source'];$questions++;
            }
            $v['ai_question_count']=$questions;$v['ai_labels']=$resolve($ai);$v['researcher_labels']=$resolve($researcher);$v['effective_labels']=$resolve($effective);$v['label_sources']=array_values(array_unique($sources));
            $v['message_activities']=$activities;if($v['section']!=='ai')continue;$v['activity']=$questions?implode(' → ',array_column($activities,'activity')):($v['request_keys']?'AI sohbeti · Mesaj gönderme denemesi':'AI sohbeti görüntüleme');
            $v['activity_code']=$questions?'AI_MESSAGE':($v['request_keys']?'AI_SEND_ATTEMPT':'AI_VIEW');
        }unset($v);return $visits;
    }
    private static function responseName(string $status): string {return match($status){'completed'=>'AI yanıtı geldi','partial'=>'AI yanıtı eksik geldi','failed'=>'AI yanıtı alınamadı',default=>'AI işlemi durumu bildirildi'};}
    public static function activities(array $visits,array $data=[],array $definitions=[]): array {
        $rows=[];
        foreach($visits as $v){
            $sends=$v['message_sends']??[];$responses=$v['response_events']??[];
            if($v['locked']||(!$sends&&!$responses)){$rows[]=$v;continue;}
            if($v['section']!=='ai'){
                $rows[]=$v;
                // A request may start after an awaited save, after the student left AI/the app.
                foreach($sends as $send){
                    $message=current(array_filter($v['message_activities']??[],fn($m)=>$m['event_id']===$send['event_id']));
                    $point=$v;$point['activity_id']=$send['event_id'].'-question';$point['activity']=$message['activity']??'AI sohbeti · Mesaj gönderme denemesi';$point['activity_code']=$message?'AI_MESSAGE':'AI_SEND_ATTEMPT';$point['activity_type']='ai_question';$point['start_at']=$point['end_at']=$send['at'];$point['start_event_id']=$point['end_event_id']=$send['event_id'];$point['start_sequence']=$send['sequence'];$point['duration_seconds']=0.0;$point['request_id']=$message['request_id']??null;$point['message_id']=$message['message_id']??null;$point['message_sequence']=$message['message_sequence']??null;$point['ai_question_count']=$message?1:0;$point['edited_fields']=[];
                    foreach(['ai_labels','researcher_labels','effective_labels'] as $field)$point[$field]=$message[$field]??[];
                    $point['label_sources']=isset($message['label_source'])?[$message['label_source']]:[];$rows[]=$point;
                }
                foreach($responses as $response){$at=self::time($response['at']);if($at===null||$at<self::time($v['start_at'])||(self::time($v['end_at'])!==null&&$at>self::time($v['end_at'])))continue;$point=$v;$point['activity_id']=$response['event_id'].'-response';$point['activity']=self::responseName($response['status']).($v['section']==='outside'?' · Uygulama dışındayken':' · Sohbet sekmesi açık değildi');$point['activity_code']='AI_RESPONSE';$point['activity_type']='ai_response';$point['start_at']=$point['end_at']=$response['at'];$point['start_sequence']=$response['sequence'];$point['duration_seconds']=0.0;$point['quality']='point_event';$point['start_event_id']=$point['end_event_id']=$response['event_id'];$point['response_observed']=true;$point['ai_question_count']=0;$point['edited_fields']=[];$point['background_seen']=null;$point['end_reason']='response_arrived';$point['next_section']=null;$point['next_section_name']=null;$rows[]=$point;}
                continue;
            }
            $start=self::time($v['start_at']);$end=self::time($v['end_at']);$previous=$start;$valid=$start!==null;
            $boundaries=array_merge($sends,$responses);usort($boundaries,fn($a,$b)=>$a['sequence']<=>$b['sequence']);
            foreach($boundaries as $send){$at=self::time($send['at']);if($at===null||$previous===null||$at<$previous||($end!==null&&$at>$end))$valid=false;$previous=$at;}
            if(!$valid){$v['activity']='AI sohbeti · Soru süreleri ölçülemedi';$v['activity_code']='AI_UNSPLIT';$rows[]=$v;continue;}
            $points=[['at'=>$v['start_at'],'event_id'=>$v['start_event_id'],'sequence'=>$v['start_sequence'],'activity'=>'AI sohbeti görüntüleme']];
            foreach($sends as $send){$message=current(array_filter($v['message_activities']??[],fn($m)=>$m['event_id']===$send['event_id']));$points[]=array_replace($send,$message?:['activity'=>'AI sohbeti · Mesaj gönderme denemesi']);}
            $seenResponses=[];
            foreach($responses as $response){
                if(isset($seenResponses[$response['request_id'].'/'.$response['status']]))continue;$seenResponses[$response['request_id'].'/'.$response['status']]=true;
                $points[]=$response+['activity'=>self::responseName($response['status']),'kind'=>'response','boundary_order'=>0];
                $points[]=$response+['activity'=>'AI sohbeti görüntüleme','kind'=>'view','boundary_order'=>1];
            }
            usort($points,fn($a,$b)=>[$a['sequence'],$a['boundary_order']??0]<=>[$b['sequence'],$b['boundary_order']??0]);
            foreach($points as $i=>$point){
                $next=$points[$i+1]??null;$to=$next['at']??$v['end_at'];
                if(($i===0||($point['kind']??'')==='view')&&self::time($to)===self::time($point['at']))continue;
                $isResponse=($point['kind']??'')==='response';$isView=$i===0||($point['kind']??'')==='view';
                $row=$v;$row['activity_id']=$v['visit_id'].'-part-'.$i;$row['activity']=$point['activity'];$row['activity_code']=$isResponse?'AI_RESPONSE':($isView?'AI_VIEW':(!empty($point['message_id'])?'AI_MESSAGE':'AI_SEND_ATTEMPT'));$row['activity_type']=$isResponse?'ai_response':($isView?'ai_view':'ai_question');$row['boundary_order']=$point['boundary_order']??0;
                $row['start_at']=$point['at'];$row['start_event_id']=$point['event_id'];$row['start_sequence']=$point['sequence'];$row['end_at']=$to;$row['end_event_id']=$next['event_id']??$v['end_event_id'];
                $row['duration_seconds']=self::time($to)!==null?round(self::time($to)-self::time($point['at']),6):null;
                $row['quality']=$isResponse?'point_event':($row['duration_seconds']===null?'open_end':($v['missing_events']?'event_gap':'click_to_click'));$row['response_observed']=$isResponse?true:($isView?null:($next&&($next['kind']??'')==='response'));
                $row['end_reason']=$isResponse?'response_arrived':($next?(($next['kind']??'')==='response'?'response_arrived':'next_question'):$v['end_reason']);$row['next_activity']=$next['activity']??null;
                $row['next_section']=$next?null:$v['next_section'];$row['next_section_name']=$next?null:$v['next_section_name'];
                $row['request_id']=$point['request_id']??null;$row['message_id']=$point['message_id']??null;$row['message_sequence']=$point['message_sequence']??null;
                $row['edited_fields']=[];$row['ai_question_count']=empty($point['message_id'])?0:1;
                foreach(['ai_labels','researcher_labels','effective_labels'] as $key)$row[$key]=$point[$key]??[];
                $row['label_sources']=isset($point['label_source'])?[$point['label_source']]:[];
                // Visibility belongs to the whole visit; do not assign it to every question.
                $row['background_seen']=null;$rows[]=$row;
            }
        }
        // Sending is a point; response preparation is an independent lane, even across section switches.
        $responsesByRequest=[];
        foreach($visits as $v)foreach($v['response_events']??[] as $response)$responsesByRequest[$v['trace_id'].'/'.$response['request_id']][]=$response+['section'=>$v['section'],'invalid_clock'=>!empty($v['invalid_clock'])];
        $separated=[];
        foreach($rows as $row){
            if(($row['activity_type']??'')!=='ai_question'){$row['timeline_lane']='student';$separated[]=$row;continue;}
            $send=$row;$send['activity_id'].='-sent';$send['end_at']=$send['start_at'];$send['end_event_id']=$send['start_event_id'];$send['duration_seconds']=0.0;$send['quality']='point_event';$send['end_reason']='message_sent';$send['next_activity']='AI yanıtı hazırlanıyor';$send['next_section']=$send['next_section_name']=null;$send['boundary_order']=0;$send['timeline_lane']='student';$send['response_observed']=null;$separated[]=$send;
            $matches=array_values(array_filter($responsesByRequest[$row['trace_id'].'/'.($row['request_id']??'')]??[],fn($r)=>$r['sequence']>$row['start_sequence']&&self::time($r['at'])!==null&&self::time($r['at'])>=self::time($row['start_at'])));
            usort($matches,fn($a,$b)=>$a['sequence']<=>$b['sequence']);$response=$matches[0]??null;
            $wait=$row;$wait['activity_id'].='-wait';$wait['activity']='AI yanıtı hazırlanıyor';$wait['activity_code']='AI_WAIT';$wait['activity_type']='ai_wait';$wait['boundary_order']=1;$wait['timeline_lane']='ai_wait';$wait['ai_question_count']=0;
            $wait['end_at']=$response['at']??null;$wait['end_event_id']=$response['event_id']??null;$wait['duration_seconds']=$response?round(self::time($response['at'])-self::time($row['start_at']),6):null;
            $clockInvalid=!empty($row['invalid_clock'])||!empty($response['invalid_clock']);if($clockInvalid)$wait['duration_seconds']=null;$wait['quality']=$clockInvalid?'invalid_clock':($response?'client_response_wait':'open_end');$wait['response_observed']=(bool)$response;$wait['end_reason']=$clockInvalid?'invalid_clock':($response?'response_arrived':'missing_response');$wait['next_activity']=$response?self::responseName($response['status']):null;$wait['response_section']=$response['section']??null;$wait['next_section']=$wait['next_section_name']=null;$separated[]=$wait;
        }$rows=$separated;
        // Older clients persisted the actual message but no message_send event.
        // Show the server record as a separate point; never invent a client send time or wait duration.
        $known=[];foreach($visits as $v)foreach($v['message_activities']??[] as $m)if($m['event_id'])$known[$m['message_id']]=true;
        $names=array_column($definitions,'name','id');$resolve=fn($ids)=>array_values(array_unique(array_map(fn($id)=>$names[$id]??'Adı bulunamayan etiket',$ids)));
        foreach($data['messages']??[] as $m){
            if($m['role']!=='user'||isset($known[$m['id']])||self::time($m['created_at']??null)===null)continue;
            $request=current(array_filter($data['ai_requests']??[],fn($r)=>$r['id']===($m['request_id']??null)&&$r['app_id']===$m['app_id']));if(!$request)continue;
            $batches=array_values(array_filter($data['label_batches']??[],fn($b)=>!isset($b['app_id'])||$b['app_id']===$m['app_id']));
            $state=MessageLabels::state($m['id'],$data['labels']??[],$data['label_reviews']??[],$batches);
            $activity='AI sohbeti · '.($state['effective']?implode(' + ',$resolve($state['effective'])):'Mesaj gönderdi · '.($state['source']==='none'?'Etiket henüz yok':($state['source']==='researcher'?'Etiket seçilmedi':'Eşleşen etiket yok')));
            $rows[]=['app_id'=>$m['app_id'],'activity_id'=>$m['id'].'-server-message','visit_id'=>'','trace_id'=>'','visit_number'=>null,'activity'=>$activity,'activity_code'=>'AI_MESSAGE','activity_type'=>'ai_question_legacy','section'=>'ai','start_at'=>$m['created_at'],'end_at'=>null,'start_event_id'=>$m['id'],'end_event_id'=>null,'start_sequence'=>(int)$m['seq'],'message_id'=>$m['id'],'message_sequence'=>(int)$m['seq'],'duration_seconds'=>null,'quality'=>'server_message_only','timestamp_source'=>'server_message','end_reason'=>'missing_client_send','next_section'=>null,'next_section_name'=>null,'edited_fields'=>[],'ai_question_count'=>1,'ai_labels'=>$resolve($state['ai']),'researcher_labels'=>$resolve($state['researcher']),'effective_labels'=>$resolve($state['effective']),'label_sources'=>[$state['source']],'background_seen'=>null,'locked'=>false,'read_only_tab'=>false,'missing_events'=>true,'overlapping_tab'=>false];
        }
        $rows=self::presenceActivities($rows,$visits);
        usort($rows,fn($a,$b)=>[self::time($a['start_at'])??PHP_FLOAT_MAX,$a['trace_id'],$a['start_sequence'],$a['boundary_order']??0]<=>[self::time($b['start_at'])??PHP_FLOAT_MAX,$b['trace_id'],$b['start_sequence'],$b['boundary_order']??0]);
        foreach($rows as $i=>&$row){$row['sequence']=$i+1;$row['activity_id']??=$row['visit_id'];}unset($row);return $rows;
    }
    private static function presenceActivities(array $rows,array $visits): array {
        $outside=array_values(array_filter($visits,fn($v)=>$v['section']==='outside'));
        $waits=array_values(array_filter($rows,fn($v)=>($v['activity_type']??'')==='ai_wait'));
        $result=[];
        foreach($rows as $row){
            if(!empty($row['invalid_clock'])||($row['quality']??'')==='invalid_clock'){$result[]=$row;continue;}
            $isWait=($row['activity_type']??'')==='ai_wait';
            $isView=$row['section']==='ai'&&!$row['locked']&&($row['activity_code']??'')==='AI_VIEW';
            $cuts=$isWait?array_filter($outside,fn($v)=>$v['trace_id']===$row['trace_id']):($isView?array_filter($waits,fn($v)=>$v['trace_id']===$row['trace_id']&&($v['end_at']!==null||(!empty($row['pending_request'])&&$row['pending_request']===$v['request_id']))):[]);
            $pieces=[$row];$changed=false;
            foreach($cuts as $cut){
                $cs=self::time($cut['start_at']);$ce=self::time($cut['end_at'])??INF;
                if($cs===null)continue;
                $next=[];
                foreach($pieces as $piece){
                    $ps=self::time($piece['start_at']);$pe=self::time($piece['end_at'])??INF;
                    if($ps===null||$ce<=$ps||$cs>=$pe){$next[]=$piece;continue;}
                    $changed=true;
                    if($cs>$ps){$left=$piece;$left['end_at']=$cut['start_at'];$left['end_event_id']=$cut['start_event_id'];$left['end_reason']=$isWait?'app_outside':'next_question';$left['next_section']=$isWait?'outside':null;$left['next_section_name']=$isWait?'Uygulama dışında':null;$next[]=$left;}
                    if($ce<$pe){$right=$piece;$right['start_at']=$cut['end_at'];$right['start_event_id']=$cut['end_event_id'];$right['start_sequence']=$cut['end_sequence']??$cut['start_sequence'];$next[]=$right;}
                }
                $pieces=$next;
            }
            foreach($pieces as $i=>$piece){
                if($changed){
                    $piece['activity_id']=($row['activity_id']??$row['visit_id']).'-presence-'.$i;
                    $a=self::time($piece['start_at']);$b=self::time($piece['end_at']);$piece['duration_seconds']=$a!==null&&$b!==null?round($b-$a,6):null;
                    $piece['quality']=$piece['duration_seconds']===null?'open_end':'presence_interval';
                    if($isWait){
                        // Each resumed wait belongs to its current visible visit, not a new question.
                        foreach($visits as $v)if($v['trace_id']===$piece['trace_id']&&$v['section']!=='outside'&&self::time($v['start_at'])!==null&&self::time($v['start_at'])<=$a&&(self::time($v['end_at'])??INF)>$a){$piece['visit_id']=$v['visit_id'];$piece['visit_number']=$v['visit_number'];$piece['section']=$v['section'];break;}
                    }
                }
                $result[]=$piece;
            }
        }
        return $result;
    }
    public static function build(array $events,?string $submittedAt=null): array {
        $groups=[];$visits=[];$milestones=[];$cutoff=self::time($submittedAt);
        foreach($events as $event){$p=obj($event['payload']);$key=($event['session_id']??'').'/'.($event['tab_id']??'').'/'.($p['view_id']??'legacy');$groups[$key][]=$event;}
        foreach($groups as $key=>$items){
            usort($items,fn($a,$b)=>[(int)$a['client_seq'],$a['received_at'],$a['id']]<=>[(int)$b['client_seq'],$b['received_at'],$b['id']]);
            $active=null;$previous=null;$done=false;$number=0;$present=true;$clockInvalid=false;
            $start=function(array $event,string $section)use(&$active,&$number,&$clockInvalid,$key){
                $p=obj($event['payload']);$number++;
                $active=['visit_id'=>$event['id'].'-'.$section,'trace_id'=>substr(hash('sha256',$key),0,20),'visit_number'=>$number,'section'=>$section,'invalid_clock'=>$clockInvalid,'measurement_version'=>'presence-v2','presence_source'=>is_bool($p['app_active']??null)?'app_active':(is_bool($p['visible']??null)?'legacy_visibility':'unobserved'),'resume_section'=>$event['section'],'pending_request'=>$p['pending_request']??null,'start_at'=>$event['client_at']??null,'end_at'=>null,'start_event_id'=>$event['id'],'start_sequence'=>(int)$event['client_seq'],'edited_fields'=>[],'request_keys'=>[],'message_sends'=>[],'response_events'=>[],'locked'=>$section!=='outside'&&!empty($p['locked']),'read_only_tab'=>!empty($p['read_only_tab']),'background_seen'=>$section==='outside','missing_events'=>false];
            };
            $finish=function(?array $end,string $reason)use(&$active,&$visits){
                if(!$active)return;
                $endTime=$end?self::time($end['client_at']??null):null;$startTime=self::time($active['start_at']);
                $active['end_at']=$end['client_at']??null;$active['end_event_id']=$end['id']??null;$active['end_sequence']=$end?(int)$end['client_seq']:null;$active['end_reason']=$reason;
                $active['duration_seconds']=!$active['invalid_clock']&&$startTime!==null&&$endTime!==null&&$endTime>=$startTime?round($endTime-$startTime,6):null;
                $active['quality']=$active['duration_seconds']===null?($end?'invalid_clock':'open_end'):($active['missing_events']?'event_gap':'presence_interval');
                $active['next_section']=$reason==='next_section'?($end['section']??null):($reason==='app_outside'?'outside':($reason==='app_return'?($end['section']??null):null));
                $active['next_section_name']=self::TABS[$active['next_section']]??null;
                $section=$active['section'];
                $active['activity']=($section==='outside'?'Uygulama dışında':($active['locked']?($section==='ai'?'Kilitli AI bölümünü görüntüleme':'Kilitli '.(self::TABS[$section]??'bölüm').' bölümünü görüntüleme'):(self::TABS[$section]??'Bölüm').($active['edited_fields']?' güncelleme':' görüntüleme')));
                if($section==='ai'&&!$active['locked'])$active['activity']=$active['request_keys']?'AI sohbeti · Mesaj gönderme denemesi':'AI sohbeti görüntüleme';
                $active['activity_code']=$section==='outside'?'APP_OUTSIDE':($section==='ai'&&!$active['locked']?($active['request_keys']?'AI_SEND_ATTEMPT':'AI_VIEW'):strtoupper($section).($active['locked']?'_LOCKED':($active['edited_fields']?'_EDIT':'_VIEW')));
                $visits[]=$active;$active=null;
            };
            foreach($items as $event){
                $p=obj($event['payload']);$time=self::time($event['client_at']??null);
                if($done)break;
                if(!empty($p['submitted'])&&!(($event['type']==='workflow_confirmed')&&($p['kind']??'')==='submit')){$finish(null,'after_submission');break;}
                if($cutoff!==null&&$time!==null&&$time>$cutoff&&empty($p['view_id'])){$finish(null,'legacy_submission_cutoff');break;}
                if($active&&$previous&&(int)$event['client_seq']!==(int)$previous['client_seq']+1)$active['missing_events']=true;
                if($previous&&$time!==null&&self::time($previous['client_at']??null)!==null&&$time<self::time($previous['client_at'])){$clockInvalid=true;if($active)$active['invalid_clock']=true;}
                // New clients explicitly report app presence; old logs can only establish visibility.
                $observed=is_bool($p['app_active']??null)?$p['app_active']:(is_bool($p['visible']??null)?$p['visible']:null);
                if($observed!==null&&$observed!==$present){
                    $present=$observed;
                    if($active){$finish($event,$present?'app_return':'app_outside');$start($event,$present?$event['section']:'outside');}
                }
                if($event['type']==='section_open'){
                    if(!$active||$active['start_event_id']!==$event['id']){
                        if($active&&$active['section']==='outside')$active['resume_section']=$event['section'];
                        else{$finish($event,'next_section');$start($event,$present?$event['section']:'outside');}
                    }
                }elseif(in_array($event['type'],['section_leave','page_exit'],true))$finish($event,$p['reason']??'page_exit');
                elseif($event['type']==='workflow_confirmed'){
                    $kind=$p['kind']??'';
                    if(in_array($kind,['initial','submit'],true)){
                        if($kind==='submit'||!$active||$active['section']!=='outside')$finish($event,$kind.'_confirmed');
                        $milestones[]=['id'=>$event['id'],'kind'=>$kind,'at'=>$event['client_at']??null,'source'=>'client_confirmation','trace_id'=>substr(hash('sha256',$key),0,20),'sequence'=>(int)$event['client_seq']];
                        if($kind==='submit')$done=true;
                    }
                }elseif($active){
                    if($event['type']==='response_visible'&&is_string($p['request']??null))$active['response_events'][]=['request_id'=>$p['request'],'status'=>$p['status']??'','at'=>$event['client_at']??null,'event_id'=>$event['id'],'sequence'=>(int)$event['client_seq']];
                    if($event['type']==='message_send'&&is_string($p['request_key']??null)&&!in_array($p['request_key'],$active['request_keys'],true)){$active['request_keys'][]=$p['request_key'];$active['message_sends'][]=['request_key'=>$p['request_key'],'at'=>$event['client_at']??null,'event_id'=>$event['id'],'sequence'=>(int)$event['client_seq']];}
                    if(in_array($event['type'],['text_changed','profile_changed','question_changed'],true)&&$event['section']===$active['section']&&isset($p['field'])&&!in_array($p['field'],$active['edited_fields'],true))$active['edited_fields'][]=$p['field'];
                }
                $previous=$event;
            }
            $finish(null,'unobserved_exit');
        }
        usort($visits,fn($a,$b)=>[(self::time($a['start_at'])??PHP_FLOAT_MAX),$a['trace_id'],$a['start_sequence'],$a['visit_id']]<=>[(self::time($b['start_at'])??PHP_FLOAT_MAX),$b['trace_id'],$b['start_sequence'],$b['visit_id']]);
        // Home/reopen boundaries establish a case absence, not use of another application.
        $gaps=[];
        foreach($visits as $i=>$from){
            if($from['end_reason']!=='home'||$from['end_at']===null||$from['read_only_tab']||$from['invalid_clock'])continue;
            $end=self::time($from['end_at']);
            foreach(array_slice($visits,$i+1) as $to){
                $start=self::time($to['start_at']);if($start===null||$start<$end)continue;
                if($to['trace_id']===$from['trace_id']||$to['read_only_tab']||$to['invalid_clock'])break;
                $overlap=false;foreach($visits as $other){if($other['visit_id']===$from['visit_id']||$other['visit_id']===$to['visit_id'])continue;$a=self::time($other['start_at']);$b=self::time($other['end_at']);if($a!==null&&$b!==null&&$a<$start&&$b>$end){$overlap=true;break;}}
                if($overlap||$start===$end)break;
                $gap=$from;$gap['visit_id']=$from['visit_id'].'-away-'.$to['visit_id'];$gap['trace_id']=substr(hash('sha256',$gap['visit_id']),0,20);$gap['visit_number']=null;$gap['section']='away';$gap['activity']='Vaka dışında';$gap['activity_code']='CASE_AWAY';$gap['activity_type']='case_away';$gap['start_at']=$from['end_at'];$gap['end_at']=$to['start_at'];$gap['start_event_id']=$from['end_event_id'];$gap['end_event_id']=$to['start_event_id'];$gap['start_sequence']=$from['end_sequence']??0;$gap['end_sequence']=$to['start_sequence'];$gap['duration_seconds']=round($start-$end,6);$gap['quality']='observed_case_gap';$gap['presence_source']='home_and_reopen';$gap['end_reason']='case_return';$gap['next_section']=$to['section'];$gap['next_section_name']=self::TABS[$to['section']]??null;$gap['resume_section']=$to['resume_section'];$gap['locked']=false;$gap['background_seen']=false;$gap['pending_request']=null;$gap['edited_fields']=[];$gap['request_keys']=[];$gap['message_sends']=[];$gap['response_events']=[];$gap['missing_events']=false;$gaps[]=$gap;break;
            }
        }
        $visits=array_merge($visits,$gaps);
        usort($visits,fn($a,$b)=>[self::time($a['start_at'])??PHP_FLOAT_MAX,$a['trace_id'],$a['start_sequence']]<=>[self::time($b['start_at'])??PHP_FLOAT_MAX,$b['trace_id'],$b['start_sequence']]);
        foreach($visits as $i=>&$visit){$visit['sequence']=$i+1;$visit['overlapping_tab']=false;foreach(array_slice($visits,0,$i) as $earlier){if($earlier['trace_id']!==$visit['trace_id']&&self::time($earlier['end_at'])!==null&&self::time($visit['start_at'])!==null&&self::time($earlier['end_at'])>self::time($visit['start_at'])){$visit['overlapping_tab']=true;break;}}}unset($visit);
        return ['visits'=>$visits,'milestones'=>$milestones,'definition'=>'Bölüm süresi uygulamadan ayrılınca kapanır; dönüşte yeniden başlar. Aradaki süre Uygulama dışında olarak ayrı kaydedilir. Son teslim sonrası izlenmez; eksik kapanış tahmin edilmez.'];
    }
}
