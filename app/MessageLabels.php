<?php
declare(strict_types=1);
final class MessageLabels {
    public const DIRECTIVE='message-labels-v11';
    public function __construct(private App $app) {}
    public static function definitions(array $rows): array {
        return array_map(function($row){$body=is_array($row['body'])?$row['body']:obj($row['body']);return ['id'=>$row['id'],'name'=>$row['name'],'description'=>$body['description']??'','target_role'=>$body['target_role']??null,'topic_scope'=>$body['topic_scope']??null,'body'=>$body];},$rows);
    }
    public static function candidates(array $definitions,string $role): array {
        return array_values(array_filter(self::definitions($definitions),fn($d)=>in_array($role,['user','assistant'],true)&&$d['target_role']===$role));
    }
    public static function payload(array $messages,array $definitions,string $targetRole='all'): array {
        demand(in_array($targetRole,['all','user','assistant'],true),'Etiketlenecek mesaj grubu geçersiz');
        $context=array_map(fn($m)=>array_intersect_key($m,array_flip(['id','seq','role','content','status'])),$messages);
        $targets=$targetRole==='all'?$context:array_values(array_filter($context,fn($m)=>$m['role']===$targetRole));
        $payload=['task'=>'message_labels','directive'=>self::DIRECTIVE,'target_role'=>$targetRole,'messages'=>$targets,'label_definitions'=>$targetRole==='all'?self::definitions($definitions):self::candidates($definitions,$targetRole)];
        if($targetRole==='assistant')$payload['context_messages']=$context;
        if($targetRole==='user'){$payload['decision_mode']='scoped_per_label';demand(!array_filter($payload['label_definitions'],fn($d)=>!in_array($d['topic_scope'],['pedagogical','outside','any'],true)),'Öğrenci etiketlerinin konu kapsamını seçin');foreach($payload['messages'] as &$target){$position=array_search($target['id'],array_column($context,'id'),true);$target['context_before']=array_slice($context,0,$position);}unset($target);}
        return $payload;
    }
    public static function inputHash(array $messages,array $definitions,string $model,string $targetRole='all'): string {
        $input=[self::DIRECTIVE,$model,$messages,self::definitions($definitions)];if($targetRole!=='all')$input[]=$targetRole;return hash('sha256',js($input));
    }
    public static function validate(array $output,array $messages,array $definitions): array {
        $map=$output['labels']??null;$mids=array_column($messages,'id');$ids=array_column($definitions,'id');
        demand(is_array($map)&&count($map)===count($mids)&&!array_diff(array_keys($map),$mids),'Mesaj etiketleme yanıtı eksik veya geçersiz',502);
        foreach($map as $mid=>$labels){$message=$messages[array_search($mid,$mids,true)];$allowed=array_column(self::candidates($definitions,$message['role']),'id');demand(is_array($labels)&&array_is_list($labels),'Etiket listesi geçersiz',502);foreach($labels as $label)demand(is_string($label),'Etiket kimliği geçersiz',502);demand(count($labels)===count(array_unique($labels))&&!array_diff($labels,$ids),'Bilinmeyen veya tekrarlı mesaj etiketi',502);demand(!array_diff($labels,$allowed),'Etiket mesajın grubuna uygun değil',502);}
        return $map;
    }
    public function persist(array $u,string $appId,array $map,string $origin,string $hash='',array $definitions=[],string $targetRole='all',array $messageScopes=[]): array {
        $db=$this->app->db;$batch=uid();$at=now();
        foreach($map as $mid=>$ids)foreach($ids as $id)$db->insert('message_labels',['id'=>uid(),'message_id'=>$mid,'definition_id'=>$id,'source'=>'ai','review_id'=>$batch,'actor'=>$u['id'],'reason'=>'AI etiket önerisi; araştırmacı seçimi değildir','created_at'=>$at]);
        $detail=['batch_id'=>$batch,'labels'=>$map,'hash'=>$hash,'origin'=>$origin,'model'=>$this->app->config['ai']['model'],'directive'=>self::DIRECTIVE,'target_role'=>$targetRole,'message_scope'=>$messageScopes,'label_definitions'=>self::definitions($definitions)];
        $db->audit($u['id'],'message_labels_ai',$appId,'',$detail);return $detail;
    }
    public function generate(array $u,string $id,string $targetRole='all'): array {
        Auth::researcher($u);demand(in_array($targetRole,['all','user','assistant'],true),'Etiketlenecek mesaj grubu geçersiz');$db=$this->app->db;Scope::application($db,$u,$id);
        $messages=$db->all('SELECT id,seq,role,content,status FROM messages WHERE app_id=? ORDER BY seq',[$id]);demand(count($messages)>0,'Etiketlenecek mesaj yok');demand(count($messages)<=500,'En çok 500 mesaj etiketlenebilir');
        $defs=$db->all("SELECT d.id,d.name,d.body FROM definitions d WHERE d.owner_id=? AND d.family='label' AND d.archived=0 AND d.deleted_at IS NULL AND NOT EXISTS(SELECT 1 FROM definitions n WHERE n.parent_id=d.id) ORDER BY d.id",[$u['id']]);demand(count($defs)>0,'Önce Rubrikler ve etiketler bölümünden etiket ekleyin');
        $definitions=self::definitions($defs);demand(!array_filter($definitions,fn($d)=>!in_array($d['target_role'],['user','assistant'],true)),'Önce tüm etiketler için Öğrenci mesajı veya AI yanıtı grubunu seçin');$payload=self::payload($messages,$defs,$targetRole);$targets=$payload['messages'];demand(count($targets)>0,'Bu grupta etiketlenecek mesaj yok');demand(count($payload['label_definitions'])>0,'Bu grup için önce etiket tanımlayın');$hash=self::inputHash($messages,$defs,$this->app->config['ai']['model'],$targetRole);foreach($db->all("SELECT detail FROM audit WHERE target=? AND action='message_labels_ai' ORDER BY created_at DESC,id DESC",[$id]) as $batch){$detail=obj($batch['detail']);if(($detail['target_role']??'all')!==$targetRole)continue;if(($detail['hash']??null)===$hash)return $detail;break;}
        $map=[];$messageScopes=[];$ai=new Ai($this->app->config);foreach($targetRole==='user'?array_chunk($targets,4):[$targets] as $chunk){$part=$payload;$part['messages']=$chunk;$output=$ai->call($part,true);$messageScopes+=$output['message_scope']??[];$map+=self::validate($output,$chunk,$payload['label_definitions']);}
        return $db->tx(function()use($db,$u,$id,$messages,$defs,$hash,$map,$payload,$targetRole,$messageScopes){Scope::application($db,$u,$id);$db->lock($id);
            demand($db->all('SELECT id,seq,role,content,status FROM messages WHERE app_id=? ORDER BY seq',[$id])===$messages,'Konuşma değişti; etiketlemeyi yeniden başlatın',409);
            $freshDefs=$db->all("SELECT d.id,d.name,d.body FROM definitions d WHERE d.owner_id=? AND d.family='label' AND d.archived=0 AND d.deleted_at IS NULL AND NOT EXISTS(SELECT 1 FROM definitions n WHERE n.parent_id=d.id) ORDER BY d.id",[$u['id']]);
            demand($freshDefs===$defs,'Etiket tanımı değişti; yeniden başlatın',409);
            return $this->persist($u,$id,$map,'conversation',$hash,$payload['label_definitions'],$targetRole,$messageScopes);
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
