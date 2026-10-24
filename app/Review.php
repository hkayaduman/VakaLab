<?php
declare(strict_types=1);
// Five-section review contract. Legacy analyses retain their original contract.
final class Review {
    public const ORDER=[1,2,3,5,4,6,8];
    public const HUMAN=[2,3,4,6];
    public const QUESTIONS=[3=>['problem'=>'Bu vakadaki temel problemi nasıl değerlendiriyorsunuz?','decision'=>'Kararınız nedir?','reason'=>'Bu kararı neden veriyorsunuz?'],4=>['final_decision'=>'Nihai kararınız nedir?','final_reason'=>'Nihai kararınızın gerekçesi nedir?'],6=>['reflection'=>'AI ile etkileşiminiz nihai kararınızı nasıl etkiledi? Hangi öneriyi kabul ettiniz, değiştirdiniz veya reddettiniz? Neden?']];
    public function __construct(private App $app) {}
    public static function version(Db $db,string $id): int {
        $audit=$db->one("SELECT detail FROM audit WHERE action='analysis_run' AND target=? ORDER BY created_at,id LIMIT 1",[$id]);
        return $audit?(int)(obj($audit['detail'])['review_version']??1):1;
    }
    public function current(string $rid,int $step): ?array {return (new Analysis($this->app))->currentStep($rid,$step);}
    public static function text(mixed $text): string {
        demand(is_string($text)&&mb_strlen(trim($text))>=10&&mb_strlen($text)<=2500,'Yorumu 10–2500 karakter arasında yazın');
        demand(!is_array(json_decode($text,true)),'Yorum düz metin olmalı; JSON nesnesi veya dizisi olamaz');
        demand(!preg_match('/^\s*(?:["\x27][^"\x27\r\n]+["\x27]\s*:|```)/u',$text),'Yorum düz metin olmalı; kod bloğu veya kodlanmış alan içeremez');
        demand(!preg_match('/\b(LLM|GPT|JSON|mock|prompt)\b/iu',$text),'Yorumda teknik üretim bilgisi bulunamaz');
        demand(!preg_match('/\b(final_collected|final_confidence|final_difficulty|final_severity)\b/iu',$text),'Yorumda teknik alan adı bulunamaz');
        return trim($text);
    }
    public static function bounds(array $rubric): array {
        $min=$max=$weight=0.0;foreach($rubric['dimensions'] as $d){$min+=(float)$d['min']*(float)$d['weight'];$max+=(float)$d['max']*(float)$d['weight'];$weight+=(float)$d['weight'];}
        return ['min'=>round($min/$weight,4),'max'=>round($max/$weight,4)];
    }
    public function profile(array $draft): array {
        $fields=[['knowledge',null,'Ön bilgi'],['confidence','final_confidence','Güven'],['difficulty','final_difficulty','Algılanan zorluk'],['severity','final_severity','Algılanan ciddiyet']];$rows=[];
        foreach($fields as [$initial,$final,$title]){$a=$draft[$initial]??null;$b=$final?($draft[$final]??null):null;$rows[]=['id'=>$initial,'title'=>$title,'initial'=>$a,'final'=>$b,'final_collected'=>$final!==null&&array_key_exists($final,$draft),'change'=>is_numeric($a)&&is_numeric($b)?$b-$a:null];}
        return $rows;
    }
    public function payload(array $run,int $step): array {
        $db=$this->app->db;$a=$db->one('SELECT * FROM applications WHERE id=?',[$run['app_id']]);$draft=obj($a['draft']);
        $base=['review_version'=>2,'step'=>$step];
        if($step===2)return $base+['task'=>'profile_comparison','profile'=>$this->profile($draft),'instruction'=>'Başlangıç ve nihai profil değerlerini karşılaştır. Yalnız ölçülmüş değişimi yorumla; güven artmış/azalmış/değişmemiş gibi açık Türkçe yorum yaz. Puan verme, başarı veya nedensellik çıkarma. Nihai değeri olmayan alanlarda değişim uydurma.','expected'=>['feedback'=>'Turkish comparison comment only; no score']];
        if(isset(self::QUESTIONS[$step])){
            $rubric=$db->one('SELECT * FROM definitions WHERE id=?',[$run['rubric_id']]);$case=$db->one('SELECT id,body,instructions FROM case_versions WHERE id=?',[$a['version_id']]);$questions=[];
            foreach(self::QUESTIONS[$step] as $id=>$title)$questions[$id]=['title'=>$title,'answer'=>$draft[$id]??''];
            return $base+['task'=>'question_group','case'=>$case,'rubric'=>obj($rubric['body']),'rubric_version'=>$run['rubric_id'],'questions'=>$questions];
        }
        return $base;
    }
    public function stats(array $run): array {
        $db=$this->app->db;$a=$db->one('SELECT * FROM applications WHERE id=?',[$run['app_id']]);$messages=$db->all('SELECT role,status FROM messages WHERE app_id=?',[$a['id']]);
        $navigation=Navigation::build($db->all('SELECT * FROM events WHERE app_id=?',[$a['id']]),$a['submitted_at']);$visits=array_values(array_filter($navigation['visits'],fn($v)=>$v['section']==='ai'&&!$v['locked']));
        $spans=[];foreach($visits as $v)if($v['duration_seconds']!==null){$start=Navigation::time($v['start_at']);$end=Navigation::time($v['end_at']);if($start!==null&&$end!==null)$spans[]=[$start,$end];}
        usort($spans,fn($a,$b)=>$a[0]<=>$b[0]);$sum=0.0;$end=null;foreach($spans as [$a,$b]){$sum+=max(0,$b-max($a,$end??$a));$end=max($b,$end??$b);}
        return ['student_messages'=>count(array_filter($messages,fn($m)=>$m['role']==='user')),'assistant_messages'=>count(array_filter($messages,fn($m)=>$m['role']==='assistant')),'seconds'=>$spans?round($sum,6):null,'unknown_intervals'=>count(array_filter($visits,fn($v)=>$v['duration_seconds']===null)),'has_visits'=>(bool)$visits];
    }
    public function scoreQuestions(array $payload): array {
        $questions=[];$bounds=self::bounds($payload['rubric']);
        foreach($payload['questions'] as $id=>$q){
            $request=['task'=>'question_score','review_version'=>2,'step'=>$payload['step'],'case'=>$payload['case'],'rubric'=>$payload['rubric'],'rubric_version'=>$payload['rubric_version'],'question'=>$q['title'],'sources'=>[$id=>$q['answer']],'instruction'=>'Yalnız bu sorunun yanıtını rubriğe göre değerlendir. Kararın gerekçelendirilmesini sorunun kapsamına göre incele. Her ölçüt için score/level/source/quote/reason döndür. Ayrıca feedback alanında bu puanın nedenini ve öğrencinin yanıtını nasıl geliştirebileceğini açık Türkçe yaz. Başka sorunun yanıtını bu sorunun yerine kullanma.','expected'=>['scores'=>['dimension_id'=>['score'=>'number in rubric range','level'=>'rubric level','source'=>$id,'quote'=>'exact excerpt','reason'=>'scoring explanation']],'feedback'=>'Turkish explanation of score and concrete improvement guidance']];
            try{$out=(new Ai($this->app->config))->call($request,true);$score=Rules::scores($out,$payload['rubric'],$request['sources']);$feedback=self::text($out['feedback']??null);}catch(Fault $e){throw new Fault('Soru '.$id.': '.$e->getMessage(),$e->getCode());}
            $questions[$id]=['title'=>$q['title'],'score'=>$score,'min'=>$bounds['min'],'max'=>$bounds['max'],'feedback'=>$feedback,'criteria'=>$out['scores'],'input'=>$request];
        }
        return ['review_version'=>2,'questions'=>$questions];
    }
    public function step(array $u,array $run,int $step,bool $repeat): array {
        $db=$this->app->db;$rid=$run['id'];demand(in_array($step,self::ORDER,true),'Geçersiz değerlendirme bölümü');
        $s=$db->tx(function()use($db,$u,$run,$rid,$step,$repeat){
            $db->one('SELECT id FROM analysis_runs WHERE id=?'.($db->sqlite()?'':' FOR UPDATE'),[$rid]);
            demand(!$db->one("SELECT id FROM analysis_steps WHERE run_id=? AND status='running'",[$rid]),'Bir değerlendirme zaten hazırlanıyor',409);
            $previous=$this->current($rid,$step);
            if($previous){if($step===8){$body=$this->assemble($run);demand(array_column(obj($previous['output'])['parts'],'decision_id')!==array_column($body['parts'],'decision_id'),'Kararlar zaten birleştirildi',409);}else demand($repeat&&in_array($step,self::HUMAN,true),'Bu bölüm için yeniden hazırla düğmesini kullanın',409);}
            if($repeat)demand(in_array($step,self::HUMAN,true),'Bu bölüm yeniden yorumlanmaz');
            $index=array_search($step,self::ORDER,true);if($index>0)demand((bool)$this->current($rid,self::ORDER[$index-1]),'Önce önceki bölümün hazırlanması gerekiyor',409);
            if($step===8)$this->assemble($run);
            $attempt=(int)($db->one('SELECT MAX(attempt) n FROM analysis_steps WHERE run_id=? AND step=?',[$rid,$step])['n']??0)+1;
            $s=['id'=>uid(),'run_id'=>$rid,'step'=>$step,'attempt'=>$attempt,'status'=>'running','input'=>js($this->payload($run,$step)),'output'=>'{}','model'=>in_array($step,[1,5,8],true)?'system':$this->app->config['ai']['model'],'directive'=>Ai::REVIEW_DIRECTIVE,'created_at'=>now()];
            $db->insert('analysis_steps',$s);$db->audit($u['id'],'analysis_step',$s['id'],'',['run'=>$rid,'step'=>$step,'review_version'=>2]);return $s;
        });
        ApiRuntime::trackStep($db,$s['id']);
        try{
            $payload=obj($s['input']);
            if($step===1){$a=$db->one('SELECT * FROM applications WHERE id=?',[$run['app_id']]);$d=obj($a['draft']);Rules::initial($d);foreach(['final_decision','final_reason','reflection'] as $key)demand(trim($d[$key]??'')!=='','Teslim edilmiş yanıt eksik');$out=['review_version'=>2,'integrity'=>'submitted'];}
            elseif($step===2){$result=(new Ai($this->app->config))->call($payload,true);demand(!isset($result['score'])&&!isset($result['scores']),'Profil yorumu puan içeremez');$out=['review_version'=>2,'profile'=>$payload['profile'],'feedback'=>self::text($result['feedback']??null)];}
            elseif($step===5)$out=['review_version'=>2,'statistics'=>$this->stats($run)];
            elseif($step===8)$out=$this->assemble($run);
            else $out=$this->scoreQuestions($payload);
            $db->tx(function()use($db,$s,$out,$run,$repeat){$fresh=$db->one('SELECT status FROM analysis_steps WHERE id=?'.($db->sqlite()?'':' FOR UPDATE'),[$s['id']]);demand($fresh['status']==='running','Bu sonuç artık geçerli değil',409);$db->q("UPDATE analysis_steps SET status='completed',output=? WHERE id=?",[js($out),$s['id']]);if($repeat)$db->q('UPDATE reports SET approved_at=NULL,published_at=NULL WHERE app_id=?',[$run['app_id']]);});
        }catch(Throwable $e){$db->q("UPDATE analysis_steps SET status='failed',error='invalid_or_failed_output' WHERE id=? AND status='running'",[$s['id']]);$db->audit($u['id'],'analysis_step_failed',$s['id'],'',['run'=>$rid,'step'=>$step,'validation_error'=>$e instanceof Fault?mb_substr($e->getMessage(),0,400):get_class($e)]);}
        if($step===8&&$db->one("SELECT id FROM analysis_steps WHERE id=? AND status='completed'",[$s['id']])){
            $report=$db->one('SELECT body FROM reports WHERE app_id=? ORDER BY version DESC LIMIT 1',[$run['app_id']]);
            if($report&&(obj($report['body'])['review_version']??1)===2)(new Analysis($this->app))->report($u,'create',['run_id'=>$run['id']]);
        }
        return $db->one('SELECT * FROM analysis_steps WHERE id=?',[$s['id']]);
    }
    public function decide(array $u,array $s,array $p): array {
        $db=$this->app->db;$step=(int)$s['step'];demand(in_array($step,self::HUMAN,true)&&$s['status']==='completed','Bu bölüm yorum veya puan onayı gerektirmiyor');
        $action=$p['action']??'';demand(in_array($action,['accept','edit','reject'],true),'Karar geçersiz');
        $original=obj($s['output']);$body=in_array($action,['accept','edit'],true)?$original:[];
        if($action==='edit'){
            if($step===2)$body['feedback']=self::text($p['body']['feedback']??null);
            else foreach($original['questions'] as $id=>$q){
                $edit=$p['body']['questions'][$id]??[];
                $criteria=$edit['criteria']??[];
                $score=Rules::scores(['scores'=>$criteria],$q['input']['rubric'],$q['input']['sources']);
                $body['questions'][$id]['criteria']=$criteria;
                $body['questions'][$id]['score']=$score;
                $body['questions'][$id]['feedback']=self::text($edit['feedback']??null);
            }
        }
        if($action==='accept'&&$step!==2)foreach($original['questions'] as $id=>$q){
            $body['questions'][$id]['score']=Rules::scores(['scores'=>$q['criteria']],$q['input']['rubric'],$q['input']['sources']);
            $body['questions'][$id]['feedback']=self::text($q['feedback']);
        }
        $decision=['id'=>uid(),'step_id'=>$s['id'],'actor'=>$u['id'],'action'=>$action,'body'=>js($body),'reason'=>trim($p['reason']??'')?:'Araştırmacı bölüm kararını kaydetti.','created_at'=>now()];
        $db->tx(function()use($db,$s,$decision,$u,$step){$db->one('SELECT id FROM analysis_runs WHERE id=?'.($db->sqlite()?'':' FOR UPDATE'),[$s['run_id']]);demand(!$db->one("SELECT id FROM analysis_steps WHERE run_id=? AND status='running'",[$s['run_id']]),'Değerlendirme hazırlanıyor',409);demand($this->current($s['run_id'],$step)['id']===$s['id'],'Yeni sonucu inceleyin',409);$db->insert('decisions',$decision);$run=$db->one('SELECT app_id FROM analysis_runs WHERE id=?',[$s['run_id']]);$db->q('UPDATE reports SET approved_at=NULL,published_at=NULL WHERE app_id=?',[$run['app_id']]);$db->audit($u['id'],'decision',$decision['id'],$decision['reason']);});return $decision;
    }
    public function assemble(array $run): array {
        $db=$this->app->db;$parts=[];foreach(self::HUMAN as $step){$s=$this->current($run['id'],$step);demand((bool)$s,'Değerlendirme eksik');$d=$db->one('SELECT * FROM decisions WHERE step_id=? ORDER BY created_at DESC,id DESC LIMIT 1',[$s['id']]);demand((bool)$d,'Profil, başlangıç, nihai ve yansıtma kararlarını tamamlayın');$parts[]=['step'=>$step,'decision_id'=>$d['id'],'action'=>$d['action'],'approved'=>obj($d['body'])];}
        $a=$db->one('SELECT * FROM applications WHERE id=?',[$run['app_id']]);$user=$db->one('SELECT login FROM users WHERE id=?',[$a['user_id']]);$case=$db->one('SELECT title FROM cases WHERE id=?',[$a['case_id']]);$rubric=$db->one('SELECT name,version,body FROM definitions WHERE id=?',[$run['rubric_id']]);
        return ['review_version'=>2,'run'=>$run['id'],'participant'=>$user['login'],'case_title'=>$case['title'],'rubric_name'=>$rubric['name'],'rubric_version'=>$run['rubric_id'],'rubric_number'=>$rubric['version'],'dimensions'=>obj($rubric['body'])['dimensions'],'parts'=>$parts,'profile'=>$this->profile(obj($a['draft'])),'chat_statistics'=>$this->stats($run)];
    }
}
