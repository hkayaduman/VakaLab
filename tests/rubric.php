<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
$count=0;
function verifyRubric(bool $ok,string $message):void{global $count;if(!$ok)throw new RuntimeException($message);echo 'PASS RUBRIC '.(++$count).' '.$message.PHP_EOL;}
$rubric=['text'=>'SENTETİK: Kanıtı değerlendirin.','dimensions'=>[['id'=>'evidence','name'=>'Kanıt','min'=>0,'max'=>4,'weight'=>1.5,'levels'=>[['name'=>'Başlangıç','score'=>0,'description'=>'Henüz kanıt yok.'],['name'=>'Yetkin','score'=>4,'description'=>'İlgili kanıt var.']]]]];
$before=js($rubric);Rules::rubric($rubric);verifyRubric(js($rubric)===$before,'valid rubric and fractional weight accepted without mutating input');
$invalid=[];
$bad=$rubric;$bad['text']=' ';$invalid['blank instructions']=$bad;
$bad=$rubric;$bad['dimensions']=[];$invalid['missing dimensions']=$bad;
$bad=$rubric;$bad['dimensions'][0]['name']=' ';$invalid['blank dimension name']=$bad;
$bad=$rubric;$bad['dimensions'][0]['weight']=0;$invalid['zero weight']=$bad;
$bad=$rubric;$bad['dimensions'][0]['max']=0;$invalid['inverted score range']=$bad;
$bad=$rubric;$bad['dimensions'][0]['levels'][1]['score']=5;$invalid['level outside range']=$bad;
$bad=$rubric;$bad['dimensions'][0]['levels'][1]['name']='Başlangıç';$invalid['duplicate level names']=$bad;
$bad=$rubric;$bad['dimensions'][0]['levels'][0]['description']=' ';$invalid['empty level description']=$bad;
$bad=$rubric;$bad['dimensions'][0]['levels'][0]['score']='not-a-number';$invalid['nonnumeric level score']=$bad;
$bad=$rubric;$bad['dimensions'][0]['levels']=[];$invalid['missing levels']=$bad;
$bad=$rubric;$bad['dimensions'][]=$rubric['dimensions'][0];$invalid['duplicate stable dimension identifiers']=$bad;
$bad=$rubric;$bad['dimensions'][0]['weight']=INF;$invalid['infinite weight']=$bad;
$bad=$rubric;$bad['dimensions'][0]['max']=NAN;$invalid['nonfinite maximum']=$bad;
foreach($invalid as $name=>$value){$blocked=false;try{Rules::rubric($value);}catch(Fault $e){$blocked=$e->getCode()===422;}verifyRubric($blocked,$name.' rejected at server boundary');}
$sources=['answer'=>'Somut kanıt.'];
$scores=['scores'=>['evidence'=>['level'=>'Yetkin','score'=>4,'source'=>'answer','quote'=>'Somut kanıt.','reason'=>'İlgili kanıt kullanılmış.']]];
verifyRubric(Rules::scores($scores,$rubric,$sources)===4.0,'matching level score accepted');
foreach(['mismatched score'=>['score'=>0],'empty reason'=>['reason'=>''],'whitespace reason'=>['reason'=>" \t\n"],'nontext reason'=>['reason'=>[]],'nonfinite score'=>['score'=>INF],'unknown level'=>['level'=>'Unknown']] as $name=>$change){
    $bad=$scores;$bad['scores']['evidence']=array_replace($bad['scores']['evidence'],$change);$blocked=false;
    try{Rules::scores($bad,$rubric,$sources);}catch(Fault $e){$blocked=$e->getCode()===422;}
    verifyRubric($blocked,$name.' rejected');
}
echo "TOTAL RUBRIC $count checks passed. Pure domain validation; no database/config access.\n";
