<?php
declare(strict_types=1);
final class Analysis {
    public function __construct(private App $app) {}
    public function newRun(array $u,string $id,string $rubric,int $reviewVersion=1): array {
        Auth::researcher($u);$a=$this->app->access($u,$id);demand($a['state']==='submitted','Yalnız teslim edilmiş veri analiz edilir',409);$r=Scope::owned($this->app->db,$u,'definitions',$rubric);$r=$this->app->db->one("SELECT * FROM definitions WHERE id=? AND family='rubric' AND archived=0 AND deleted_at IS NULL",[$rubric]);demand((bool)$r,'Rubrik seçin');Rules::rubric(obj($r['body']));
        $run=['id'=>uid(),'app_id'=>$id,'rubric_id'=>$rubric,'created_at'=>now()];demand(in_array($reviewVersion,[1,2],true),'Değerlendirme sürümü geçersiz');$this->app->db->tx(function()use($run,$u,$reviewVersion){$this->app->db->lock($run['app_id']);$this->app->db->insert('analysis_runs',$run);if($reviewVersion===2)$this->app->db->q('UPDATE reports SET approved_at=NULL,published_at=NULL WHERE app_id=?',[$run['app_id']]);$this->app->db->audit($u['id'],'analysis_run',$run['id'],'',['review_version'=>$reviewVersion]);});return $run+['review_version'=>$reviewVersion];
    }
    public function currentStep(string $runId,int $step): ?array {
        return $this->app->db->one("SELECT * FROM analysis_steps WHERE run_id=? AND step=? AND status='completed' ORDER BY attempt DESC,id DESC LIMIT 1",[$runId,$step]);
    }
    public function payload(array $run,int $step): array {
        $db=$this->app->db;$a=$db->one('SELECT * FROM applications WHERE id=?',[$run['app_id']]);$d=obj($a['draft']);$v=$db->one('SELECT id,body,instructions FROM case_versions WHERE id=?',[$a['version_id']]);$rubric=$db->one('SELECT * FROM definitions WHERE id=?',[$run['rubric_id']]);
        $initial="Temel problem: {$d['problem']}\nKarar: {$d['decision']}\nGerekçe: {$d['reason']}";$final="Nihai karar: {$d['final_decision']}\nNihai gerekçe: {$d['final_reason']}";
        $base=['step'=>$step,'case'=>$v];
        if($step===3||$step===4)return $base+['rubric'=>obj($rubric['body']),'rubric_version'=>$rubric['id'],'sources'=>[$step===3?'initial':'final'=>$step===3?$initial:$final],'expected'=>['scores'=>['dimension_id'=>['score'=>'number in range','level'=>'level name','source'=>'source key','quote'=>'exact excerpt','reason'=>'reason','feedback'=>'Turkish student-facing feedback; no AI/model/technical language']]]];
        if($step===5)return $base+['sources'=>['initial'=>$initial,'final'=>$final],'expected'=>['summary'=>'evidence based change description','evidence'=>[['source'=>'initial or final','quote'=>'exact excerpt']]]];
        $messages=$db->all('SELECT id,seq,role,content,status FROM messages WHERE app_id=? ORDER BY seq',[$a['id']]);$sources=[];foreach($messages as $m)$sources['message:'.$m['id']]=$m['content'];
        if($step===6)return $base+['messages'=>$messages,'sources'=>$sources?:['final'=>$final],'drafts'=>$db->all('SELECT id,revision,body FROM draft_revisions WHERE app_id=? ORDER BY revision',[$a['id']]),'label_definitions'=>$db->all("SELECT d.id,d.name,d.body FROM definitions d WHERE d.family='label' AND d.archived=0 AND d.deleted_at IS NULL AND d.owner_id=(SELECT owner_id FROM cases WHERE id=?) AND NOT EXISTS(SELECT 1 FROM definitions n WHERE n.parent_id=d.id) ORDER BY d.id",[$a['case_id']]),'expected'=>['summary'=>'evidence based interaction description','evidence'=>[['source'=>'message:id','quote'=>'exact excerpt']],'labels'=>['message_id'=>['label_definition_id']]]];
        if($step===7)return $base+['sources'=>['reflection'=>$d['reflection']]+$sources,'expected'=>['summary'=>'reflection and observed process; preserve disagreement','evidence'=>[['source'=>'reflection or message:id','quote'=>'exact excerpt']]]];
        return [];
    }
    public function step(array $u,string $rid,int $step,bool $repeat=false): array {
        Auth::researcher($u);$versionRun=Scope::run($this->app->db,$u,$rid);if(Review::version($this->app->db,$rid)===2)return (new Review($this->app))->step($u,$versionRun,$step,$repeat);demand($step>=1&&$step<=8,'Adım geçersiz');$db=$this->app->db;
        $s=$db->tx(function()use($db,$rid,$step,$u,$repeat){$run=$db->one('SELECT * FROM analysis_runs WHERE id=?'.($db->sqlite()?'':' FOR UPDATE'),[$rid]);demand((bool)$run,'Analiz yok',404);
            demand(!$db->one("SELECT id FROM analysis_steps WHERE run_id=? AND status='running'",[$rid]),'Analiz adımı sürüyor',409);
            $previous=$this->currentStep($rid,$step);
            if($step===8&&$previous){$assembled=$this->assemble($run);$old=obj($previous['output']);demand(array_column($old['parts']??[],'decision_id')!==array_column($assembled['parts'],'decision_id'),'Bu kararlar zaten birleştirildi',409);}
            elseif($previous)demand($repeat&&$step>=3&&$step<=7,'Tamamlanmış adım için yeniden hesaplat seçin',409);
            if($repeat)demand($step>=3&&$step<=7,'Yalnız değerlendirme adımları yeniden hesaplatılabilir');
            if($step>1)demand((bool)$db->one("SELECT id FROM analysis_steps WHERE run_id=? AND step=? AND status='completed'",[$rid,$step-1]),'Önce önceki adımı tamamlayın',409);
            $attempt=(int)($db->one('SELECT MAX(attempt) n FROM analysis_steps WHERE run_id=? AND step=?',[$rid,$step])['n']??0)+1;
            $s=['id'=>uid(),'run_id'=>$rid,'step'=>$step,'attempt'=>$attempt,'status'=>'running','input'=>js($this->payload($run,$step)),'output'=>'{}','model'=>$this->app->config['ai']['model'],'directive'=>'analysis-v1','created_at'=>now()];$db->insert('analysis_steps',$s);$db->audit($u['id'],'analysis_step',$s['id'],'',['run'=>$rid,'step'=>$step]);return $s;});
        try{
            $run=$db->one('SELECT * FROM analysis_runs WHERE id=?',[$rid]);$a=$db->one('SELECT * FROM applications WHERE id=?',[$run['app_id']]);$d=obj($a['draft']);
            if($step===1){Rules::initial($d);Rules::final($d);$out=['integrity'=>'submitted','revision'=>(int)$a['revision'],'warnings'=>$db->all("SELECT id,status,error FROM ai_requests WHERE app_id=? AND status<>'completed'",[$a['id']])];}
            elseif($step===2)$out=['initial_confidence'=>$d['confidence'],'final_confidence'=>$d['final_confidence'],'difference'=>$d['final_confidence']-$d['confidence']];
            elseif($step===8)$out=$this->assemble($run);
            else{$payload=obj($s['input']);$out=(new Ai($this->app->config))->call($payload,true);
                if($step===3||$step===4)$out['weighted_mean']=Rules::scores($out,$payload['rubric'],$payload['sources']);
                else{demand(isset($out['summary'],$out['evidence'])&&is_string($out['summary'])&&is_array($out['evidence'])&&count($out['evidence'])>0,'Analiz JSON/kanıt geçersiz');foreach($out['evidence'] as $e)demand(isset($payload['sources'][$e['source']??''])&&trim($e['quote']??'')!==''&&str_contains($payload['sources'][$e['source']],$e['quote']),'Kanıt geçersiz');}
            }
            if($step===6)MessageLabels::validate($out,$this->payload($run,6)['messages'],$this->payload($run,6)['label_definitions']);
            $db->tx(function()use($db,$s,$out,$step,$u,$run){$fresh=$db->one('SELECT status FROM analysis_steps WHERE id=?'.($db->sqlite()?'':' FOR UPDATE'),[$s['id']]);demand($fresh['status']==='running','Geç analiz yanıtı artık geçerli değil',409);$db->q("UPDATE analysis_steps SET status='completed',output=? WHERE id=?",[js($out),$s['id']]);
                if($step===6)(new MessageLabels($this->app))->persist($u,$run['app_id'],$out['labels'],'analysis:'.$s['id'],hash('sha256',js([obj($s['input'])['messages'],obj($s['input'])['label_definitions']])));
            });
        }catch(Throwable $e){$db->q("UPDATE analysis_steps SET status='failed',error='invalid_or_failed_output' WHERE id=? AND status='running'",[$s['id']]);}
        return $db->one('SELECT * FROM analysis_steps WHERE id=?',[$s['id']]);
    }
    public function decide(array $u,string $sid,array $p): array {
        Auth::researcher($u);$versionStep=Scope::step($this->app->db,$u,$sid);if(Review::version($this->app->db,$versionStep['run_id'])===2)return (new Review($this->app))->decide($u,$versionStep,$p);$db=$this->app->db;$s=$db->one("SELECT * FROM analysis_steps WHERE id=? AND status='completed'",[$sid]);demand($s&&(int)$s['step']>=3&&(int)$s['step']<=7,'İncelenecek adım yok');
        $action=$p['action']??'';demand(in_array($action,['accept','edit','reject','uncertain'],true),'Karar geçersiz');demand(trim($p['reason']??'')!=='','Karar gerekçesi gerekli');
        $body=$action==='accept'?obj($s['output']):($action==='edit'?($p['body']??[]):[]);
        if(in_array($action,['accept','edit'],true)){if(in_array((int)$s['step'],[3,4],true)){$payload=obj($s['input']);$body['weighted_mean']=Rules::scores($body,$payload['rubric'],$payload['sources']);}else demand(isset($body['summary'])&&is_string($body['summary']),'Onaylı açıklama gerekli');}
        $decision=['id'=>uid(),'step_id'=>$sid,'actor'=>$u['id'],'action'=>$action,'body'=>js($body),'reason'=>$p['reason'],'created_at'=>now()];
        $db->tx(function()use($db,$decision,$u,$s){$db->one('SELECT id FROM analysis_runs WHERE id=?'.($db->sqlite()?'':' FOR UPDATE'),[$s['run_id']]);demand(!$db->one("SELECT id FROM analysis_steps WHERE run_id=? AND status='running'",[$s['run_id']]),'Değerlendirme sürerken karar kaydedilemez',409);demand($this->currentStep($s['run_id'],(int)$s['step'])['id']===$s['id'],'Yeni bir sonuç hazırlandı; güncel değerlendirmeyi inceleyin',409);$db->insert('decisions',$decision);$db->audit($u['id'],'decision',$decision['id'],$decision['reason']);});return $decision;
    }
    public function assemble(array $run): array {
        if(Review::version($this->app->db,$run['id'])===2)return (new Review($this->app))->assemble($run);
        $db=$this->app->db;$parts=[];$totals=[];
        for($n=3;$n<=7;$n++){$s=$this->currentStep($run['id'],$n);demand((bool)$s,'Analiz eksik');$d=$db->one('SELECT * FROM decisions WHERE step_id=? ORDER BY created_at DESC,id DESC LIMIT 1',[$s['id']]);demand((bool)$d,'Tüm AI adımlarına araştırmacı kararı gerekli');$sources=[];$approved=obj($d['body']);$suggested=obj($s['output']);foreach($approved['scores']??[] as $key=>$score){$original=$suggested['scores'][$key]??[];$sources[$key]=isset($original['score'],$original['level'])&&(float)$score['score']===(float)$original['score']&&$score['level']===$original['level']?'ai_accepted':'researcher_edited';}$parts[]=['step'=>$n,'decision_id'=>$d['id'],'action'=>$d['action'],'approved'=>$approved,'score_sources'=>$sources,'reason'=>$d['reason']];if($n===3||$n===4)$totals[$n]=in_array($d['action'],['accept','edit'],true)?(obj($d['body'])['weighted_mean']??null):null;}
        $profile=$db->one("SELECT output FROM analysis_steps WHERE run_id=? AND step=2 AND status='completed'",[$run['id']]);
        $app=$db->one('SELECT * FROM applications WHERE id=?',[$run['app_id']]);$draft=obj($app['draft']);$user=$db->one('SELECT login FROM users WHERE id=?',[$app['user_id']]);$case=$db->one('SELECT title FROM cases WHERE id=?',[$app['case_id']]);$rubric=$db->one('SELECT name,version,body FROM definitions WHERE id=?',[$run['rubric_id']]);$integrity=$db->one("SELECT output FROM analysis_steps WHERE run_id=? AND step=1 AND status='completed'",[$run['id']]);
        return ['run'=>$run['id'],'participant'=>$user['login'],'case_title'=>$case['title'],'rubric_name'=>$rubric['name'],'rubric_version'=>$run['rubric_id'],'rubric_number'=>$rubric['version'],'dimensions'=>obj($rubric['body'])['dimensions'],'profile'=>array_intersect_key($draft,array_flip(['knowledge','confidence','difficulty','severity'])),'limitations'=>'Tam yanıt aktarımı: ilk parça zamanı ölçülmedi. Görünürlük okuma/düşünme kanıtı değildir. AI süreç süresi öğrenci süresine eklenmez. Teknik uyarı sayısı: '.count(obj($integrity['output'])['warnings']),'parts'=>$parts,'confidence'=>obj($profile['output']),'initial_mean'=>$totals[3],'final_mean'=>$totals[4],'difference'=>$totals[3]!==null&&$totals[4]!==null?round($totals[4]-$totals[3],4):null,'notice'=>'AI destekli ön değerlendirme araştırmacı tarafından incelendi. Kararsız/reddedilmiş alanlar onaylı toplam sayılmaz.'];
    }
    public function report(array $u,string $op,array $p): array {
        Auth::researcher($u);$db=$this->app->db;$prepared=null;$fingerprint=null;
        if($op==='create'){
            Scope::run($db,$u,$p['run_id']);$run=$db->one('SELECT * FROM analysis_runs WHERE id=?',[$p['run_id']]);
            demand((bool)$db->one("SELECT id FROM analysis_steps WHERE run_id=? AND step=8 AND status='completed'",[$run['id']]),'Rapor birleştirme adımı gerekli');
            demand(!$db->one("SELECT id FROM analysis_steps WHERE run_id=? AND status='running'",[$run['id']]),'Değerlendirme hazırlanırken rapor oluşturulamaz',409);
            $base=$this->assemble($run);$fingerprint=js($base);$a=$db->one('SELECT draft FROM applications WHERE id=?',[$run['app_id']]);
            $student=StudentReport::build($base,obj($a['draft']));
            // Provider call occurs outside the transaction; scores are server-owned.
            $payload=['task'=>'student_feedback','report'=>$student,'rubric'=>['dimensions'=>$base['dimensions']]];
            if(($base['review_version']??1)===2){$prepared=['student'=>$student,'input'=>[],'output'=>[]];}
            else{
            $generatedFeedback=(new Ai($this->app->config))->call($payload,true);$feedback=$generatedFeedback;
            foreach(['initial'=>3,'final'=>4] as $key=>$n){$part=current(array_filter($base['parts'],fn($part)=>(int)$part['step']===$n));if($part&&in_array($part['action'],['accept','edit'],true))foreach($part['approved']['scores']??[] as $cid=>$score)if(isset($score['feedback']))$feedback[$key][$cid]=$score['feedback'];}
            $prepared=['student'=>StudentReport::feedback($student,$feedback),'input'=>$payload,'output'=>$generatedFeedback];
            }
        }
        return $db->tx(function()use($db,$u,$op,$p,$prepared,$fingerprint){
            if($op==='create'){Scope::run($db,$u,$p['run_id']);$run=$db->one('SELECT * FROM analysis_runs WHERE id=?'.($db->sqlite()?'':' FOR UPDATE'),[$p['run_id']]);demand((bool)$run,'Analiz yok');demand(!$db->one("SELECT id FROM analysis_steps WHERE run_id=? AND status='running'",[$run['id']]),'Değerlendirme hazırlanırken rapor oluşturulamaz',409);demand((bool)$db->one("SELECT id FROM analysis_steps WHERE run_id=? AND step=8 AND status='completed'",[$run['id']]),'Rapor birleştirme adımı gerekli');$a=$db->lock($run['app_id']);$body=$this->assemble($run);demand(js($body)===$fingerprint,'Değerlendirme değişti; raporu yeniden hazırlayın',409);$body['student_report']=$prepared['student'];$body['internal']=['notes'=>$db->all('SELECT body,reason,created_at FROM research_notes WHERE app_id=?',[$a['id']]),'suggestions'=>$db->all('SELECT step,attempt,status,output FROM analysis_steps WHERE run_id=? ORDER BY step,attempt',[$run['id']])];$body['internal']['student_feedback']=['input'=>$prepared['input'],'output'=>$prepared['output'],'model'=>($body['review_version']??1)===2?'system':$this->app->config['ai']['model'],'directive'=>($body['review_version']??1)===2?'approved-review-v2':'student-feedback-v1'];$v=$db->one('SELECT version FROM case_versions WHERE id=?',[$a['version_id']]);$body['case_version']=$v['version'];$body['case_id']=$a['case_id'];$r=['id'=>uid(),'app_id'=>$run['app_id'],'run_id'=>$run['id'],'version'=>(int)($db->one('SELECT MAX(version) n FROM reports WHERE app_id=?',[$run['app_id']])['n']??0)+1,'body'=>js($body),'actor'=>$u['id'],'created_at'=>now()];if(($body['review_version']??1)===2){
                    $existing=$db->one('SELECT * FROM reports WHERE app_id=? ORDER BY version DESC LIMIT 1',[$run['app_id']]);
                    if($existing&&(obj($existing['body'])['review_version']??1)===2){
                        $same=$existing['body']===$r['body'];$r['id']=$existing['id'];$r['version']=$same?$existing['version']:$r['version'];
                        $db->q('UPDATE reports SET run_id=?,version=?,body=?,actor=?,created_at=?,approved_at=?,published_at=? WHERE id=?',[$r['run_id'],$r['version'],$r['body'],$r['actor'],$r['created_at'],$same?$existing['approved_at']:null,$same?$existing['published_at']:null,$r['id']]);
                    }else $db->insert('reports',$r);
                }else $db->insert('reports',$r);}
            else{$r=$db->one('SELECT * FROM reports WHERE id=?'.($db->sqlite()?'':' FOR UPDATE'),[$p['id']]);demand((bool)$r,'Rapor yok');Scope::application($db,$u,$r['app_id']);
                if($op==='edit_feedback'){
                    demand(!$r['approved_at']&&!$r['published_at'],'Onaylanmış rapor değiştirilemez; yeni rapor hazırlayın',409);
                    $a=$db->lock($r['app_id']);$body=obj($r['body']);$student=StudentReport::projection($body,obj($a['draft']));
                    $body['student_report']=StudentReport::feedback($student,$p['feedback']??[]);
                    $body['internal']['feedback_review']=['actor'=>$u['id'],'source_report_id'=>$r['id'],'created_at'=>now()];
                    $r=['id'=>uid(),'app_id'=>$r['app_id'],'run_id'=>$r['run_id'],'version'=>(int)$db->one('SELECT MAX(version) n FROM reports WHERE app_id=?',[$a['id']])['n']+1,'body'=>js($body),'actor'=>$u['id'],'created_at'=>now()];
                    if(($body['review_version']??1)===2){
                        $source=$p['id'];$r['id']=$source;
                        $db->q('UPDATE reports SET version=?,body=?,actor=?,created_at=? WHERE id=?',[$r['version'],$r['body'],$r['actor'],$r['created_at'],$source]);
                    }else $db->insert('reports',$r);
                }
                elseif($op==='show'){
                    demand((obj($r['body'])['review_version']??1)===2,'Güncel değerlendirme raporu gerekli');
                    $latest=$db->one('SELECT id FROM reports WHERE app_id=? ORDER BY version DESC LIMIT 1',[$r['app_id']]);demand($latest['id']===$r['id'],'Güncel raporu kullanın',409);
                    $run=$db->one('SELECT * FROM analysis_runs WHERE id=?',[$r['run_id']]);$a=$db->lock($r['app_id']);$runs=$db->all('SELECT id FROM analysis_runs WHERE app_id=? ORDER BY created_at DESC,id DESC',[$r['app_id']]);foreach($runs as $candidate)if(Review::version($db,$candidate['id'])===2){demand($candidate['id']===$run['id'],'Güncel değerlendirmeyi tamamlayın',409);break;}
                    demand(!$db->one("SELECT id FROM analysis_steps WHERE run_id=? AND status='running'",[$run['id']]),'Değerlendirme hazırlanıyor',409);
                    $body=$this->assemble($run);demand(array_column($body['parts'],'decision_id')===array_column(obj($r['body'])['parts'],'decision_id'),'Raporu güncel değerlendirmeyle hazırlayın',409);
                    $c=$db->one('SELECT collection_closed_at FROM cases WHERE id=?',[$a['case_id']]);$ass=$db->one('SELECT allow_late FROM assignments WHERE user_id=? AND case_id=?',[$a['user_id'],$a['case_id']]);demand(Scope::collectionClosed($c+$ass),'Öğrenciye göstermek için bu vakada veri toplamayı kapatın',409);
                    $db->q('UPDATE reports SET approved_at=?,published_at=? WHERE id=?',[now(),now(),$r['id']]);
                }
                elseif($op==='approve'){demand(!$r['approved_at'],'Zaten onaylı');$db->q('UPDATE reports SET approved_at=? WHERE id=?',[now(),$r['id']]);}
                elseif($op==='publish'){demand($r['approved_at']&&!$r['published_at'],'Onaylı ve henüz yayımlanmamış rapor gerekli');$a=Scope::application($db,$u,$r['app_id']);$c=$db->one('SELECT collection_closed_at FROM cases WHERE id=?',[$a['case_id']]);$ass=$db->one('SELECT allow_late FROM assignments WHERE user_id=? AND case_id=?',[$a['user_id'],$a['case_id']]);demand(Scope::collectionClosed($c+$ass),'Bu vakada öğrenci için veri toplama açık',409);$db->q('UPDATE reports SET published_at=? WHERE id=?',[now(),$r['id']]);}
                else throw new Fault('Rapor işlemi geçersiz');
            }$db->audit($u['id'],'report_'.$op,$r['id'],$p['reason']??'');return $db->one('SELECT * FROM reports WHERE id=?',[$r['id']]);});
    }
}
