<?php
declare(strict_types=1);
final class Rules {
    public static function draft(array $d): array {
        $out=[];
        foreach(['knowledge','confidence','difficulty','severity','final_confidence','final_difficulty','final_severity'] as $k){$v=$d[$k]??null;demand($v===null||(is_int($v)&&$v>=0&&$v<=10),'Puan 0–10 tam sayı olmalı');$out[$k]=$v;}
        foreach(['problem','decision','reason','final_decision','final_reason','reflection','question'] as $k){$v=$d[$k]??'';demand(is_string($v)&&strlen($v)<=60000,'Metin sınırı aşıldı');$out[$k]=$v;}
        return $out;
    }
    public static function initial(array $d): void {
        foreach(['knowledge','confidence','difficulty','severity'] as $k)demand($d[$k]!==null,'Dört profil alanını doldurun');
        foreach(['problem','decision','reason'] as $k)demand(trim($d[$k])!=='','Başlangıç alanlarını doldurun');
    }
    public static function final(array $d): void {
        foreach(['final_decision','final_reason','reflection'] as $k)demand(trim($d[$k])!=='','Nihai yanıt ve yansıtmayı doldurun');
        demand($d['final_confidence']!==null,'Nihai güveni seçin');
        demand(($d['final_difficulty']??null)!==null,'Nihai algılanan zorluğu seçin');
        demand(($d['final_severity']??null)!==null,'Nihai algılanan ciddiyeti seçin');
    }
    public static function submitted(array $d): array {
        // Validate historical submissions without inventing later-added measurements.
        // New submissions must use final(), including all current measurements.
        $validated=self::draft($d);self::initial($validated);
        foreach(['final_decision','final_reason','reflection'] as $key)demand(trim($validated[$key])!=='','Teslim edilmiş yanıt eksik');
        demand($validated['final_confidence']!==null,'Teslim edilmiş nihai güven eksik');
        return array_values(array_filter(['final_severity','final_difficulty'],fn($key)=>$validated[$key]===null));
    }
    public static function rubric(array $r): void {
        demand(isset($r['text'],$r['dimensions'])&&is_string($r['text'])&&trim($r['text'])!==''&&is_array($r['dimensions'])&&count($r['dimensions'])>0,'Rubrik metni ve boyutlar gerekli');$ids=[];
        foreach($r['dimensions'] as $d){
            demand(is_array($d)&&isset($d['id'],$d['name'],$d['min'],$d['max'],$d['weight'],$d['levels'])&&is_string($d['id'])&&trim($d['id'])!==''&&is_string($d['name'])&&trim($d['name'])!==''&&is_array($d['levels'])&&$d['levels']!==[],'Boyut adı ve düzeyler gerekli');
            demand(!in_array($d['id'],$ids,true),'Boyut ID tekrarı');$ids[]=$d['id'];demand(is_numeric($d['min'])&&is_numeric($d['max'])&&is_numeric($d['weight'])&&is_finite((float)$d['min'])&&is_finite((float)$d['max'])&&is_finite((float)$d['weight'])&&$d['max']>$d['min']&&$d['weight']>0,'Rubrik aralık/ağırlık geçersiz');
            $names=[];foreach($d['levels'] as $level){
                demand(is_array($level)&&isset($level['name'],$level['score'],$level['description'])&&is_string($level['name'])&&trim($level['name'])!==''&&is_string($level['description'])&&trim($level['description'])!=='','Düzey adı, puanı ve açıklaması gerekli');
                demand(!in_array(trim($level['name']),$names,true),'Düzey adları tekrarlanamaz');$names[]=trim($level['name']);
                demand(is_numeric($level['score'])&&is_finite((float)$level['score'])&&$level['score']>=$d['min']&&$level['score']<=$d['max'],'Düzey puanı boyut aralığı içinde olmalı');
            }
        }
    }
    public static function scores(array $out,array $rubric,array $sources): float {
        demand(isset($out['scores'])&&is_array($out['scores'])&&count($out['scores'])===count($rubric['dimensions']),'Eksik puan');$sum=0;$weight=0;
        foreach($rubric['dimensions'] as $d){
            $s=$out['scores'][$d['id']]??null;
            demand(is_array($s)&&isset($s['score'],$s['quote'],$s['source'],$s['reason'],$s['level']),'Kanıt/puan eksik');
            demand(is_numeric($s['score'])&&is_finite((float)$s['score'])&&$s['score']>=$d['min']&&$s['score']<=$d['max'],'Puan aralık dışı');
            $level=null;foreach($d['levels'] as $candidate)if($candidate['name']===$s['level']){$level=$candidate;break;}
            demand($level!==null,'Düzey geçersiz');
            demand((float)$s['score']===(float)$level['score'],'Puan seçilen rubrik düzeyiyle eşleşmeli');
            demand(is_string($s['reason'])&&trim($s['reason'])!=='','Puanlama gerekçesi gerekli');
            demand(is_string($s['source'])&&isset($sources[$s['source']])&&is_string($s['quote'])&&trim($s['quote'])!==''&&str_contains($sources[$s['source']],$s['quote']),'Kanıt kaynakta bulunamadı');
            if(isset($s['feedback']))demand(is_string($s['feedback'])&&mb_strlen(trim($s['feedback']))>=10&&mb_strlen($s['feedback'])<=2500&&!preg_match('/\b(AI|LLM|GPT|JSON|mock|prompt)\b|yapay zek[aâ]/iu',$s['feedback']),'Öğrenci geri bildirimi geçersiz');
            $sum+=$s['score']*$d['weight'];$weight+=$d['weight'];
        }
        return round($sum/$weight,4);
    }
    public static function csv(string $s): string { return preg_match('/^[\s]*[=+@\-\t\r]/u',$s)?"'".$s:$s; }
    public static function unionSeconds(array $spans): int {
        usort($spans,fn($a,$b)=>$a[0]<=>$b[0]);$total=0;$end=null;
        foreach($spans as [$a,$b]){if($b<$a)continue;$total+=max(0,$b-max($a,$end??$a));$end=max($b,$end??$b);}return $total;
    }
}
