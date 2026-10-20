<?php
declare(strict_types=1);
final class StudentReport {
    public static function build(array $body,array $draft): array {
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
    public static function feedback(array $report,array $feedback): array {
        $text=function($s){demand(is_string($s)&&mb_strlen(trim($s))>=10&&mb_strlen($s)<=2500,'Öğrenci geri bildirimi eksik veya çok uzun',502);demand(!preg_match('/\b(AI|LLM|GPT|JSON|mock|prompt)\b|yapay zek[aâ]/iu',$s),'Öğrenci geri bildiriminde teknik üretim bilgisi var',502);return trim($s);};
        foreach(['initial','final'] as $key){
            $map=$feedback[$key]??null;demand(is_array($map),'Ölçüt geri bildirimleri eksik',502);
            $ids=array_column($report['sections'][$key]['criteria'],'id');demand(count($map)===count($ids)&&!array_diff(array_keys($map),$ids),'Geri bildirim ölçütleri uyuşmuyor',502);
            foreach($report['sections'][$key]['criteria'] as &$criterion)$criterion['feedback']=$text($map[$criterion['id']]??null);unset($criterion);
        }
        $report['overall_feedback']=$text($feedback['overall_feedback']??null);return $report;
    }
    public static function projection(array $body,array $draft): array {
        // Existing report bodies remain immutable; old versions explicitly lack new feedback.
        return $body['student_report']??self::build($body,$draft);
    }
    public static function blocks(array $r): array {
        $d=$r['report'];$b=[];$p=function($s)use(&$b){$b[]=['type'=>'paragraph','text'=>$s];};$h=function($s)use(&$b){$b[]=['type'=>'heading','text'=>$s];};
        $score=fn($s,$m)=>$s===null?'Puanlanmadı':$s.' / '.$m;
        $p($d['case_title'].' · '.$d['participant'].' · Rapor sürümü '.$r['version']);
        $p('Rubrik: '.$d['rubric_name']);$p(!empty($r['approved_at'])?'Araştırmacı onayıyla sunulmuştur.':'İnceleme taslağı · Henüz onaylanmadı.');
        $h('Toplam puan: '.$score($d['total'],$d['max']));$p('Toplam puan nihai değerlendirmeye aittir. Başlangıç puanı karşılaştırma amacıyla ayrıca gösterilir.');$p($d['overall_feedback']);
        foreach($d['sections'] as $key=>$section){$b[]=['type'=>$key==='final'?'section':'heading','text'=>$section['title']];foreach($section['answers'] as $label=>$answer){$b[]=['type'=>'label','text'=>$label];$p($answer?:'Yanıt bulunamadı.');}
            $h('Aldığım puan ve geri bildirim');
            foreach($section['criteria'] as $c){$b[]=['type'=>'label','text'=>$c['name'].' — '.$score($c['score'],$c['max']).' · '.$c['level'].($c['weight']!=1?' · Ağırlık: '.$c['weight']:'')];$p($c['feedback']);}
            $p('Bölüm toplamı: '.$score($section['total'],$section['max']).' (ölçüt puanları × ağırlıkları)');
        }
        if(isset($d['unicode_long_text']))$p($d['unicode_long_text']);return $b;
    }
}
