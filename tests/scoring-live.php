<?php
/** Controlled real-provider checks; no application database is opened. */
require __DIR__.'/../app/bootstrap.php';
if(getenv('VAKALAB_RUN_LIVE_E2E')!=='1'){echo "SKIP: explicit live-test flag required\n";exit;}
$c=cfg();demand($c['ai']['model']==='openai/gpt-5-nano','Expected configured GPT-5 Nano');
$base=__DIR__.'/../test-evidence/live-workflow-2026-10-24/';$state=obj(file_get_contents(__DIR__.'/tmp/live-workflow-state.json'));$fixture=obj(file_get_contents($state['evidence'].'/fixture.json'));
$cases=[
 'supported_reason'=>['answer'=>'Öğretmenin doğrudan tanık olmaması ve anlatımların farklı olması ek bilgi toplamayı gerektirir. İki tarafı dinlemek, tek bir anlatımı doğru kabul ederek diğer öğrenciyi haksız yere suçlama riskini azaltır.','expected'=>['evidence'=>4,'perspectives'=>4]],
 'no_reason'=>['answer'=>'Bu konuda bir değerlendirmem veya gerekçem yok.','expected'=>['evidence'=>0,'perspectives'=>0]],
];
$dir=$base.'scoring-'.gmdate('Ymd-His');mkdir($dir,0777,true);
file_put_contents($dir.'/manifest.json',js(['model'=>$c['ai']['model'],'cases'=>$cases,'rubric'=>$fixture['rubric'],'source_sha256'=>hash_file('sha256',__DIR__.'/../app/Ai.php'),'prompt'=>Ai::QUESTION_SYSTEM_PROMPT]));
$results=[];
foreach($cases as $id=>$test){
 $payload=['task'=>'question_score','question'=>'Bu kararı neden veriyorsunuz?','sources'=>['answer'=>$test['answer']],'case'=>$fixture['case'],'rubric'=>$fixture['rubric']];$started=microtime(true);
 try{$out=(new Ai($c))->call($payload,true);file_put_contents($dir.'/'.$id.'-raw.json',js($out));Rules::scores($out,$fixture['rubric'],$payload['sources']);Review::text($out['feedback']??null);$actual=array_map(fn($x)=>$x['score'],$out['scores']);$ok=$actual==$test['expected'];$results[$id]=['passed'=>$ok,'actual'=>$actual,'expected'=>$test['expected'],'seconds'=>round(microtime(true)-$started,3)];}
 catch(Throwable $e){$results[$id]=['passed'=>false,'error'=>$e->getMessage(),'seconds'=>round(microtime(true)-$started,3)];}
 file_put_contents($dir.'/summary.json',js($results));echo ($results[$id]['passed']?'PASS ':'FAIL ').$id.PHP_EOL;
}
exit(count(array_filter($results,fn($x)=>!$x['passed']))?1:0);
