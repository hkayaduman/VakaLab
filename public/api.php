<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
ApiRuntime::start();
header('Content-Type: application/json; charset=utf-8');header('Cache-Control: no-store');header('X-Content-Type-Options: nosniff');header('Referrer-Policy: same-origin');
try{
    $config=cfg();demand($config['env']!=='production'||$config['https'],'Üretimde HTTPS zorunlu',503);
    $db=new Db($config);$auth=new Auth($db);$auth->session();$op=$_GET['op']??'me';$p=[];
    if($_SERVER['REQUEST_METHOD']==='POST'){$auth->csrf();demand((int)($_SERVER['CONTENT_LENGTH']??0)<=1048576,'İstek çok büyük',413);$p=obj(file_get_contents('php://input'));}
    $writes=['login','logout','start','save','initial','submit','chat','events','onboarding','admin','new_run','step','decision','report','message_labels','holistic'];
    if(in_array($op,$writes,true))demand($_SERVER['REQUEST_METHOD']==='POST','POST gerekli',405);
    if($op==='token'){$result=['csrf'=>$_SESSION['csrf']];}
    elseif($op==='login'){$u=$auth->login($p['login']??'',$p['password']??'');$result=['role'=>$u['role'],'csrf'=>$_SESSION['csrf']];}
    elseif($op==='logout'){$_SESSION=[];session_destroy();setcookie('vakalab','',['expires'=>1,'path'=>($config['base_path']?:'').'/','secure'=>$config['https'],'httponly'=>true,'samesite'=>'Strict']);$result=['ok'=>true];}
    else{
        $u=$auth->user();$session=$_SESSION['telemetry']??'';session_write_close(); // crucial: no PHP session lock in AI or DB work
        $app=new App($db,$config);$id=$p['app_id']??$_GET['app_id']??'';
        switch($op){
        case 'me':$result=['id'=>$u['id'],'login'=>$u['login'],'name'=>$u['name'],'role'=>$u['role'],'metadata'=>array_intersect_key(obj($u['metadata']),array_flip(['program','year'])),'test'=>(bool)$u['test'],'config'=>array_intersect_key($config,array_flip(['timezone','autosave_ms','heartbeat_ms','idle_ms','consent_text'])),'mock'=>$config['ai']['provider']==='mock'];break;
        case 'onboarding':demand($u['role']==='student','Öğrenci gerekli',403);$meta=obj($u['metadata']);foreach(['program','year'] as $k)$meta[$k]=substr((string)($p[$k]??''),0,3000);$db->q('UPDATE users SET metadata=? WHERE id=?',[js($meta),$u['id']]);$db->audit($u['id'],'onboarding',$u['id']);$result=['ok'=>true];break;
        case 'cases':$result=$db->all("SELECT c.id,c.title,c.position,c.practice,c.status,c.collection_closed_at,s.allow_late,a.id app_id,a.attempt_no,a.state,a.last_section FROM cases c JOIN assignments s ON s.case_id=c.id AND s.user_id=? AND s.active=1 LEFT JOIN applications a ON a.case_id=c.id AND a.user_id=s.user_id AND a.attempt_no=(SELECT MAX(ax.attempt_no) FROM applications ax WHERE ax.case_id=c.id AND ax.user_id=s.user_id) WHERE c.stop_access=0 AND (c.status='active' OR (c.status='closed' AND a.id IS NOT NULL)) ORDER BY c.position",[$u['id']]);break;
        case 'start':$result=$app->start($u,$p['case_id']);break;
        case 'view':$result=$app->view($u,$id);break;
        case 'save':case 'initial':case 'submit':$result=$app->save($u,$id,$p,$op);break;
        case 'chat':$result=$app->chat($u,$id,$p);break;
        case 'events':$result=(new Telemetry($app))->ingest($u,$id,$p,$session);break;
        case 'dashboard':$result=(new Admin($app))->dashboard($u);break;
        case 'admin':$result=(new Admin($app))->mutate($u,$p['action'],$p);break;
        case 'new_run':$result=(new Analysis($app))->newRun($u,$id,$p['rubric_id'],(int)($p['review_version']??1));break;
        case 'step':$result=(new Analysis($app))->step($u,$p['run_id'],(int)$p['step'],!empty($p['repeat']));break;
        case 'decision':$result=(new Analysis($app))->decide($u,$p['step_id'],$p);break;
        case 'report':$result=(new Analysis($app))->report($u,$p['action'],$p);break;
        case 'holistic':$result=(new HolisticReview($app))->handle($u,$p);break;
        case 'message_labels':$targetRole=$p['target_role']??'user';demand(is_string($targetRole)&&in_array($targetRole,['user','assistant'],true),'Mesaj grubu geçersiz');$result=(new MessageLabels($app))->generate($u,$p['app_id']??'',$targetRole);break;
        case 'report_data':$result=(new Export($app))->reportData($u,$_GET['id']??'',!empty($_GET['student_view']));break;
        case 'download':
            $ex=new Export($app);$format=$_GET['format']??'json';$rids=explode(',',$_GET['report_ids']??'');$report=($rids[0]??'')!=='';$data=[];
            if($format==='disco'){
                demand(!$report,'Disco vaka olayları içindir');$bytes=$ex->disco($u,$_GET);
                header('Content-Type: text/csv; charset=utf-8');header('Content-Disposition: attachment; filename="'.(($_GET['view']??'visits')==='events'?'vakalab-disco-olaylar.csv':'vakalab-disco-gecis-sureler.csv').'"');echo $bytes;exit;
            }
            if($report){demand(count($rids)<=50,'En çok 50 rapor');$data=array_map(fn($r)=>$ex->reportData($u,$r,!empty($_GET['student_view'])),$rids);}else{
                $filters=$_GET;if(!empty($filters['all_data']))$filters=array_merge($filters,['include_test'=>1,'include_practice'=>1,'include_excluded'=>1,'coded'=>0]);
                $data=$ex->dataset($u,$filters);
            }
            $types=['json'=>'application/json','csv'=>'text/csv','xlsx'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document','pdf'=>'application/pdf'];demand(isset($types[$format]),'Dosya türü geçersiz');
            $db->audit($u['id'],'download',$report?$rids[0]:'dataset','',['format'=>$format]);
            if($format==='json')$bytes=js($data);elseif($format==='csv'){$table=$_GET['table']??'events';demand(isset($data[$table])&&!$report,'CSV tablo seçimi geçersiz');$bytes=Export::csv($data[$table]);}
            else{$path=tempnam(__DIR__.'/../storage','export-');try{if($format==='xlsx'){if($report)Export::reportXlsx($data,$path);else Export::xlsx($data,$path);}else{demand($report,'DOCX/PDF için rapor seçin');Export::reportFile($data,$format,$path);}$bytes=file_get_contents($path);}finally{if(is_file($path))unlink($path);}}
            header('Content-Type: '.$types[$format]);header('Content-Disposition: attachment; filename="vakalab.'.$format.'"');echo $bytes;exit;
        default:throw new Fault('İşlem bulunamadı',404);
        }
    }
    echo js(['data'=>$result]);
}catch(Fault $e){http_response_code(in_array($e->getCode(),[401,403,404,405,409,413,422,429,502,503],true)?$e->getCode():422);echo js(['error'=>$e->getMessage()]);}
catch(Throwable $e){http_response_code(500);error_log('VakaLab internal error: '.get_class($e));echo js(['error'=>'İşlem tamamlanamadı. Yapılandırma/veritabanı veya kayıt çakışmasını kontrol edin.']);}
