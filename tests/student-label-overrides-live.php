<?php
require __DIR__.'/../app/bootstrap.php';
if(getenv('VAKALAB_RUN_LIVE_PROMPT_TEST')!=='1'){echo "SKIP: VAKALAB_RUN_LIVE_PROMPT_TEST=1 is required.\n";exit;}
$c=cfg();demand($c['ai']['model']==='openai/gpt-5-nano','GPT-5 Nano gerekli');
$defs=[
 ['id'=>'custom-information','name'=>'Bilgi istedi','body'=>['target_role'=>'user','topic_scope'=>'pedagogical','description'=>'Yalnız sayısal bir istatistiğin kaynağını soran mesajlar. Temel kavram tanımlarını ve neden-sonuç açıklaması taleplerini dışla.']],
 ['id'=>'custom-explanation','name'=>'Açıklama istedi','body'=>['target_role'=>'user','topic_scope'=>'pedagogical','description'=>'Bu özel araştırma tanımında yalnız bir kavramın temel tanımını isteyen mesajlar kodlanır. Neden veya işleyiş sorularını dışla.']],
 ['id'=>'custom-offtopic','name'=>'Konu dışı istek','body'=>['target_role'=>'user','topic_scope'=>'outside','description'=>'Pedagojik soruyla ilişkisi olmayan kişisel yatırım hesabı veya finansal ürün seçtirme talepleri. Sınıfta finansal okuryazarlık öğretimini sormayı dışla.']]
];
$cases=[
 ['id'=>'override-1','role'=>'user','content'=>'Empati nedir?','expected_names'=>['Açıklama istedi']],
 ['id'=>'override-2','role'=>'user','content'=>'Bu yüzde otuz istatistiğinin kaynağı ne?','expected_names'=>['Bilgi istedi']],
 ['id'=>'override-3','role'=>'user','content'=>'Bu yaklaşım neden işe yarıyor?','expected_names'=>[]],
 ['id'=>'override-4','role'=>'user','content'=>'Kendi birikimim için hangi yatırım hesabını seçmeliyim?','expected_names'=>['Konu dışı istek']]
];
$messages=array_map(function($r){unset($r['expected_names']);return $r;},$cases);$payload=MessageLabels::payload($messages,$defs,'user');foreach($payload['messages'] as &$target)$target['context_before']=[];unset($target);$dir=__DIR__.'/../test-evidence/student-labels-2026-10-24/'.MessageLabels::DIRECTIVE;if(!is_dir($dir)&&!@mkdir($dir,0777,true)&&!is_dir($dir))throw new RuntimeException('Kanıt klasörü yok');file_put_contents($dir.'/override-expected-before-call.json',js(['cases'=>$cases,'payload'=>$payload]));$out=(new Ai($c))->call($payload,true);file_put_contents($dir.'/override-output.json',js($out));$map=MessageLabels::validate($out,$messages,$defs);$names=array_column($defs,'name','id');$results=[];$passed=0;foreach($cases as $m){$actual=array_map(fn($id)=>$names[$id],$map[$m['id']]);$expected=$m['expected_names'];sort($actual);sort($expected);$ok=$actual===$expected;if($ok)$passed++;$r=['id'=>$m['id'],'expected'=>$expected,'actual'=>$actual,'passed'=>$ok];$results[]=$r;if(!$ok)echo js($r).PHP_EOL;}
file_put_contents($dir.'/override-comparison.json',js(['at'=>now(),'passed'=>$passed,'total'=>4,'custom_definitions_only'=>true,'working_database_opened'=>false,'results'=>$results]));echo 'CUSTOM DESCRIPTION OVERRIDE '.$passed.'/4'.PHP_EOL;if($passed!==4)exit(1);
