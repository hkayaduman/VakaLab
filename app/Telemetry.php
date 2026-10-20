<?php
declare(strict_types=1);
final class Telemetry {
    public const TYPES=['section_open','field_focus','text_changed','profile_changed','question_changed','copy','paste','scroll','visibility','focus','heartbeat','offline','online','recovered','response_visible','page_exit'];
    public function __construct(private App $app) {}
    public function ingest(array $u,string $id,array $p,string $session): array {
        $this->app->access($u,$id);demand($u['role']==='student','Öğrenci olayları gerekli',403);$items=$p['events']??[];demand(is_array($items)&&count($items)<=100,'Olay batch sınırı');
        return $this->app->db->tx(function()use($id,$items,$session){$this->app->db->lock($id);$accepted=[];
            foreach($items as $e){demand(in_array($e['type']??'',self::TYPES,true),'Bilinmeyen olay');demand(preg_match('/^[a-zA-Z0-9_-]{16,100}$/',$e['key']??'')===1,'Olay kimliği geçersiz');demand(in_array($e['section']??'',['case','profile','initial','ai','final','reflection','confirm'],true),'Bölüm geçersiz');
                $accepted[]=$e['key'];if($this->app->db->one('SELECT id FROM events WHERE app_id=? AND client_key=?',[$id,$e['key']]))continue;
                $payload=$e['payload']??[];demand(is_array($payload)&&strlen(js($payload))<12000,'Olay içeriği çok uzun');
                $this->app->db->insert('events',['id'=>uid(),'app_id'=>$id,'client_key'=>$e['key'],'tab_id'=>substr($e['tab']??'',0,64),'session_id'=>$session,'client_seq'=>(int)($e['seq']??0),'client_at'=>substr($e['at']??'',0,32),'received_at'=>now(),'type'=>$e['type'],'section'=>$e['section'],'payload'=>js($payload),'quality'=>'client_observation_unverified']);
                $this->app->db->q('UPDATE applications SET last_section=?,last_activity=? WHERE id=?',[$e['section'],now(),$id]);
            }return ['accepted'=>$accepted];});
    }
    public static function visits(array $events): array {
        // Per tab/session; client times are used only within a contiguous sequence.
        usort($events,fn($a,$b)=>[$a['session_id'],$a['tab_id'],(int)$a['client_seq']]<=>[$b['session_id'],$b['tab_id'],(int)$b['client_seq']]);$prev=[];$visits=[];$spans=[];
        foreach($events as $e){$key=$e['session_id'].'/'.$e['tab_id'];$a=$prev[$key]??null;$prev[$key]=$e;if(!$a)continue;
            $start=strtotime($a['client_at']??'');$end=strtotime($e['client_at']??'');$contiguous=(int)$e['client_seq']===(int)$a['client_seq']+1;$known=$start!==false&&$end!==false&&$end>=$start&&$end-$start<=30&&$contiguous&&$a['type']!=='page_exit';
            $meta=obj($a['payload']);$visible=$meta['visible']??null;$focused=$meta['focused']??null;
            $visits[]=['tab'=>$e['tab_id'],'section'=>$a['section'],'start'=>$a['client_at'],'end'=>$e['client_at'],'seconds'=>$known?$end-$start:null,'visible'=>$visible,'focused'=>$focused,'interactive'=>$meta['interactive']??null,'quality'=>$known?'bounded_client_observation':'unknown_gap'];
            if($known&&$visible===true&&$focused===true)$spans[]=[$start,$end];
        }
        return ['visits'=>$visits,'visible_focused_union_seconds'=>Rules::unionSeconds($spans),'quality'=>'measurement-v1; açık son aralıklar ölçülmez; AI süresi ayrı; okuma/düşünme çıkarımı yok'];
    }
}
