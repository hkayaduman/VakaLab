<?php
declare(strict_types=1);

final class HolisticReview {
    public function __construct(private App $app) {}
    public static function text(mixed $text): string {
        demand(is_string($text)&&mb_strlen(trim($text))>=10&&mb_strlen($text)<=30000,'Rapor metni 10–30000 karakter olmalı');
        demand(!is_array(json_decode($text,true)),'Rapor düz metin olmalı');return trim($text);
    }
    public function payload(array $u,string $id,string $rubricId): array {
        $a=Scope::application($this->app->db,$u,$id);demand($a['state']==='submitted','Önce öğrenci teslimini tamamlamalı',409);
        $definition=Scope::owned($this->app->db,$u,'definitions',$rubricId);
        demand($definition['family']==='rubric'&&!$definition['archived']&&!$definition['deleted_at'],'Etkin bir rubrik seçin');
        $rubric=obj($definition['body']);Rules::rubric($rubric);$draft=obj($a['draft']);Rules::submitted($draft);
        $case=$this->app->db->one('SELECT body FROM case_versions WHERE id=?',[$a['version_id']]);$questions=[];$sources=[];
        foreach(Review::QUESTIONS as $step=>$items)foreach($items as $key=>$question){
            $answer=$draft[$key]??'';$sources[$key]=$answer;
            $questions[]=['source_id'=>$key,'section'=>[3=>'Başlangıç değerlendirmesi',4=>'Nihai değerlendirme',6=>'Yansıtma'][$step],'question'=>$question,'answer'=>$answer];
        }
        return ['task'=>'holistic_review','review_version'=>3,'report_layout'=>'questions-v1','case'=>$case['body'],'rubric'=>$rubric,'rubric_id'=>$rubricId,'rubric_name'=>$definition['name'],'question_answers'=>$questions,'sources'=>$sources,'profile_comparison'=>(new Review($this->app))->profile($draft)];
    }
    private function latest(string $id): ?array {
        $row=$this->app->db->one('SELECT * FROM reports WHERE app_id=? ORDER BY version DESC LIMIT 1',[$id]);
        return $row&&(obj($row['body'])['review_version']??1)===3?$row:null;
    }
    private function idle(string $id): void {
        demand(!$this->app->db->one("SELECT s.id FROM analysis_steps s JOIN analysis_runs r ON r.id=s.run_id WHERE r.app_id=? AND s.directive IN ('holistic-v1','holistic-v2') AND s.status='running'",[$id]),'Rapor hazırlanıyor; sonuç otomatik güncellenecek',409);
    }
    private function version(?array $row,array $p): void {
        demand(array_key_exists('version',$p)&&(int)$p['version']===(int)($row['version']??0),'Rapor değişti. Güncel raporu açıp tekrar deneyin',409);
    }
    private function begin(array $u,string $id,array $payload,string $mode): array {
        $db=$this->app->db;$run=['id'=>uid(),'app_id'=>$id,'rubric_id'=>$payload['rubric_id'],'created_at'=>now()];$db->insert('analysis_runs',$run);
        $db->audit($u['id'],'analysis_run',$run['id'],'',['review_version'=>3,'mode'=>$mode]);
        $step=['id'=>uid(),'run_id'=>$run['id'],'step'=>1,'attempt'=>1,'status'=>$mode==='ai'?'running':'completed','input'=>js($payload),'output'=>'{}','model'=>$mode==='ai'?$this->app->config['ai']['model']:'researcher','directive'=>'holistic-v2','created_at'=>now()];
        $db->insert('analysis_steps',$step);return $step;
    }
    public static function manualScores(array $submitted,array $rubric): array {
        $ids=array_column($rubric['dimensions'],'id');demand(count($submitted)===count($ids)&&!array_diff(array_keys($submitted),$ids),'Her rubrik ölçütü için bir düzey seçin');$scores=[];
        foreach($rubric['dimensions'] as $d){$choice=$submitted[$d['id']]??[];$level=null;foreach($d['levels'] as $l)if($l['name']===($choice['level']??null))$level=$l;
            demand($level!==null,'Her rubrik ölçütü için geçerli bir düzey seçin');$reason=$choice['reason']??'';demand(is_string($reason)&&mb_strlen($reason)<=2500,'Ölçüt açıklaması çok uzun');
            $scores[$d['id']]=['level'=>$level['name'],'score'=>(float)$level['score'],'reason'=>trim($reason)?:$level['description']];
        }return $scores;
    }
    public static function questionResults(array $submitted,array $payload,bool $manual): array {
        $ids=array_column($payload['question_answers'],'source_id');
        demand(count($submitted)===count($ids)&&!array_diff(array_keys($submitted),$ids),'Her soru için geri bildirim ve rubrik puanları gerekli');$out=[];
        foreach($payload['question_answers'] as $q){$id=$q['source_id'];$item=$submitted[$id]??null;demand(is_array($item),'Soru değerlendirmesi eksik');
            $feedback=self::text($item['feedback']??null);demand(mb_strlen($feedback)<=5000,'Soru geri bildirimi en fazla 5000 karakter olmalı');
            demand(is_array($item['scores']??null),'Soru puanları eksik');
            if($manual)$scores=self::manualScores($item['scores'],$payload['rubric']);
            else{Rules::scores($item,$payload['rubric'],[$id=>$q['answer']]);$scores=$item['scores'];$texts=[$feedback];foreach($scores as $score){demand(mb_strlen($score['reason'])<=2500,'Ölçüt gerekçesi çok uzun');$texts[]=$score['reason'];}Review::answerPresence($texts,[$id=>$q['answer']]);}
            $out[$id]=['feedback'=>$feedback,'scores'=>$scores];
        }return $out;
    }
    private function write(array $u,string $id,array $payload,array $result,array $step,string $mode,?array $existing): array {
        $db=$this->app->db;$a=$db->one('SELECT a.*,u.login,c.title FROM applications a JOIN users u ON u.id=a.user_id JOIN cases c ON c.id=a.case_id WHERE a.id=?',[$id]);
        $sections=[];$total=0.0;$max=0.0;
        foreach($payload['question_answers'] as $q){$review=$result['questions'][$q['source_id']];$criteria=[];$sum=0.0;$limit=0.0;
            foreach($payload['rubric']['dimensions'] as $d){$s=$review['scores'][$d['id']];$criteria[]=['id'=>$d['id'],'name'=>$d['name'],'score'=>$s['score'],'max'=>$d['max'],'weight'=>$d['weight'],'level'=>$s['level'],'feedback'=>$s['reason']];$sum+=(float)$s['score']*$d['weight'];$limit+=(float)$d['max']*$d['weight'];}
            $sections[$q['source_id']]=['title'=>$q['question'],'section'=>$q['section'],'answer'=>$q['answer'],'feedback'=>$review['feedback'],'answers'=>[],'criteria'=>$criteria,'total'=>round($sum,4),'max'=>round($limit,4)];$total+=$sum;$max+=$limit;
        }
        $student=['format'=>'student-v3','report_layout'=>'questions-v1','participant'=>$a['login'],'case_title'=>$a['title'],'rubric_name'=>$payload['rubric_name'],'overall_feedback'=>$result['report'],'profile'=>$payload['profile_comparison'],'questions'=>$payload['question_answers'],'total'=>round($total,4),'max'=>round($max,4),'sections'=>$sections];
        $body=['review_version'=>3,'report_layout'=>'questions-v1','mode'=>$mode,'rubric_id'=>$payload['rubric_id'],'rubric'=>$payload['rubric'],'report_text'=>$result['report'],'question_reviews'=>$result['questions'],'profile'=>$payload['profile_comparison'],'questions'=>$payload['question_answers'],'student_report'=>$student];
        $number=(int)($db->one('SELECT MAX(version) n FROM reports WHERE app_id=?',[$id])['n']??0)+1;
        $row=['id'=>$existing['id']??uid(),'app_id'=>$id,'run_id'=>$step['run_id'],'version'=>$number,'body'=>js($body),'actor'=>$u['id'],'created_at'=>now()];
        if($existing)$db->q('UPDATE reports SET run_id=?,version=?,body=?,actor=?,created_at=?,approved_at=NULL,published_at=NULL WHERE id=?',[$row['run_id'],$number,$row['body'],$u['id'],$row['created_at'],$row['id']]);else $db->insert('reports',$row);
        $db->q('UPDATE reports SET published_at=NULL WHERE app_id=? AND id<>?',[$id,$row['id']]);
        $db->q("UPDATE analysis_steps SET output=?,status='completed' WHERE id=?",[js($result),$step['id']]);
        $db->audit($u['id'],'report_create',$row['id'],'',['review_version'=>3,'mode'=>$mode,'version'=>$number]);return $db->one('SELECT * FROM reports WHERE id=?',[$row['id']]);
    }
    public function handle(array $u,array $p): array {
        $db=$this->app->db;$id=$p['app_id']??'';Scope::application($db,$u,$id);$action=$p['action']??'';
        if(in_array($action,['approve','show','hide'],true))return $db->tx(function()use($db,$u,$p,$id,$action){
            $db->lock($id);$this->idle($id);$r=$this->latest($id);demand($r&&$r['id']===($p['report_id']??null),'Güncel raporu seçin',409);$this->version($r,$p);
            if($action==='approve')$db->q('UPDATE reports SET approved_at=? WHERE id=?',[now(),$r['id']]);
            elseif($action==='show'){demand((bool)$r['approved_at'],'Önce raporu onaylayın',409);demand(Scope::collectionClosed($this->app->access($u,$id)),'Öğrenciye göstermek için bu vakada veri toplamayı kapatın',409);$db->q('UPDATE reports SET published_at=? WHERE id=?',[now(),$r['id']]);}
            else $db->q('UPDATE reports SET published_at=NULL WHERE id=?',[$r['id']]);
            $db->audit($u['id'],'report_'.$action,$r['id']);return $db->one('SELECT * FROM reports WHERE id=?',[$r['id']]);
        });
        demand(in_array($action,['generate','save'],true),'Geçersiz rapor işlemi');$payload=$this->payload($u,$id,$p['rubric_id']??'');
        if($action==='save'){
            demand(is_string($p['report']??''),'Genel değerlendirme metin olmalı');
            $result=['report'=>trim($p['report']??'')===''?'':self::text($p['report']),'questions'=>self::questionResults($p['questions']??[],$payload,true)];
            return $db->tx(function()use($db,$u,$id,$p,$payload,$result){$db->lock($id);$this->idle($id);$existing=$this->latest($id);$this->version($existing,$p);$step=$this->begin($u,$id,$payload,'manual');return $this->write($u,$id,$payload,$result,$step,'manual',$existing);});
        }
        $step=$db->tx(function()use($db,$u,$id,$p,$payload){$db->lock($id);$this->idle($id);$this->version($this->latest($id),$p);return $this->begin($u,$id,$payload,'ai');});
        ApiRuntime::trackStep($db,$step['id']);
        try{
            // Exactly one provider call: all question-answer pairs and the whole rubric.
            $result=(new Ai($this->app->config))->call($payload,true);$result=['report'=>self::text($result['report']??null),'questions'=>self::questionResults($result['questions']??[],$payload,false)];
            return $db->tx(function()use($db,$u,$id,$p,$payload,$result,$step){$db->lock($id);$fresh=$db->one('SELECT status FROM analysis_steps WHERE id=?',[$step['id']]);demand($fresh['status']==='running','Bu sonuç artık geçerli değil',409);$existing=$this->latest($id);$this->version($existing,$p);return $this->write($u,$id,$payload,$result,$step,'ai',$existing);});
        }catch(Throwable $e){$db->q("UPDATE analysis_steps SET status='failed',error='invalid_or_failed_output' WHERE id=? AND status='running'",[$step['id']]);$db->audit($u['id'],'analysis_step_failed',$step['id'],'',['validation_error'=>$e instanceof Fault?mb_substr($e->getMessage(),0,400):get_class($e)]);throw $e;}
    }
    public static function blocks(array $r): array {
        $d=$r['report'];$b=[['type'=>'paragraph','text'=>$d['case_title'].' · '.$d['participant']],['type'=>'paragraph','text'=>'Rubrik: '.$d['rubric_name']],['type'=>'paragraph','text'=>$r['approved_at']?'Araştırmacı onayıyla sunulmuştur.':'İnceleme taslağı · Henüz onaylanmadı.'],['type'=>'heading','text'=>'Değerlendirme raporu'],['type'=>'paragraph','text'=>$d['overall_feedback']]];
        foreach($d['sections'] as $section){
            if(($d['report_layout']??'')==='questions-v1'){$b[]=['type'=>'heading','text'=>$section['section'].' · '.$section['title']];$b[]=['type'=>'paragraph','text'=>'Öğrencinin yanıtı: '.$section['answer']];$b[]=['type'=>'paragraph','text'=>'Geri bildirim: '.$section['feedback']];}
            $rows=[['Ölçüt','Düzey','Puan','Ağırlık','Gerekçe']];foreach($section['criteria'] as $c)$rows[]=[$c['name'],$c['level'],$c['score'].' / '.$c['max'],(string)$c['weight'],$c['feedback']];$b[]=['type'=>'table','rows'=>$rows];
            if(($d['report_layout']??'')==='questions-v1')$b[]=['type'=>'score','text'=>'Soru puanı: '.$section['total'].' / '.$section['max']];
        }
        $b[]=['type'=>'score','text'=>'Toplam puan: '.$d['total'].' / '.$d['max']];$b[]=['type'=>'paragraph','text'=>'Her sorunun puanı rubrik ölçüt puanları ve ağırlıklarıyla hesaplanır. Toplam, soru puanlarının toplamıdır.'];
        $rows=[['Profil','Başlangıç','Nihai','Değişim']];foreach($d['profile'] as $p)$rows[]=[$p['title'],(string)($p['initial']??'Kayıt yok'),$p['final_collected']?(string)($p['final']??'Kayıt yok'):'Bu aşamada sorulmadı',$p['change']===null?'Karşılaştırılamıyor':(string)$p['change']];$b[]=['type'=>'heading','text'=>'Başlangıç–nihai profil karşılaştırması'];$b[]=['type'=>'table','rows'=>$rows];return $b;
    }
}
