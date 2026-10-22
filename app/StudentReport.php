<?php
declare(strict_types=1);
final class StudentReport {
    public static function build(array $body,array $draft): array {
        if(($body['review_version']??1)===2)return self::buildReview($body,$draft);
        $sections=[];
        foreach(['initial'=>3,'final'=>4] as $key=>$step){
            $part=current(array_filter($body['parts'],fn($p)=>(int)$p['step']===$step));
            $accepted=$part&&in_array($part['action'],['accept','edit'],true);$criteria=[];$total=0;$max=0;
            foreach($body['dimensions'] as $dim){
                $s=$accepted?($part['approved']['scores'][$dim['id']]??null):null;$weight=(float)$dim['weight'];
                $criteria[]=['id'=>$dim['id'],'name'=>$dim['name'],'score'=>$s['score']??null,'max'=>$dim['max'],'weight'=>$weight,'level'=>$s['level']??'Puanlanmadı','feedback'=>'Geri bildirim henüz hazırlanmadı.'];
                if($s)$total+=(float)$s['score']*$weight;$max+=(float)$dim['max']*$weight;
            }
            $answers=$key==='initial'?['Temel problem'=>$draft['problem']??'','Kararım'=>$draft['decision']??'','Gerekçem'=>$draft['reason']??'']:['Nihai kararım'=>$draft['final_decision']??'','Nihai gerekçem'=>$draft['final_reason']??''];
            $sections[$key]=['title'=>$key==='initial'?'Başlangıç değerlendirmem':'Nihai değerlendirmem','answers'=>$answers,'criteria'=>$criteria,'total'=>$accepted?round($total,4):null,'max'=>round($max,4)];
        }
        return ['format'=>'student-v1','participant'=>$body['participant'],'case_title'=>$body['case_title'],'rubric_name'=>$body['rubric_name'],'sections'=>$sections,'total'=>$sections['final']['total'],'max'=>$sections['final']['max'],'overall_feedback'=>'Geri bildirim henüz hazırlanmadı.'];
    }
    public static function buildReview(array $body,array $draft=[]): array {
        $sections=[];
        foreach(['initial'=>3,'final'=>4,'reflection'=>6] as $key=>$step){
            $part=current(array_filter($body['parts'],fn($p)=>(int)$p['step']===$step));$accepted=$part&&in_array($part['action'],['accept','edit'],true);$criteria=[];$total=0.0;$max=0.0;
            $questions=$accepted?($part['approved']['questions']??[]):[];
            foreach(Review::QUESTIONS[$step] as $id=>$title){$q=$questions[$id]??null;$score=$q['score']??null;$limit=$q['max']??null;
                $criteria[]=['id'=>$id,'name'=>$title,'answer'=>$q['input']['sources'][$id]??$draft[$id]??null,'score'=>$score,'max'=>$limit,'weight'=>1,'level'=>'','feedback'=>$q['feedback']??'Bu soruya ilişkin puan ve geri bildirim kesinleştirilmedi.'];
                if($score!==null)$total+=$score;if($limit!==null)$max+=$limit;
            }
            $sections[$key]=['title'=>['initial'=>'Başlangıç değerlendirmesi','final'=>'Nihai değerlendirme','reflection'=>'Yansıtma'][$key],'answers'=>[],'criteria'=>$criteria,'total'=>$accepted?round($total,4):null,'max'=>$accepted?round($max,4):null];
        }
        $profile=current(array_filter($body['parts'],fn($p)=>(int)$p['step']===2));$feedback=$profile&&in_array($profile['action'],['accept','edit'],true)?$profile['approved']['feedback']:'Profil değişimine ilişkin yorum kesinleştirilmedi.';
        return ['format'=>'student-v2','participant'=>$body['participant'],'case_title'=>$body['case_title'],'rubric_name'=>$body['rubric_name'],'sections'=>$sections,'total'=>null,'max'=>null,'overall_feedback'=>$feedback];
    }
    public static function feedback(array $report,array $feedback): array {
        $text=function($s){demand(is_string($s)&&mb_strlen(trim($s))>=10&&mb_strlen($s)<=2500,'Öğrenci geri bildirimi eksik veya çok uzun',502);demand(!preg_match('/\b(AI|LLM|GPT|JSON|mock|prompt)\b|yapay zek[aâ]/iu',$s),'Öğrenci geri bildiriminde teknik üretim bilgisi var',502);return trim($s);};
        foreach(array_keys($report['sections']) as $key){
            $map=$feedback[$key]??null;demand(is_array($map),'Ölçüt geri bildirimleri eksik',502);
            $ids=array_column($report['sections'][$key]['criteria'],'id');demand(count($map)===count($ids)&&!array_diff(array_keys($map),$ids),'Geri bildirim ölçütleri uyuşmuyor',502);
            foreach($report['sections'][$key]['criteria'] as &$criterion)$criterion['feedback']=($report['format']??'')==='student-v2'?Review::text($map[$criterion['id']]??null):$text($map[$criterion['id']]??null);unset($criterion);
        }
        $report['overall_feedback']=($report['format']??'')==='student-v2'?Review::text($feedback['overall_feedback']??null):$text($feedback['overall_feedback']??null);return $report;
    }
    public static function projection(array $body,array $draft): array {
        // Enrich old projections only from the original reviewed answer, never a later draft.
        $report=$body['student_report']??self::build($body,$draft);
        if(($report['format']??'')==='student-v2'){
            $sources=[];foreach($body['parts']??[] as $part)foreach($part['approved']['questions']??[] as $id=>$q)$sources[$id]=$q['input']['sources'][$id]??null;
            foreach($report['sections'] as &$section)foreach($section['criteria'] as &$criterion){
                if(!array_key_exists('answer',$criterion))$criterion['answer']=$sources[$criterion['id']]??null;
            }unset($section,$criterion);
        }
        return $report;
    }
    public static function scoreSummary(array $d): array {
        if(($d['format']??'')!=='student-v2')return ['total'=>$d['total'],'max'=>$d['max']];
        $total=0.0;$max=0.0;
        foreach(['initial','final','reflection'] as $key){
            $section=$d['sections'][$key]??[];
            if(!isset($section['total'],$section['max']))return ['total'=>null,'max'=>null];
            $total+=(float)$section['total'];$max+=(float)$section['max'];
        }
        return ['total'=>round($total,4),'max'=>round($max,4)];
    }
    public static function blocks(array $r): array {
        $d=$r['report'];$b=[];$p=function($s)use(&$b){$b[]=['type'=>'paragraph','text'=>$s];};$h=function($s)use(&$b){$b[]=['type'=>'heading','text'=>$s];};
        $score=fn($s,$m)=>$s===null?'Puanlanmadı':$s.' / '.$m;
        $p($d['case_title'].' · '.$d['participant'].(($d['format']??'')==='student-v2'?'':' · Rapor sürümü '.$r['version']));
        $p('Rubrik: '.$d['rubric_name']);$p(!empty($r['approved_at'])?'Araştırmacı onayıyla sunulmuştur.':'İnceleme taslağı · Henüz onaylanmadı.');
        $summary=self::scoreSummary($d);
        $b[]=['type'=>'score','text'=>'Toplam puan: '.$score($summary['total'],$summary['max'])];
        $p(($d['format']??'')==='student-v2'?'Toplam puan, başlangıç değerlendirmesi, nihai değerlendirme ve yansıtma puanlarının toplamıdır.':'Toplam puan nihai değerlendirmeye aittir. Başlangıç puanı karşılaştırma amacıyla ayrıca gösterilir.');
        $rows=[['Değerlendirme bölümü','Alınan puan / En yüksek puan']];
        foreach($d['sections'] as $section)$rows[]=[$section['title'],$score($section['total'],$section['max'])];
        $b[]=['type'=>'table','rows'=>$rows];
        $p('Puanlar alınan puan / en yüksek puan biçiminde gösterilir. Öğrenci yanıtı, teslim edilen metindir; değerlendirme yorumu ise bu yanıta ilişkin geri bildirimdir.');
        $h(($d['format']??'')==='student-v2'?'Profil değişimine ilişkin değerlendirme yorumu':'Genel değerlendirme yorumu');$p($d['overall_feedback']);
        foreach($d['sections'] as $key=>$section){$h($section['title']);
            $v2=($d['format']??'')==='student-v2';
            if(!$v2)foreach($section['answers']??[] as $title=>$answer){$b[]=['type'=>'label','text'=>'Öğrenci yanıtı · '.$title];$b[]=['type'=>'answer','text'=>$answer];}
            foreach($section['criteria'] as $c){
                $b[]=['type'=>'label','text'=>($v2?'Soru: ':'Değerlendirme ölçütü: ').$c['name']];
                if($v2){$b[]=['type'=>'label','text'=>'Öğrenci yanıtı'];$b[]=['type'=>'answer','text'=>$c['answer']??'Bu rapor kaydında öğrenci yanıtının metni bulunmuyor.'];}
                $b[]=['type'=>'question-score','text'=>($v2?'Bu sorudan alınan puan: ':'Bu ölçütten alınan puan: ').$score($c['score'],$c['max']).($c['score']!==null?' puan':'').(!empty($c['level'])?' · Düzey: '.$c['level']:'').($c['weight']!=1?' · Ağırlık: '.$c['weight']:'')];
                $b[]=['type'=>'label','text'=>'Değerlendirme yorumu'];$b[]=['type'=>'feedback','text'=>$c['feedback']];
            }
            $b[]=['type'=>'subtotal','text'=>'Bu bölümden alınan toplam puan: '.$score($section['total'],$section['max']).($section['total']!==null?' puan':'').($v2?' (bu bölümdeki soru puanlarının toplamı)':' (ölçüt puanları × ağırlıkları)')];
        }
        if(isset($d['unicode_long_text']))$p($d['unicode_long_text']);return $b;
    }
}
