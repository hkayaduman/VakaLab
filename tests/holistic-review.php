<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
// Provider spy: counts calls and inspects the exact input without network or costs.
final class Ai {
    public static int $calls=0;public static array $last=[];public static bool $bad=false;public static $during=null;
    public function __construct(array $config) {}
    public function call(array $p,bool $analysis=false): array {
        self::$calls++;self::$last=$p;if(self::$during)(self::$during)();if(self::$bad)return ['report'=>'Geçersiz sonuç örneği.','scores'=>[]];
        $questions=[];foreach($p['question_answers'] as $q){$scores=[];
            foreach($p['rubric']['dimensions'] as $d){$l=end($d['levels']);$scores[$d['id']]=['level'=>$l['name'],'score'=>$l['score'],'source'=>$q['source_id'],'quote'=>$q['answer'],'reason'=>'Bu sorunun kendi yanıtı rubrik ölçütüne dayanır.'];}
            $questions[$q['source_id']]=['feedback'=>'Bu sorunun yanıtı ilgili vaka bilgilerine dayanır; uygulama adımlarınızı daha açık gösterebilirsiniz.','scores'=>$scores];
        }
        return ['report'=>'Her sorunun öğrenci yanıtı ve rubrik puanı ayrı incelenmiştir.','questions'=>$questions];
    }
}
$c=require __DIR__.'/../config/example.php';$c['dsn']='sqlite::memory:';$c['db_user']='';$c['db_password']='';$c['https']=false;
if(getenv('VAKALAB_TEST_DSN')){demand(str_contains(getenv('VAKALAB_TEST_DSN'),'_test_')&&str_contains(getenv('VAKALAB_TEST_DSN'),'host=127.0.0.1;'),'Yalnız yerel ayrı test DB kullanılabilir');$c['dsn']=getenv('VAKALAB_TEST_DSN');$c['db_user']=getenv('VAKALAB_TEST_USER');$c['db_password']=getenv('VAKALAB_TEST_PASSWORD');}
$db=new Db($c);Migration::run($db);$app=new App($db,$c);$service=new HolisticReview($app);$n=0;
foreach(['test-researcher'=>'researcher','test-student-6'=>'student'] as $id=>$role)$db->insert('users',['id'=>$id,'login'=>$id,'name'=>'Sentetik kullanıcı','password'=>'unused','role'=>$role,'metadata'=>'{}','test'=>1,'created_at'=>now(),'owner_id'=>$role==='student'?'test-researcher':null]);
$rubric=['text'=>'Yanıtları soruları kapsamında birlikte değerlendir.','dimensions'=>[['id'=>'reasoning','name'=>'Gerekçelendirme','min'=>0,'max'=>4,'weight'=>2,'levels'=>[['name'=>'Eksik','score'=>0,'description'=>'Gerekçe sunulmuyor.'],['name'=>'Açık','score'=>4,'description'=>'Vaka ile gerekçe açıkça ilişkilendiriliyor.']]],['id'=>'reflection','name'=>'Yansıtma','min'=>0,'max'=>2,'weight'=>1,'levels'=>[['name'=>'Eksik','score'=>0,'description'=>'Yansıtma açıklanmıyor.'],['name'=>'Açık','score'=>2,'description'=>'Karar değişimi gerekçesiyle açıklanıyor.']]]]];
$db->insert('definitions',['id'=>'test-rubric-v1','family'=>'rubric','name'=>'Sentetik rubrik','version'=>1,'body'=>js($rubric),'created_at'=>now(),'owner_id'=>'test-researcher']);
$db->insert('cases',['id'=>'case','title'=>'Sentetik grup katılımı','position'=>1,'status'=>'active','owner_id'=>'test-researcher','rubric_id'=>'test-rubric-v1']);
$db->insert('case_versions',['id'=>'version','case_id'=>'case','version'=>1,'body'=>'Grup çalışmasında bir öğrencinin sözü kesiliyor.','instructions'=>'Vakayı inceleyin.','notes'=>'ÖZEL NOT','created_at'=>now()]);
$db->insert('assignments',['id'=>'assignment','user_id'=>'test-student-6','case_id'=>'case']);
$fixtureDraft=['knowledge'=>5,'confidence'=>5,'difficulty'=>6,'severity'=>7,'problem'=>'Bir öğrencinin sözü kesiliyor ve katılımı azalıyor.','decision'=>'Sırayla konuşma ve görev paylaşımı uygularım.','reason'=>'Eşit katılım öğrencinin aidiyetini destekler.','final_decision'=>'Rolleri dönüşümlü uygular ve katılımı gözlemlerim.','final_reason'=>'Rol vermek tek başına eşit katılımı sağlamayabilir.','reflection'=>'Öneriyi katılım gözlemi ekleyerek uyarladım.','final_confidence'=>7,'final_difficulty'=>4,'final_severity'=>7];
$db->insert('applications',['id'=>'application','user_id'=>'test-student-6','case_id'=>'case','version_id'=>'version','state'=>'submitted','draft'=>js($fixtureDraft),'created_at'=>now(),'initial_at'=>now(),'submitted_at'=>now()]);
function holisticCheck(bool $ok,string $name): void {global $n;if(!$ok)throw new RuntimeException($name);echo 'PASS '.(++$n).' '.$name.PHP_EOL;}
function holisticDenied(callable $fn,string $name): void {try{$fn();}catch(Fault){holisticCheck(true,$name);return;}throw new RuntimeException('Not blocked: '.$name);}
$r=$db->one("SELECT * FROM users WHERE id='test-researcher'");$student=$db->one("SELECT * FROM users WHERE id='test-student-6'");$a=$db->one('SELECT * FROM applications WHERE user_id=?',[$student['id']]);$base=['app_id'=>$a['id'],'rubric_id'=>'test-rubric-v1','version'=>0];
$payload=$service->payload($r,$a['id'],'test-rubric-v1');$draft=obj($a['draft']);
holisticCheck(count($payload['question_answers'])===6,'all six answers included in one request');
foreach($payload['question_answers'] as $q)holisticCheck($q['answer']===$draft[$q['source_id']]&&$q['question']!==''&&$q['section']!=='','question section and answer matched: '.$q['source_id']);
holisticCheck(!isset($payload['participants'],$payload['password'],$payload['messages'],$payload['notes']),'private identifiers and unrelated data excluded');
holisticDenied(fn()=>$service->handle($student,$base+['action'=>'generate']),'student cannot generate assessment');
$other=$r;$other['id']='unowned-researcher';holisticDenied(fn()=>$service->handle($other,$base+['action'=>'save','report'=>'Yetkisiz bir rapor.','scores'=>[]]),'other researcher cannot edit work');
$choices=[];foreach($payload['rubric']['dimensions'] as $d)$choices[$d['id']]=['level'=>$d['levels'][0]['name'],'score'=>999,'reason'=>'Araştırmacının gerekçesi.'];
$reviews=[];foreach($payload['question_answers'] as $q)$reviews[$q['source_id']]=['feedback'=>'Araştırmacı bu sorunun yanıtını rubriğe göre değerlendirdi: '.$q['source_id'],'scores'=>$choices];
$manual=$service->handle($r,$base+['action'=>'save','report'=>'Araştırmacı tarafından yazılmış genel not.','questions'=>$reviews]);
holisticCheck(Ai::$calls===0,'manual report and scoring make zero provider calls');$body=obj($manual['body']);
holisticCheck($body['mode']==='manual'&&$body['student_report']['format']==='student-v3','manual report has holistic student projection');
holisticCheck($body['student_report']['total']===0,'client-supplied scores ignored and real zero retained');
holisticCheck(count($body['student_report']['sections'])===6&&count($body['student_report']['sections']['reason']['criteria'])===count($payload['rubric']['dimensions']),'six question sections with separate criterion scores');
holisticCheck($body['profile']===$payload['profile_comparison'],'profile comparison computed without provider call');
$legacy=new Analysis($app);
holisticDenied(fn()=>$legacy->step($r,$manual['run_id'],2),'old step endpoint cannot add calls to holistic report');
holisticDenied(fn()=>$legacy->report($r,'approve',['id'=>$manual['id']]),'old approval endpoint cannot bypass holistic version guard');
$p=$base;$p['version']=$manual['version'];$p['report_id']=$manual['id'];
holisticDenied(fn()=>$service->handle($r,$p+['action'=>'show']),'publication requires separate approval');
$approved=$service->handle($r,$p+['action'=>'approve']);holisticCheck((bool)$approved['approved_at']&&!$approved['published_at'],'approval does not publish');
holisticDenied(fn()=>$service->handle($r,$p+['action'=>'show']),'open data collection blocks student publication');
holisticDenied(fn()=>(new Export($app))->reportData($student,$manual['id']),'student cannot download private draft');
$db->q('UPDATE cases SET collection_closed_at=? WHERE id=?',[now(),$a['case_id']]);$shown=$service->handle($r,$p+['action'=>'show']);
holisticCheck((bool)$shown['published_at'],'closed case permits explicitly approved publication');
$public=(new Export($app))->reportData($student,$manual['id']);holisticCheck($public['report']['overall_feedback']===$body['report_text']&&!isset($public['report']['scores'],$public['report']['mode']),'student gets report projection without internal assessment fields');
Ai::$during=function()use($service,$r,$p){holisticDenied(fn()=>$service->handle($r,$p+['action'=>'generate']),'duplicate concurrent generation blocked before provider call');};
$generated=$service->handle($r,$p+['action'=>'generate']);Ai::$during=null;
holisticCheck(Ai::$calls===1,'AI assessment makes exactly one provider call');
holisticCheck($generated['id']===$manual['id']&&$generated['version']>$manual['version'],'AI updates the single current report with revision number');
holisticCheck(!$generated['approved_at']&&!$generated['published_at'],'AI replacement requires fresh approval and publication');
$newBody=obj($generated['body']);$sum=0;$max=0;foreach($payload['rubric']['dimensions'] as $d){$l=end($d['levels']);$sum+=$l['score']*$d['weight'];$max+=$d['max']*$d['weight'];}
holisticCheck($newBody['student_report']['total']===$sum*6&&$newBody['student_report']['max']===$max*6,'weighted totals calculated by application');
holisticCheck(count($db->all('SELECT * FROM analysis_steps WHERE run_id=?',[$generated['run_id']]))===1,'one assessment record instead of per-question steps');
holisticCheck(Review::version($db,$generated['run_id'])===3,'new contract version stored without schema migration');
$stale=$p;$stale['action']='approve';holisticDenied(fn()=>$service->handle($r,$stale),'stale browser cannot approve replacement report');
$p['version']=$generated['version'];Ai::$bad=true;
holisticDenied(fn()=>$service->handle($r,$p+['action'=>'generate']),'invalid model scores rejected');Ai::$bad=false;
$preserved=$db->one('SELECT * FROM reports WHERE id=?',[$generated['id']]);holisticCheck($preserved['body']===$generated['body']&&$preserved['version']===$generated['version'],'failed generation preserves last saved report');
holisticCheck(!$db->one("SELECT s.id FROM analysis_steps s JOIN analysis_runs r ON r.id=s.run_id WHERE r.app_id=? AND s.status='running' AND s.directive IN ('holistic-v1','holistic-v2')",[$a['id']]),'failed generation releases work lock');
$missing=$reviews;array_pop($missing);holisticDenied(fn()=>$service->handle($r,$p+['action'=>'save','report'=>'Araştırmacı rapor metni.','questions'=>$missing]),'manual save requires every rubric criterion');
$invalid=$reviews;$first=array_key_first($choices);$invalid['reason']['scores'][$first]['level']='Uydurma düzey';holisticDenied(fn()=>$service->handle($r,$p+['action'=>'save','report'=>'Araştırmacı rapor metni.','questions'=>$invalid]),'manual levels restricted to selected rubric');
$exporter=new Export($app);$preview=$exporter->reportData($r,$generated['id'],true);$blocks=StudentReport::blocks($preview);
holisticCheck(count(array_filter($blocks,fn($b)=>$b['type']==='score'))===7&&str_contains(js($blocks),'Başlangıç–nihai'),'one report includes scores and profile comparison');
foreach($payload['question_answers'] as $q){$section=$preview['report']['sections'][$q['source_id']];holisticCheck($section['title']===$q['question']&&$section['answer']===$q['answer']&&$section['feedback']===obj($generated['body'])['question_reviews'][$q['source_id']]['feedback'],'own question answer feedback and score stay paired: '.$q['source_id']);}
$wrong=obj($generated['body'])['question_reviews'];$wrong['reason']['scores'][$first]['source']='decision';$wrong['reason']['scores'][$first]['quote']=$draft['decision'];holisticDenied(fn()=>HolisticReview::questionResults($wrong,$payload,false),'another question cannot supply scoring evidence');
$incomplete=obj($generated['body'])['question_reviews'];unset($incomplete['reflection']);holisticDenied(fn()=>HolisticReview::questionResults($incomplete,$payload,false),'missing reflection assessment rejected');
$blank=$reviews;$blank['reflection']['feedback']='';holisticDenied(fn()=>HolisticReview::questionResults($blank,$payload,true),'manual question feedback is required');
$contradiction=obj($generated['body'])['question_reviews'];$contradiction['reason']['feedback']='Yanıt verilmedi. Bu nedenle puanlama yapılamıyor.';holisticDenied(fn()=>HolisticReview::questionResults($contradiction,$payload,false),'filled answer cannot be described as missing');
$old=$preview;$old['report']['report_layout']='legacy';$old['report']['sections']=['holistic'=>$preview['report']['sections']['reason']];holisticCheck(count(StudentReport::blocks($old))>5,'previous general report remains exportable');
$out=__DIR__.'/tmp/holistic';if(!is_dir($out))mkdir($out,0700,true);
foreach(['docx','pdf'] as $format){Export::reportFile([$preview],$format,$out.'/report.'.$format);holisticCheck(filesize($out.'/report.'.$format)>100,'holistic '.$format.' export');}
Export::reportXlsx([$preview],$out.'/report.xlsx');holisticCheck(filesize($out.'/report.xlsx')>100,'holistic xlsx export');
$zip=new ZipArchive();$zip->open($out.'/report.docx');$doc=html_entity_decode(strip_tags($zip->getFromName('word/document.xml')),ENT_QUOTES|ENT_XML1,'UTF-8');$zip->close();
$book=\PhpOffice\PhpSpreadsheet\IOFactory::load($out.'/report.xlsx');$cells=$book->getActiveSheet()->toArray();$book->disconnectWorksheets();
foreach($payload['question_answers'] as $q){$feedback=$newBody['question_reviews'][$q['source_id']]['feedback'];holisticCheck(str_contains($doc,$q['question'])&&str_contains($doc,$q['answer'])&&str_contains($doc,$feedback),'Word includes question answer feedback: '.$q['source_id']);$row=current(array_filter($cells,fn($r)=>in_array($q['question'],$r,true)&&in_array($q['answer'],$r,true)&&in_array($feedback,$r,true)));holisticCheck((bool)$row,'Excel keeps question answer and feedback on same row: '.$q['source_id']);}
echo 'TOTAL HOLISTIC '.$n.' checks passed; isolated '.($db->sqlite()?'SQLite':'MySQL').' and counted provider spy.'.PHP_EOL;
