<?php
require __DIR__.'/../app/bootstrap.php';
if(getenv('VAKALAB_RUN_LIVE_PROMPT_TEST')!=='1'){echo "SKIP: VAKALAB_RUN_LIVE_PROMPT_TEST=1 is required.\n";exit;}
$c=cfg();demand($c['ai']['model']==='openai/gpt-5-nano'&&$c['ai']['endpoint']==='https://openrouter.ai/api/v1/chat/completions','OpenRouter GPT-5 Nano ayarı gerekli');$db=new Db($c);$db->pdo->exec('PRAGMA query_only=ON');$manifest=obj(file_get_contents(__DIR__.'/../test-evidence/label-catalog-2026-10-23/installation.json'));$defs=$db->all("SELECT d.id,d.name,d.body FROM definitions d WHERE d.owner_id=? AND d.family='label' AND d.archived=0 AND d.deleted_at IS NULL AND NOT EXISTS(SELECT 1 FROM definitions n WHERE n.parent_id=d.id) ORDER BY d.id",[$manifest['owner_id']]);
$catalog=obj(file_get_contents(__DIR__.'/../label-definitions/2026-10-23.json'))['labels'];$current=MessageLabels::candidates($defs,'user');$approved=array_values(array_filter($catalog,fn($d)=>$d['body']['target_role']==='user'));demand(count($current)===count($approved),'Öğrenci etiket kümesi değişmiş; beklenen sonuçları yeniden inceleyin');foreach($approved as $item){$matches=array_values(array_filter($current,fn($d)=>$d['name']===$item['name']&&$d['description']===$item['body']['description']));demand(count($matches)===1,'Güncel etiket tanımı test beklentisinden farklı');}
$original=obj(file_get_contents(__DIR__.'/../test-evidence/label-catalog-2026-10-23/expected-before-call.json'))['cases'];$students=array_values(array_filter($original,fn($r)=>$r['role']==='user'));$context=array_map(function($r){unset($r['expected_names']);return $r;},$original);
$new=[
 ['Vakada olay bahçede mi yaşanmış?',['Bilgi istedi']],
 ['Arabuluculuk kavramını bir cümleyle tanımla.',['Bilgi istedi']],
 ['Tarafları tek tek dinlemek hangi nedenle önyargıyı azaltır?',['Açıklama istedi']],
 ['Önerdiğim kararın tutarsız noktalarını bul.',['Eleştiri istedi']],
 ['İki farklı müdahale seçeneği sun ve birini seçerek öner.',['Alternatif fikir istedi','Karar istedi']],
 ['Şu cümlenin dilini düzelt: İki öğrenciyi dinlemek lazım. Sonra gerekçemin eksiklerini değerlendir.',['Metin düzenletmek istedi','Eleştiri istedi']],
 ['Sence bu pedagojik vakada ilk hangi adımı atmalıyım?',['Karar istedi']],
 ['Bahsettiğin çözümden farklı bir yol öner.',['Alternatif fikir istedi']],
 ['Bu yöntemin nasıl uygulandığını bir örnekle anlat.',['Açıklama istedi']],
 ['Çok sağ ol, cevabını aldım.',[]],
 ['En kârlı yatırım için hangi fonu alayım?',[]],
 ['Görüşümü yazdım; başka bir şey sormuyorum.',[]]
];
$holdout=[
 ['Öz düzenleme terimi ne anlama gelir?',['Bilgi istedi']],
 ['Öz düzenleme bu öğrencinin davranışını neden etkiler, açıklar mısın?',['Açıklama istedi']],
 ['Öğretmen bu olayda neye doğrudan tanık olmuş?',['Bilgi istedi']],
 ['Şu cümledeki yazım yanlışını düzelt: Öğrencileri dinlemeliyiz çünkü farklı görüşü var.',['Metin düzenletmek istedi']],
 ['İki yeni çözüm yolu sırala; sonra hangisini seçmem gerektiğini söyle.',['Alternatif fikir istedi','Karar istedi']],
 ['Yazdığım gerekçedeki çelişkileri göster.',['Eleştiri istedi']],
 ['Bu vakada öğrencileri hangi sırayla dinlemeliyim?',['Karar istedi']],
 ['Bu müdahaleye başka bir seçenek var mı?',['Alternatif fikir istedi']],
 ['Paramı hangi kripto varlığa yatırayım?',[]],
 ['Kiracı olarak şahsi davamda hangi dilekçeyi kullanmalıyım?',[]],
 ['Velilerle finansal okuryazarlık etkinliği için bir plan öner.',['Karar istedi']],
 ['Çocuk hakları konusunu sınıfta hangi oyunla anlatayım?',['Karar istedi']],
 ['Bütçe kelimesi bu matematik dersinde ne demek?',['Bilgi istedi']],
 ['Öğrencilerin anlatımlarını karşılaştırmak neden gerekli?',['Açıklama istedi']],
 ['Teşekkürler, şu anda başka bir isteğim yok.',[]],
 ['Şunu yap.',['Belirsiz']]
];
$frozenHoldout=[
 ['Okul iklimi sözcük grubunun anlamını kısaca ver.',['Bilgi istedi']],
 ['Bu olayda çocukların farklı anlatımlarını niçin tek tek karşılaştırmak gerek?',['Açıklama istedi']],
 ['Yazdığım çözümün hangi varsayımları kanıtsız?',['Eleştiri istedi']],
 ['Bu disiplin sorunu için iki ayrı yöntem öner; sonra birini seç ve hangisiyle başlamam gerektiğini söyle.',['Alternatif fikir istedi','Karar istedi']],
 ['Emeklilik birikimimi hangi özel fona aktaracağımı seç.',[]],
 ['Öğrencilere tasarrufu öğretmek için hangi sınıf etkinliğini uygulayayım?',['Karar istedi']],
 ['Şu cümleyi dilbilgisi açısından düzelt: Öğrenciler dinleriz. Ayrıca yazdığım kararın eksiklerini belirt.',['Metin düzenletmek istedi','Eleştiri istedi']],
 ['Şimdilik yalnız okuyorum; bir isteğim yok.',[]]
];
$new=array_merge($new,$holdout,$frozenHoldout);
foreach($new as $i=>[$content,$expected])$students[]=['id'=>'fresh-student-'.($i+1),'seq'=>33+$i,'role'=>'user','content'=>$content,'expected_names'=>$expected];
$batch=(int)($argv[1]??0);demand($batch>=0&&$batch<13,'Batch 0–12');$cases=array_slice($students,$batch*4,4);$messages=array_map(function($r){unset($r['expected_names']);return $r;},$cases);
$payload=MessageLabels::payload($batch<4?$context:$messages,$defs,'user');$wanted=array_column($messages,'id');$payload['messages']=array_values(array_filter($payload['messages'],fn($m)=>in_array($m['id'],$wanted,true)));if($batch>=4)foreach($payload['messages'] as &$target)$target['context_before']=[];unset($target);$dir=__DIR__.'/../test-evidence/student-labels-2026-10-24/'.MessageLabels::DIRECTIVE;if(!is_dir($dir)&&!@mkdir($dir,0777,true)&&!is_dir($dir))throw new RuntimeException('Kanıt klasörü oluşturulamadı');file_put_contents($dir.'/expected-'.$batch.'.json',js(['cases'=>$cases,'payload'=>$payload,'prompt'=>Ai::labelSystemPrompt($payload)]));
$start=microtime(true);try{$out=(new Ai($c))->call($payload,true);file_put_contents($dir.'/raw-'.$batch.'.json',js($out));$map=MessageLabels::validate($out,$messages,$payload['label_definitions']);}catch(Fault $e){file_put_contents($dir.'/comparison-'.$batch.'.json',js(['failed'=>true,'seconds'=>round(microtime(true)-$start,3),'error'=>$e->getMessage()]));throw $e;}
$names=array_column($payload['label_definitions'],'name','id');$results=[];$passed=0;foreach($cases as $m){$actual=array_map(fn($id)=>$names[$id],$map[$m['id']]);$expected=$m['expected_names'];sort($actual);sort($expected);$ok=$actual===$expected;if($ok)$passed++;$r=['id'=>$m['id'],'content'=>$m['content'],'expected'=>$expected,'actual'=>$actual,'passed'=>$ok];$results[]=$r;if(!$ok)echo js($r).PHP_EOL;}
file_put_contents($dir.'/comparison-'.$batch.'.json',js(['model'=>$c['ai']['model'],'directive'=>MessageLabels::DIRECTIVE,'at'=>now(),'passed'=>$passed,'total'=>count($cases),'seconds'=>round(microtime(true)-$start,3),'results'=>$results]));echo 'STUDENT FOCUSED '.$passed.'/'.count($cases).PHP_EOL;if($passed!==count($cases))exit(1);
