<?php
declare(strict_types=1);
final class Telemetry {
    public const TYPES=['section_open','field_focus','text_changed','profile_changed','question_changed','copy','paste','scroll','visibility','focus','heartbeat','offline','online','recovered','response_visible','page_exit','section_leave','workflow_confirmed','message_send','app_presence'];
    public function __construct(private App $app) {}
    public const CUTOFF_UNVERIFIED='collection_cutoff_unverified';

    public function ingest(array $u,string $id,array $p,string $session): array {
        demand($u['role']==='student','Öğrenci olayları gerekli',403);
        $items=$p['events']??[];
        demand(is_array($items)&&count($items)<=100,'Olay paketinde en fazla 100 kayıt olabilir');
        $db=$this->app->db;
        return $db->tx(function()use($db,$u,$id,$items,$session){
            $db->lock($id);
            return $this->record($this->app->access($u,$id),array_values($items),$session);
        });
    }

    // The caller holds the application lock and commits the submission snapshot in this transaction.
    public function recordSubmission(array $a,mixed $batch,string $session): array {
        demand(is_array($batch)&&is_array($batch['events']??null)&&count($batch['events'])<100,'Teslim olay paketi geçersiz');
        $marker=$batch['marker']??null;
        demand(is_array($marker)&&($marker['type']??null)==='workflow_confirmed'&&($marker['payload']['kind']??null)==='submit','Teslim olay kaydı geçersiz');
        $pending=self::pendingRanges($batch['pending']??null);
        $items=array_values($batch['events']);
        $receipt=$this->record($a,[...$items,$marker],$session,count($items),$pending);
        demand(in_array($marker['key']??null,$receipt['accepted'],true),'Teslim olay kaydı doğrulanamadı');
        return $receipt;
    }

    public static function researchEvent(array $event): bool {
        return ($event['quality']??null)!==self::CUTOFF_UNVERIFIED;
    }

    private static function pendingRanges(mixed $pending): array {
        $requested=is_array($pending['ranges']??null)?$pending['ranges']:[];
        $ranges=[];
        foreach(array_slice($requested,0,48) as $range){
            if(!is_array($range))continue;
            $session=$range['session']??null;$tab=$range['tab']??null;
            $first=$range['first']??null;$last=$range['last']??null;
            if(is_string($session)&&preg_match('/^[a-f0-9]{32}$/D',$session)&&is_string($tab)&&preg_match('/^[a-zA-Z0-9_-]{1,64}$/D',$tab)&&
                is_int($first)&&is_int($last)&&$first>0&&$last>=$first&&$last<=2147483647)
                $ranges[]=['session'=>$session,'tab'=>$tab,'first'=>$first,'last'=>$last];
        }
        $truncated=count($ranges)!==count($requested)||!empty($pending['truncated']);
        usort($ranges,fn($a,$b)=>[$a['session'],$a['tab'],$a['first']]<=>[$b['session'],$b['tab'],$b['first']]);
        $merged=[];
        foreach($ranges as $range){
            $last=count($merged)-1;
            if($last>=0&&$merged[$last]['session']===$range['session']&&$merged[$last]['tab']===$range['tab']&&$range['first']<=$merged[$last]['last']+1)
                $merged[$last]['last']=max($merged[$last]['last'],$range['last']);
            else $merged[]=$range;
        }
        return ['ranges'=>$merged,'truncated'=>$truncated];
    }

    private static function includesSequence(array $ranges,string $session,string $tab,int $seq): bool {
        foreach($ranges as $range)
            if($range['session']===$session&&$range['tab']===$tab&&$seq>=$range['first']&&$seq<=$range['last'])return true;
        return false;
    }

