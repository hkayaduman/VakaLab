<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
$n=0;function contractCheck(bool $ok,string $label): void {global $n;if(!$ok)throw new RuntimeException($label);echo 'PASS '.(++$n).' '.$label.PHP_EOL;}
$rubric=['text'=>'Gerekçe ve yansıtmayı birlikte inceleyin.','dimensions'=>[['id'=>'reasoning','name'=>'Gerekçelendirme','min'=>0,'max'=>4,'weight'=>1,'levels'=>[['name'=>'Eksik','score'=>0,'description'=>'Gerekçe yok.'],['name'=>'Açık','score'=>4,'description'=>'Gerekçe vakayla ilişkili.']]]]];
$qas=[];$sources=[];foreach(Review::QUESTIONS as $section=>$questions)foreach($questions as $id=>$question){$answer='SENTETİK '.$id.' yanıtı: eşit katılımı desteklerim.';$sources[$id]=$answer;$qas[]=['source_id'=>$id,'section'=>(string)$section,'question'=>$question,'answer'=>$answer];}
$p=['task'=>'holistic_review','case'=>'SENTETİK vaka: öğrencinin sözü kesiliyor.','rubric'=>$rubric,'question_answers'=>$qas,'sources'=>$sources,'profile_comparison'=>[['title'=>'Güven','initial'=>5,'final'=>7,'change'=>2]],'private'=>'NEVER SEND'];
$messages=Ai::holisticMessages($p);$wire=obj($messages[1]['content']);
contractCheck(count($messages)===2&&$messages[0]['role']==='system'&&$messages[1]['role']==='user','one instruction and one data message');
contractCheck($wire['case']===$p['case']&&$wire['rubric']===$rubric,'case and complete rubric preserved');
contractCheck($wire['question_answers']===$qas&&count($qas)===6,'all questions paired with exact full answers in wire payload');
contractCheck($wire['profile_comparison']===$p['profile_comparison'],'locally calculated profile sent in same request');
contractCheck(!isset($wire['private'],$wire['sources']),'no private field or duplicate answer map transmitted');
$schema=Ai::responseFormat($p,true)['json_schema']['schema'];
contractCheck($schema['required']===['report','questions']&&$schema['additionalProperties']===false&&$schema['properties']['questions']['required']===array_keys($sources),'one report requires every question feedback and score');
$score=$schema['properties']['questions']['properties']['reason']['properties']['scores']['properties']['reasoning'];
contractCheck($score['properties']['level']['enum']===['Eksik','Açık']&&$score['properties']['score']['enum']===[0.0,4.0],'rubric levels and points constrain output');
contractCheck($score['properties']['source']['enum']===['reason'],'evidence can point only to this question answer');
$c=require __DIR__.'/../config/example.php';$result=(new Ai($c))->call($p,true);
contractCheck(is_string($result['report'])&&count($result['questions'])===6,'real mock adapter supports holistic contract');
contractCheck(Rules::scores($result['questions']['reason'],$rubric,['reason'=>$sources['reason']])===0.0,'mock zero score and genuine source quote validate');
echo 'TOTAL HOLISTIC CONTRACT '.$n.' checks passed; no external calls.'.PHP_EOL;
