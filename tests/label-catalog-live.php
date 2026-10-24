<?php
/** Explicit live-provider test: synthetic messages; current definitions read-only. */
require __DIR__.'/../app/bootstrap.php';
if(getenv('VAKALAB_RUN_LIVE_PROMPT_TEST')!=='1'){echo "SKIP: VAKALAB_RUN_LIVE_PROMPT_TEST=1 is required.\n";exit;}
$c=cfg();demand($c['ai']['model']==='openai/gpt-5-nano'&&$c['ai']['endpoint']==='https://openrouter.ai/api/v1/chat/completions','OpenRouter GPT-5 Nano ayarı gerekli');
$manifest=obj(file_get_contents(__DIR__.'/../test-evidence/label-catalog-2026-10-23/installation.json'));$db=new Db($c);$db->pdo->exec('PRAGMA query_only=ON');
$defs=$db->all("SELECT d.id,d.name,d.body FROM definitions d WHERE d.owner_id=? AND d.family='label' AND d.archived=0 AND d.deleted_at IS NULL AND NOT EXISTS(SELECT 1 FROM definitions n WHERE n.parent_id=d.id) ORDER BY d.id",[$manifest['owner_id']]);
$cases=[];$add=function($role,$content,$expected)use(&$cases){$cases[]=['id'=>'synthetic-'.(count($cases)+1),'seq'=>count($cases)+1,'role'=>$role,'content'=>$content,'status'=>'completed','expected_names'=>$expected];};
$add('user','Şunu bir halletsene.',['Belirsiz']);
$add('assistant','Neyi kastettiğini açıklayabilir misin?',['Soru sordu']);
$add('user','Vakada öğretmen tartışmanın başladığı anı görmüş mü?',['Bilgi istedi']);
$add('assistant','Hayır, vaka metnine göre öğretmen olaya doğrudan tanık olmamış.',['Bilgi verdi']);
$add('user','Onarıcı yaklaşım nedir?',['Bilgi istedi']);
$add('assistant','Onarıcı yaklaşım, zarar gören ilişkileri ve tarafların ihtiyaçlarını merkeze alan bir yaklaşımdır.',['Bilgi verdi']);
$add('user','Öğrencileri ayrı ayrı dinlemek neden önemli?',['Açıklama istedi']);
$add('assistant','Ayrı görüşmeler, öğrencilerin akran baskısı olmadan anlatım sunmasına yardımcı olabilir.',['Açıklama yaptı']);
$add('user','Yazdığım şu cümleyi dilbilgisi bakımından düzelt: Öğrencileri ayrı dinlemeyi düşünüyor çünkü daha rahat konuşurlar.',['Metin düzenletmek istedi']);
$add('assistant','Öğrencileri ayrı ayrı dinlemeyi düşünüyorum; çünkü böylece daha rahat konuşabilirler.',['Metin düzenledi']);
$add('user','Gerekçemde hangi noktalar eksik?',['Eleştiri istedi']);
$add('assistant','Gerekçende farklı anlatımları nasıl karşılaştıracağın ve hangi kanıtları kullanacağın belirtilmemiş.',['Eleştirel değerlendirme yaptı']);
$add('user','Mevcut çözümün dışında iki farklı yol söyle.',['Alternatif fikir istedi']);
$add('assistant','İki seçenek düşünülebilir: bireysel görüşme veya ortak arabuluculuk görüşmesi.',['Alternatif sundu']);
$add('user','Bu durumda öğretmen önce ne yapmalı?',['Karar istedi']);
$add('assistant','Önce öğrencileri ayrı ayrı dinlemeni öneririm.',['Karar veya eylem önerdi']);
$add('user','Seçenekleri ver ve hangisini uygulamam gerektiğini söyle.',['Alternatif fikir istedi','Karar istedi']);
$add('assistant','Bireysel görüşme ve ortak görüşme iki seçenektir. Önce bireysel görüşmeyi öneririm.',['Alternatif sundu','Karar veya eylem önerdi']);
$add('user','Teşekkür ederim.',[]);
$add('assistant','Rica ederim.',[]);
$add('user','Bana hangi hisseyi almam gerektiğini söyle.',[]);
$add('assistant','Bu sohbet yalnız vaka ve pedagojik sorular içindir; finansal danışmanlık veremem.',['Kapsam sınırı belirtti']);
$add('user','Vaka metninde öğrencilerin yaşı belirtilmiş mi?',['Bilgi istedi']);
$add('assistant','Sence öğretmen ne yapmalı? Kararının gerekçesini de yaz.',['Soru sordu']);
$add('user','Sadece öğretmenin olaya tanık olup olmadığını söyle.',['Bilgi istedi']);
$add('assistant','Öğretmen olaya tanık olmamış. İstersen bu vaka için bir görüşme planı da hazırlayabilirim.',['Bilgi verdi','Devam daveti ekledi']);
$add('user','Vakada öğretmenin kaç öğrenciyi dinlediği söyleniyor mu?',['Bilgi istedi']);
$add('assistant','Önce öğrencileri ayrı ayrı dinlemeni öneririm.',['Karar veya eylem önerdi']);
$add('user','Gerekçemin yeterli olup olmadığını değerlendir.',['Eleştiri istedi']);
$add('assistant','Harika bir cevap!',[]);
$add('user','Bir önceki övgüden başka bir şey sormuyorum.',[]);
$add('assistant','Birlikte başka bir çözüm geliştirmek ister misin?',['Soru sordu','Devam daveti ekledi']);
$batch=isset($argv[1])?(int)$argv[1]:null;if($batch!==null){demand($batch>=0&&$batch<4,'Batch 0–3 olmalı');$cases=array_slice($cases,$batch*8,8);}$suffix=$batch===null?'':'-batch-'.$batch;
$definitions=MessageLabels::definitions($defs);$catalog=obj(file_get_contents(__DIR__.'/../label-definitions/2026-10-23.json'))['labels'];
foreach($catalog as $item){$found=array_values(array_filter($definitions,fn($d)=>$d['name']===$item['name']&&$d['target_role']===$item['body']['target_role']));demand(count($found)===1&&$found[0]['description']===$item['body']['description'],'Kaydedilen tanım katalogla eşleşmiyor');}
$messages=array_map(function($r){unset($r['expected_names']);return $r;},$cases);
$dir=__DIR__.'/../test-evidence/label-catalog-2026-10-23/'.MessageLabels::DIRECTIVE;if(!is_dir($dir))mkdir($dir,0777,true);$expected=['cases'=>$cases,'definitions'=>$definitions,'model'=>$c['ai']['model'],'directive'=>MessageLabels::DIRECTIVE,'synthetic_messages_only'=>true,'working_database_access'=>'read-only definitions; no student messages queried'];file_put_contents($dir.'/expected-before-call'.$suffix.'.json',js($expected));
$start=microtime(true);try{$output=(new Ai($c))->call(['task'=>'message_labels','directive'=>MessageLabels::DIRECTIVE,'messages'=>$messages,'label_definitions'=>$definitions],true);}catch(Fault $e){file_put_contents($dir.'/comparison'.$suffix.'.json',js(['at'=>now(),'provider_call_failed'=>true,'seconds'=>round(microtime(true)-$start,3),'error'=>$e->getMessage()]));throw $e;}file_put_contents($dir.'/raw-output'.$suffix.'.json',js($output));
$map=MessageLabels::validate($output,$messages,$definitions);$names=array_column($definitions,'name','id');$results=[];$passed=0;
foreach($cases as $case){$actual=array_map(fn($id)=>$names[$id],$map[$case['id']]);$wanted=$case['expected_names'];sort($actual);sort($wanted);$ok=$actual===$wanted;if($ok)$passed++;$results[]=['id'=>$case['id'],'role'=>$case['role'],'content'=>$case['content'],'expected'=>$wanted,'actual'=>$actual,'passed'=>$ok];if(!$ok)echo js(end($results)).PHP_EOL;}
$result=['model'=>$c['ai']['model'],'directive'=>MessageLabels::DIRECTIVE,'at'=>now(),'seconds'=>round(microtime(true)-$start,3),'passed'=>$passed,'total'=>count($cases),'role_validation_passed'=>true,'results'=>$results];file_put_contents($dir.'/comparison'.$suffix.'.json',js($result));echo 'CATALOG COMPARISON '.$passed.'/'.count($cases)." exact message matches; synthetic conversation; working definitions read-only.\n";if($passed!==count($cases))exit(1);