    private function record(array $a,array $items,string $session,?int $submissionIndex=null,array $pending=[]): array {
        $db=$this->app->db;
        $id=$a['id'];
        $submitted=$a['submitted_at']??null;
        $closed=Scope::collectionClosed($a)?$a['collection_closed_at']:null;
        $collection=new CollectionTimeline($db,$a);
        $received=Navigation::time(now());
        $priorSubmission=null;
        $priorRanges=[];
        if($submitted){
            foreach($db->all("SELECT session_id,tab_id,client_seq,payload FROM events WHERE app_id=? AND type='workflow_confirmed'",[$id]) as $prior){
                $meta=obj($prior['payload']);
                if(($meta['kind']??'')==='submit'){$priorSubmission=$prior;$priorRanges=self::pendingRanges($meta['submission_pending']??null)['ranges'];break;}
            }
        }
        $accepted=[];
        $rejected=[];
        $quarantined=[];
        $resume=null;
        $lastSection=$a['last_section'];
        $activityAt=null;
        foreach($items as $index=>$e){
            try{
                demand(is_array($e)&&in_array($e['type']??'',self::TYPES,true),'Bilinmeyen olay');
                demand(is_string($e['key']??null)&&preg_match('/^[a-zA-Z0-9_-]{16,100}$/D',$e['key'])===1,'Olay kimliği geçersiz');
                demand(in_array($e['section']??'',['case','profile','initial','ai','final','reflection','confirm'],true),'Bölüm geçersiz');
                $seq=filter_var($e['seq']??0,FILTER_VALIDATE_INT,['options'=>['min_range'=>-2147483648,'max_range'=>2147483647]]);
                demand($seq!==false&&is_string($e['tab']??'')&&is_string($e['at']??''),'Olay zamanı, sırası veya sekmesi geçersiz');
                $origin=$e['session']??null;
                demand($origin===null||(is_string($origin)&&preg_match('/^[a-f0-9]{32}$/D',$origin)===1),'Olay oturumu geçersiz');
                $payload=$e['payload']??[];
                demand(is_array($payload),'Olay içeriği geçersiz');
                foreach(['view_id','reason','field','request','request_key','status','kind','pending_request','attempt_id','pending_attempt','capture_from','capture_until'] as $field)
                    demand(!isset($payload[$field])||is_string($payload[$field]),'Olay alanı geçersiz');
                unset($payload['collection_window'],$payload['submission_pending']);
                demand(strlen(js($payload))<12000,'Olay içeriği 12.000 bayt sınırını aşıyor');
                $terminal=$e['type']==='workflow_confirmed'&&($payload['kind']??'')==='submit';
                $existing=$db->one('SELECT quality FROM events WHERE app_id=? AND client_key=?',[$id,$e['key']]);
                if($existing){
                    demand($index!==$submissionIndex,'Teslim olay kimliği daha önce kullanılmış');
                    if(!self::researchEvent($existing))$quarantined[]=$e['key'];else $accepted[]=$e['key'];
                    continue;
                }

                $tab=mb_strcut($e['tab']??'',0,64,'UTF-8');
                $originalSession=$origin??$session;
                $at=$e['at']??'';
                $time=Navigation::time($at);
                $quality=$time===null?'invalid_client_time':'client_observation_unverified';
                $beforeSubmission=false;
                if($terminal){
                    demand($index===$submissionIndex||($submitted&&!$priorSubmission),'Teslim kesinleştirmesi zaten kaydedildi veya henüz yapılmadı');
                    demand($index!==$submissionIndex||$time!==null,'Teslim olayının zamanı geçersiz');
                    if($index===$submissionIndex)$payload['submission_pending']=$pending;
                }elseif($submitted){
                    demand(empty($payload['submitted']),'Teslimden sonraki olay araştırma günlüğüne alınmadı');
                    if($priorSubmission&&$priorSubmission['session_id']===$originalSession&&$priorSubmission['tab_id']===$tab){
                        demand($seq<(int)$priorSubmission['client_seq'],'Teslimden sonraki olay araştırma günlüğüne alınmadı');
                        $beforeSubmission=true;
                    }elseif(self::includesSequence($priorRanges,$originalSession,$tab,$seq)){
                        $beforeSubmission=true;
                    }elseif($origin===null){
                        // Older clients used the current PHP session and supplied no original stream.
                        demand($time!==null&&$time<=Navigation::time($submitted),'Olayın teslimden önce gerçekleştiği doğrulanamadı');
                    }else{
                        $quality=self::CUTOFF_UNVERIFIED;
                    }
                }
                $scope=$terminal
                    ?$collection->classify($index===$submissionIndex?$received:Navigation::time($submitted),[],$received,Navigation::time($submitted)??$received)
                    :$collection->classify($time,$payload,$received,$beforeSubmission?Navigation::time($submitted):null);
                if(!$terminal&&!$scope['allowed'])$quality=self::CUTOFF_UNVERIFIED;
                if($scope['window']!==null)$payload['collection_window']=$scope['window'];
                $json=js($payload);
            }catch(Fault $error){
                $rejected[]=['index'=>$index,'reason'=>$error->getMessage()];
                continue;
            }
            $row=['id'=>uid(),'app_id'=>$id,'client_key'=>$e['key'],'tab_id'=>$tab,'session_id'=>$originalSession,'client_seq'=>$seq,'client_at'=>mb_strcut($at,0,32,'UTF-8'),'received_at'=>now(),'type'=>$e['type'],'section'=>$e['section'],'payload'=>$json,'quality'=>$quality];
            if(!$submitted&&$quality==='client_observation_unverified'){
                $activityAt=$row['received_at'];
                if($e['type']==='section_open'&&empty($payload['read_only_tab'])){
                    if($resume===null&&$lastSection!==null){
                        foreach($db->all("SELECT session_id,tab_id,client_seq,client_at,payload FROM events WHERE app_id=? AND type='section_open' AND section=? AND quality='client_observation_unverified' ORDER BY received_at",[$id,$lastSection]) as $prior){
                            if(empty(obj($prior['payload'])['read_only_tab'])&&($resume===null||self::laterSection($prior,$resume)))$resume=$prior;
                        }
                    }
                    if($resume===null||self::laterSection($row,$resume)){
                        $resume=$row;
                        $lastSection=$e['section'];
                    }
                }
            }
            $db->insert('events',$row);
            if($terminal)$priorSubmission=$row;
            if($quality===self::CUTOFF_UNVERIFIED)$quarantined[]=$e['key'];else $accepted[]=$e['key'];
        }
        if($activityAt!==null)$db->q('UPDATE applications SET last_section=?,last_activity=? WHERE id=?',[$lastSection,$activityAt,$id]);
        return ['accepted'=>$accepted,'rejected'=>$rejected,'quarantined'=>$quarantined]+($closed?['collection_closed_at'=>$closed]:[]);
    }

