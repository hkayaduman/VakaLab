<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
$sections=[];
foreach(['initial'=>['Başlangıç değerlendirmesi',[1,1,1]],'final'=>['Nihai değerlendirme',[2,2]],'reflection'=>['Yansıtma',[0]]] as $key=>[$title,$scores]){
    $criteria=[];$questions=array_values(Review::QUESTIONS[['initial'=>3,'final'=>4,'reflection'=>6][$key]]);
    foreach($scores as $i=>$score)$criteria[]=['id'=>$key.$i,'name'=>$questions[$i],'answer'=>'Grup üyelerinin katkılarını ayrı ayrı dinleyip görev dağılımını birlikte gözden geçiririm. Her öğrencinin katılımını destekleyen adil bir çözüm seçerim.','score'=>$score,'max'=>4,'weight'=>1,'level'=>'','feedback'=>'Yanıtınızda kararınızı destekleyen somut vaka bilgilerini, gerekçeleri ve olası sonuçları daha açık ilişkilendirebilirsiniz.'];
    $sections[$key]=['title'=>$title,'criteria'=>$criteria,'total'=>array_sum($scores),'max'=>count($scores)*4];
}
$d=['format'=>'student-v2','participant'=>'TEST-OGR-01','case_title'=>'Grup çalışmasında adalet','rubric_name'=>'TEST RUBRİĞİ','sections'=>$sections,'total'=>null,'max'=>null,'overall_feedback'=>'Güven başlangıçta 10, nihai aşamada 10 olarak bildirilmiş; değişmemiş. Algılanan ciddiyet başlangıçta 5, nihai aşamada 10 olarak bildirilmiş; artmış. Nihai ölçümü olmayan alanlar için değişim yorumu yapılamaz.'];
function checkLayout(bool $ok,string $label): void {demand($ok,$label);echo 'PASS '.$label.PHP_EOL;}
checkLayout(StudentReport::scoreSummary($d)===['total'=>7.0,'max'=>24.0],'section totals add up to 7 / 24');
$zero=$d;foreach($zero['sections'] as &$section)$section['total']=0;unset($section);
checkLayout(StudentReport::scoreSummary($zero)['total']===0.0,'zero is a real total');
$partial=$d;$partial['sections']['reflection']['total']=null;
checkLayout(StudentReport::scoreSummary($partial)['total']===null,'unscored section prevents misleading partial total');
$missing=$d;unset($missing['sections']['reflection']);
checkLayout(StudentReport::scoreSummary($missing)['total']===null,'missing section prevents total');
$legacy=$d;$legacy['format']='student-v1';$legacy['total']=4;$legacy['max']=8;
checkLayout(StudentReport::scoreSummary($legacy)===['total'=>4,'max'=>8],'legacy final-only total preserved');
$r=['report'=>$d,'version'=>1,'approved_at'=>null];
$blocks=StudentReport::blocks($r);checkLayout(in_array('Toplam puan: 7 / 24',array_column($blocks,'text'),true),'existing snapshot without stored aggregate gets export total');
if(!is_dir(__DIR__.'/tmp'))mkdir(__DIR__.'/tmp');
$path=__DIR__.'/tmp/formatted-student-report.docx';Export::reportFile([$r],'docx',$path);
$zip=new ZipArchive();$zip->open($path);$xml=$zip->getFromName('word/document.xml');$styles=$zip->getFromName('word/styles.xml');$zip->close();
checkLayout(str_contains($xml,'Toplam puan: 7 / 24')&&substr_count($xml,'Bu bölümden alınan toplam puan:')===3,'DOCX includes prominent aggregate and all section totals');
checkLayout(substr_count($xml,'Öğrenci yanıtı')>=6&&substr_count($xml,'Değerlendirme yorumu')===6&&str_contains($xml,'Bu sorudan alınan puan: 1 / 4 puan'),'DOCX clearly labels answers comments and scores');
checkLayout(str_contains($xml,'Grup üyelerinin katkılarını')&&str_contains($xml,'FFF8E9')&&str_contains($xml,'F3F5F7'),'DOCX includes actual answers and distinct answer/comment shading');
checkLayout(str_contains($xml,'E6F0E9')&&str_contains($styles,'Title')&&!str_contains($xml,'w:pageBreakBefore'),'DOCX has summary shading and title styles without forced section breaks');
checkLayout(!str_contains($xml,'Araştırmacı iç'),'student copy has no private appendix');
echo $path.PHP_EOL;
file_put_contents(__DIR__.'/tmp/report-layout-fixture.json',js($r));
