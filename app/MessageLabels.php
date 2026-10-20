<?php
declare(strict_types=1);
final class MessageLabels {
    public function __construct(private App $app) {}
    public static function validate(array $output,array $messages,array $definitions): array {
        $map=$output['labels']??null;$mids=array_column($messages,'id');$ids=array_column($definitions,'id');
        demand(is_array($map)&&count($map)===count($mids)&&!array_diff(array_keys($map),$mids),'Mesaj etiketleme yanıtı eksik veya geçersiz',502);
        foreach($map as $mid=>$labels){demand(is_array($labels)&&array_is_list($labels),'Etiket listesi geçersiz',502);foreach($labels as $label)demand(is_string($label),'Etiket kimliği geçersiz',502);demand(count($labels)===count(array_unique($labels))&&!array_diff($labels,$ids),'Bilinmeyen veya tekrarlı mesaj etiketi',502);}
        return $map;
    }
    public function persist(array $u,string $appId,array $map,string $origin,string $hash=''): array {
        $db=$this->app->db;$batch=uid();$at=now();
        foreach($map as $mid=>$ids)foreach($ids as $id)$db->insert('message_labels',['id'=>uid(),'message_id'=>$mid,'definition_id'=>$id,'source'=>'ai','review_id'=>$batch,'actor'=>$u['id'],'reason'=>'AI etiket önerisi; araştırmacı seçimi değildir','created_at'=>$at]);
        $detail=['batch_id'=>$batch,'labels'=>$map,'hash'=>$hash,'origin'=>$origin,'model'=>$this->app->config['ai']['model'],'directive'=>'message-labels-v1'];
        $db->audit($u['id'],'message_labels_ai',$appId,'',$detail);return $detail;
    }
    public function generate(array $u,string $id): array {
        Auth::researcher($u);$db=$this->app->db;Scope::application($db,$u,$id);
        $messages=$db->all('SELECT id,seq,role,content,status FROM messages WHERE app_id=? ORDER BY seq',[$id]);demand(count($messages)>0,'Etiketlenecek mesaj yok');demand(count($messages)<=500,'En çok 500 mesaj etiketlenebilir');
        $defs=$db->all("SELECT d.id,d.name,d.body FROM definitions d WHERE d.owner_id=? AND d.family='label' AND d.archived=0 AND d.deleted_at IS NULL AND NOT EXISTS(SELECT 1 FROM definitions n WHERE n.parent_id=d.id) ORDER BY d.id",[$u['id']]);demand(count($defs)>0,'Önce Rubrikler ve etiketler bölümünden etiket ekleyin');
        $hash=hash('sha256',js([$messages,$defs]));$last=$db->one("SELECT detail FROM audit WHERE target=? AND action='message_labels_ai' ORDER BY created_at DESC,id DESC LIMIT 1",[$id]);if($last&&obj($last['detail'])['hash']===$hash)return obj($last['detail']);
        $output=(new Ai($this->app->config))->call(['task'=>'message_labels','messages'=>$messages,'label_definitions'=>$defs],true);$map=self::validate($output,$messages,$defs);
        return $db->tx(function()use($db,$u,$id,$messages,$defs,$hash,$map){Scope::application($db,$u,$id);$db->lock($id);
            demand($db->all('SELECT id,seq,role,content,status FROM messages WHERE app_id=? ORDER BY seq',[$id])===$messages,'Konuşma değişti; etiketlemeyi yeniden başlatın',409);
            foreach($defs as $def)demand((bool)$db->one("SELECT id FROM definitions WHERE id=? AND owner_id=? AND archived=0 AND deleted_at IS NULL AND NOT EXISTS(SELECT 1 FROM definitions n WHERE n.parent_id=definitions.id)",[$def['id'],$u['id']]),'Etiket tanımı değişti; yeniden başlatın',409);
            return $this->persist($u,$id,$map,'conversation',$hash);
        });
    }
    public static function state(string $mid,array $labels,array $reviews,array $batches=[]): array {
        $sort=fn($a,$b)=>[$a['created_at'],$a['id']]<=>[$b['created_at'],$b['id']];
        $history=array_values(array_filter($reviews,fn($r)=>$r['message_id']===$mid));foreach($labels as $row)if($row['message_id']===$mid&&$row['source']==='researcher'&&!in_array($row['review_id'],array_column($history,'id'),true))$history[]=['id'=>$row['review_id'],'created_at'=>$row['created_at']];usort($history,$sort);$latest=$history?end($history):null;
        $aiRows=array_values(array_filter($labels,fn($l)=>$l['message_id']===$mid&&$l['source']==='ai'));usort($aiRows,$sort);$last=$aiRows?end($aiRows):null;$ai=$last?array_values(array_column(array_filter($aiRows,fn($l)=>$l['review_id']===$last['review_id']),'definition_id')):[];
        $aiKnown=(bool)$last;usort($batches,$sort);foreach($batches as $batch){$detail=is_array($batch['detail'])?$batch['detail']:obj($batch['detail']);if(isset($detail['labels'][$mid])&&(!$last||$batch['created_at']>=$last['created_at'])){$ai=$detail['labels'][$mid];$aiKnown=true;}}
        $chosen=$latest?array_values(array_column(array_filter($labels,fn($l)=>$l['source']==='researcher'&&$l['message_id']===$mid&&$l['review_id']===$latest['id']),'definition_id')):[];
        return ['ai'=>$ai,'researcher'=>$chosen,'effective'=>$latest?$chosen:$ai,'source'=>$latest?'researcher':($aiKnown?'ai':'none'),'reviewed'=>(bool)$latest,'ai_known'=>$aiKnown];
    }
}