    public static function scopeCoverage(array $events): array {
        $accepted=[];$withheld=[];$ranges=[];$truncated=false;
        foreach($events as $e){
            $key=self::streamKey($e['session_id']??'',$e['tab_id']??'');
            $seq=(int)$e['client_seq'];
            if(self::researchEvent($e))$accepted[$key][$seq]=true;
            else $withheld[$key][]=$seq;
            if($e['type']!=='workflow_confirmed')continue;
            $pending=self::pendingRanges(obj($e['payload'])['submission_pending']??null);
            $ranges=array_merge($ranges,$pending['ranges']);
            $truncated=$truncated||$pending['truncated'];
        }
        $prefix=[];$missing=0;
        foreach($ranges as $r){
            $key=self::streamKey($r['session'],$r['tab']);
            $seqs=array_keys($accepted[$key]??[]);
            $present=count(array_filter($seqs,fn($seq)=>$seq>=$r['first']&&$seq<=$r['last']));
            $missing+=$r['last']-$r['first']+1-$present;
            if($seqs&&min($seqs)>$r['first'])$prefix[$key]=true;
        }
        foreach($withheld as $key=>$seqs)
            if(isset($accepted[$key])&&min($seqs)<min(array_keys($accepted[$key])))$prefix[$key]=true;
        return ['expected_missing_events'=>$missing,'withheld_events'=>array_sum(array_map('count',$withheld)),
            'incomplete_prefix_streams'=>count($prefix),'pending_ranges_truncated'=>$truncated,'prefix_streams'=>$prefix];
    }

    public static function streamKey(string $session,string $tab): string {
        return hash('sha256',js([$session,$tab]));
    }

    public static function traceKey(array $event,array $payload): string {
        $base=($event['session_id']??'').'/'.($event['tab_id']??'').'/'.($payload['view_id']??'legacy');
        return isset($payload['collection_window'])?$base.'/'.$payload['collection_window']:$base;
    }

    private static function laterSection(array $next,array $previous): bool {
        if($next['session_id']===$previous['session_id']&&$next['tab_id']===$previous['tab_id'])
            return $next['client_seq']>(int)$previous['client_seq'];
        return Navigation::time($next['client_at'])>=(Navigation::time($previous['client_at'])??-INF);
    }
    public static function visits(array $events): array {
        // Per tab/session; client times are used only within a contiguous sequence.
        $events=array_filter($events,[self::class,'researchEvent']);
        usort($events,fn($a,$b)=>[$a['session_id'],$a['tab_id'],(int)$a['client_seq']]<=>[$b['session_id'],$b['tab_id'],(int)$b['client_seq']]);$prev=[];$visits=[];$spans=[];
        foreach($events as $e){$meta=obj($e['payload']);$key=$e['session_id'].'/'.$e['tab_id'].'/'.($meta['collection_window']??'');$a=$prev[$key]??null;$prev[$key]=$e;if(!$a)continue;
            $start=Navigation::time($a['client_at']??null);$end=Navigation::time($e['client_at']??null);$contiguous=(int)$e['client_seq']===(int)$a['client_seq']+1;
            $known=$start!==null&&$end!==null&&$end>=$start&&(int)$end-(int)$start<=30&&$contiguous&&$a['type']!=='page_exit';
            if($known){$start=(int)$start;$end=(int)$end;}
            $meta=obj($a['payload']);$visible=$meta['visible']??null;$focused=$meta['focused']??null;
            $visits[]=['tab'=>$e['tab_id'],'section'=>$a['section'],'start'=>$a['client_at'],'end'=>$e['client_at'],'seconds'=>$known?$end-$start:null,'visible'=>$visible,'focused'=>$focused,'interactive'=>$meta['interactive']??null,'quality'=>$known?'bounded_client_observation':'unknown_gap'];
            if($known&&$visible===true&&$focused===true)$spans[]=[$start,$end];
        }
        return ['visits'=>$visits,'visible_focused_union_seconds'=>Rules::unionSeconds($spans),'quality'=>'measurement-v1; açık son aralıklar ölçülmez; AI süresi ayrı; okuma/düşünme çıkarımı yok'];
    }
}
