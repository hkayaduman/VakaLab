<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
$fixtures=obj(file_get_contents(__DIR__.'/fixtures/scoring-hosting-contradictions.json'));
$count=0;
function scoringCheck(bool $ok,string $name): void {
    global $count;
    if(!$ok)throw new RuntimeException('FAIL '.$name);
    echo 'PASS '.(++$count).' '.$name.PHP_EOL;
}
function scoringDenied(callable $fn,string $name,int $code=502): void {
    try {$fn();}catch(Fault $e){scoringCheck($e->getCode()===$code,$name);return;}
    throw new RuntimeException('FAIL accepted contradiction: '.$name);
}
foreach($fixtures as $id=>$fixture){
    $request=$fixture['request'];$answer=$request['sources'][$id];
    $messages=Ai::questionMessages($request);$wire=obj($messages[1]['content']);
    scoringCheck($wire['student_answer']===['source_id'=>$id,'text'=>$answer],'exact full answer explicitly sent for '.$id);
    scoringCheck($wire['question']===$request['question']&&$wire['rubric']===$request['rubric']&&$wire['case_context']===$request['case']['body'],'current question rubric and case preserved for '.$id);
    scoringCheck(!isset($wire['expected'],$wire['instruction'],$wire['case']['instructions']),'workflow instructions and schema placeholders omitted for '.$id);
    scoringCheck($messages[0]['role']==='system'&&$messages[1]['role']==='user'&&count($messages)===2,'answer remains untrusted user data for '.$id);
    Rules::scores($fixture['output'],$request['rubric'],$request['sources']);
    if($id!=='problem')scoringDenied(fn()=>Review::questionResult($fixture['output'],$request),'frozen hosting missing-answer contradiction rejected for '.$id);
}
$fixture=$fixtures['decision'];$request=$fixture['request'];$out=$fixture['output'];
foreach($out['scores'] as &$criterion){$criterion['reason']='Yanıt vakayla bağlantılı bir karar içeriyor, ancak uygulama ayrıntıları sınırlı.';$criterion['feedback']='Kararınız mevcut; uygulama ayrıntılarını daha açık ifade edebilirsiniz.';}unset($criterion);
$out['feedback']='Kararınız mevcut; uygulama ayrıntılarını daha açık ifade edebilirsiniz.';
$scored=Review::questionResult($out,$request);
scoringCheck($scored['score']===0.0,'validation never automatically raises a score');
$request['sources']['decision']='Bugün hava güneşli.';
foreach($out['scores'] as &$criterion){$criterion['quote']='Bugün hava güneşli.';$criterion['reason']='Yanıt hava durumundan söz ediyor; vakadaki grup katılımı sorunuyla ilişkili bir karar içermiyor.';}unset($criterion);
$out['feedback']='Yanıtınız hava durumuyla ilgili. Vakadaki grup katılımı sorununa yönelik kararınızı yazın.';
scoringCheck(Review::questionResult($out,$request)['score']===0.0,'genuine unrelated answer may receive zero');
foreach(['Yanıt verilmedi.','Öğrenci yanıtı paylaşılmadığı için puanlama yapılamıyor.','Yanıtınız yok.','Yanıt sunulmadı.'] as $claim){$bad=$out;$bad['feedback']=$claim;scoringDenied(fn()=>Review::questionResult($bad,$request),'missing-answer claim rejected: '.$claim);}
$out['feedback']='Yanıtınızda çözümün uygulanma ayrıntıları yok; bunları ekleyebilirsiniz.';
scoringCheck(Review::questionResult($out,$request)['score']===0.0,'missing detail is not confused with missing answer');
$bad=$out;foreach($bad['scores'] as &$criterion)$criterion['reason']='Yanıt verilmedi.';unset($criterion);
scoringDenied(fn()=>Review::questionResult($bad,$request),'criterion reason checked independently of overall feedback');
$bad=$out;foreach($bad['scores'] as &$criterion)$criterion['feedback']='Öğrenci yanıtı paylaşılmadığından puan verilemiyor.';unset($criterion);
scoringDenied(fn()=>Review::questionResult($bad,$request),'criterion feedback checked independently');
$bad=$out;foreach($bad['scores'] as &$criterion)$criterion['quote']='Kaynakta bulunmayan alıntı.';unset($criterion);
scoringDenied(fn()=>Review::questionResult($bad,$request),'fabricated evidence still rejected',422);
$empty=$request;$empty['sources']=['decision'=>''];
scoringDenied(fn()=>Ai::questionMessages($empty),'empty answer never triggers a provider scoring request');
$multiple=$request;$multiple['sources']['other']='Başka sorunun yanıtı.';
scoringDenied(fn()=>Ai::questionMessages($multiple),'multiple answers never enter a single-question scoring request');
echo 'TOTAL scoring presence '.$count.' checks passed; no provider calls.'.PHP_EOL;
