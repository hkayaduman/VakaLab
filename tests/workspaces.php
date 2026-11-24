<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
require __DIR__.'/Seed.php';

$config=require __DIR__.'/../config/example.php';
$config['dsn']='sqlite::memory:';
if($dsn=getenv('VAKALAB_TEST_DSN')){
    demand(str_contains($dsn,'host=127.0.0.1;')&&str_contains($dsn,'dbname=vakalab_test_'),'Use a disposable database');
    $config['dsn']=$dsn;
    $config['db_user']=getenv('VAKALAB_TEST_USER');
    $config['db_password']=getenv('VAKALAB_TEST_PASSWORD');
}
$db=new Db($config);
Migration::run($db);
$password='VakaLab Fixture Password';
Seed::run($db,$config,$password);
$app=new App($db,$config);
$admin=new Admin($app);
$export=new Export($app);
$access=new AccountAccess($db,true);
$user=fn($id)=>$db->one('SELECT * FROM users WHERE id=?',[$id]);
$researcher=$user('test-researcher');
$student=$user('test-student-1');
$root=['id'=>'workspace-admin','role'=>'super_admin'];
$db->insert('users',$root+['login'=>'WORKSPACE-ADMIN','name'=>'Test Admin','password'=>'unused','metadata'=>'{}','test'=>1,'created_at'=>now()]);
$mutate=fn($action,$p)=>$admin->mutate($root,$action,$p);

function verifyWorkspace(bool $passed,string $description): void {
    demand($passed,$description);
    echo "PASS WORKSPACE $description\n";
}
function deniedWorkspace(callable $operation,int $status,string $description): void {
    try{$operation();}catch(Fault $e){
        verifyWorkspace(($e->getCode()?:422)===$status,$description);
        return;
    }
    throw new RuntimeException('Expected denial: '.$description);
}

$source=Workspace::DEFAULT_ID;
$target=$mutate('workspace_create',['name'=>'Hedef araştırma alanı'])['id'];
$isolated=$mutate('workspace_create',['name'=>'Ayrı araştırma alanı'])['id'];
deniedWorkspace(fn()=>$admin->dashboard($researcher,$target),403,'researcher cannot list a workspace without membership');
$access->register(['workspace_id'=>$isolated,'login'=>'SELF-WS','name'=>'Self Student','password'=>$password]);
$self=$db->one("SELECT * FROM users WHERE login='SELF-WS'");
verifyWorkspace(array_column(Workspace::visible($db,$self),'id')===[$isolated],'registration grants the selected workspace immediately');
deniedWorkspace(fn()=>$app->start($self,'test-case-1'),403,'membership does not create a case assignment');
$mutate('workspace_memberships',['user_id'=>$researcher['id'],'workspace_ids'=>[$source,$target]]);
$otherId=$admin->mutate($researcher,'participant',['workspace_id'=>$target,'role'=>'researcher','login'=>'RESEARCHER-WS','password'=>$password])['id'];
$other=$user($otherId);
verifyWorkspace(array_column(Workspace::visible($db,$other),'id')===[$target],'researcher creates a researcher in an accessible workspace');
deniedWorkspace(fn()=>$admin->mutate($researcher,'participant',['workspace_id'=>$isolated,'login'=>'OUTSIDE-WS','password'=>$password]),403,'researcher cannot create an account in another workspace');
deniedWorkspace(fn()=>$admin->mutate($researcher,'workspace_memberships',['user_id'=>$self['id'],'workspace_ids'=>[$target]]),403,'only super admin can change existing memberships');
$bulk=$admin->mutate($researcher,'participants_bulk',['workspace_id'=>$target,'rows'=>[['login'=>'BULK-WS','password'=>$password]]]);
$bulkUser=$db->one("SELECT * FROM users WHERE login='BULK-WS'");
verifyWorkspace($bulk['count']===1&&Workspace::member($db,$target,$bulkUser['id'])&&!Workspace::member($db,$source,$bulkUser['id']),'bulk grants only the selected workspace');
foreach([str_repeat('ş',6),str_repeat('Z',73)] as $bad){
    deniedWorkspace(fn()=>$admin->mutate($researcher,'participant',['workspace_id'=>$target,'login'=>'BAD-WS','password'=>$bad]),422,'single account rejects invalid password');
    deniedWorkspace(fn()=>$admin->mutate($researcher,'participants_bulk',['workspace_id'=>$target,'rows'=>[['login'=>'BAD-WS','password'=>$bad]]]),422,'bulk account rejects the same invalid password');
}
deniedWorkspace(fn()=>$admin->mutate($researcher,'participant',['workspace_id'=>$target,'login'=>'BULK-WS','password'=>$password]),409,'single account reports duplicate as a conflict');
deniedWorkspace(fn()=>$admin->mutate($researcher,'assign',['case_id'=>'test-case-1','user_id'=>$bulkUser['id'],'active'=>true]),403,'case assignment requires membership in the case workspace');

$case='test-case-1';
$revoked=$mutate('participant',['login'=>'REVOKED-WS','password'=>$password])['id'];
$admin->mutate($researcher,'assign',['case_id'=>$case,'user_id'=>$revoked,'active'=>true]);
$admin->mutate($researcher,'assign',['case_id'=>$case,'user_id'=>$revoked,'active'=>false]);
$id=$app->start($student,$case)['id'];
$at=now();
$items=[
    ['key'=>uid(),'tab'=>'workspace-test','seq'=>1,'at'=>$at,'type'=>'section_open','section'=>'case','payload'=>['view_id'=>'measurement','app_active'=>true]],
    ['key'=>uid(),'tab'=>'workspace-test','seq'=>2,'at'=>(new DateTimeImmutable($at))->modify('+2 seconds')->format('Y-m-d\TH:i:s.u\Z'),'type'=>'section_open','section'=>'profile','payload'=>['view_id'=>'measurement','app_active'=>true]],
];
(new Telemetry($app))->ingest($student,$id,['events'=>$items],'workspace-session');
$previous=$db->one('SELECT * FROM applications WHERE id=?',[$id]);
$before=$export->disco($researcher,['app_id'=>$id,'include_test'=>1]);
$events=$db->all('SELECT * FROM events WHERE app_id=? ORDER BY client_seq',[$id]);
$reviewApp=$db->one("SELECT id FROM applications WHERE case_id=? AND user_id='test-student-6'",[$case])['id'];
$history=$app->view($researcher,$reviewApp);
$detailed=$export->disco($researcher,['app_id'=>$reviewApp,'include_test'=>1,'view'=>'events']);
$sourceRubric=$db->one("SELECT * FROM definitions WHERE id='test-rubric-v1'");
deniedWorkspace(fn()=>$app->view($other,$id),403,'destination researcher has no access before move');
deniedWorkspace(fn()=>$admin->mutate($researcher,'move_case',['case_id'=>$case,'workspace_id'=>$target]),403,'researcher cannot move a case');
$mutate('move_case',['case_id'=>$case,'workspace_id'=>$target]);
$moved=$app->view($other,$id);
$review=$app->view($other,$reviewApp);
verifyWorkspace($moved['application']['workspace_id']===$target&&$db->one('SELECT * FROM applications WHERE id=?',[$id])===$previous,'move preserves the application and switches its workspace');
verifyWorkspace($db->all('SELECT * FROM events WHERE app_id=? ORDER BY client_seq',[$id])===$events&&$before===$export->disco($other,['app_id'=>$id,'include_test'=>1]),'raw events and Disco output survive the workspace transfer byte for byte');
verifyWorkspace((new Telemetry($app))->ingest($student,$id,['events'=>[$items[1]]],'workspace-session')['accepted']===[$items[1]['key']],'queued events replay safely using the original application and session after move');
verifyWorkspace(Workspace::member($db,$target,$student['id'])&&Workspace::member($db,$source,$student['id'])&&Workspace::member($db,$target,'test-student-2'),'working and assigned-only students gain membership and keep source membership');
verifyWorkspace((bool)$db->one("SELECT id FROM assignments WHERE case_id=? AND user_id='test-student-2' AND active=1",[$case]),'move preserves the unopened assignment');
verifyWorkspace(!Workspace::member($db,$target,$revoked),'a revoked assignment with no student data grants no destination membership');
verifyWorkspace($sourceRubric===$db->one("SELECT * FROM definitions WHERE id='test-rubric-v1'")&&$review['analysis_runs'][0]['rubric_id']!==$history['analysis_runs'][0]['rubric_id'],'referenced rubric version is copied and source remains unchanged');
$sourceLabels=array_column($history['labels'],'definition_id','id');
foreach($review['labels'] as $label){
    $definition=$db->one('SELECT workspace_id,origin_id FROM definitions WHERE id=?',[$label['definition_id']]);
    demand($definition['workspace_id']===$target&&$definition['origin_id']===$sourceLabels[$label['id']],'label definition did not follow the moved application');
}
verifyWorkspace(count($sourceLabels)>0&&count($review['labels'])===count($sourceLabels),'label versions are copied without losing research label decisions');
verifyWorkspace($detailed===$export->disco($other,['app_id'=>$reviewApp,'include_test'=>1,'view'=>'events']),'detailed Disco output also preserves labels and event order');
$beforeReports=array_column($history['reports'],null,'id');
$afterReports=array_column($review['reports'],null,'id');
ksort($beforeReports);
ksort($afterReports);
verifyWorkspace(array_keys($beforeReports)===array_keys($afterReports),'historical reports keep their IDs');
verifyWorkspace(!in_array($case,array_column($admin->dashboard($researcher,$source)['cases'],'id'),true),'case disappears from source dashboard');
$count=count($admin->dashboard($root,$target)['definitions']);
$mutate('move_case',['case_id'=>$case,'workspace_id'=>$target]);
verifyWorkspace($count===count($admin->dashboard($root,$target)['definitions']),'retry does not duplicate definition copies');
$admin->mutate($other,'assign',['case_id'=>$case,'user_id'=>$bulkUser['id'],'active'=>true]);
verifyWorkspace($app->start($bulkUser,$case)['case_id']===$case,'workspace colleague can assign a workspace member');
$admin->mutate($other,'assign',['case_id'=>$case,'user_id'=>$bulkUser['id'],'active'=>false]);
deniedWorkspace(fn()=>$app->start($bulkUser,$case),403,'researcher can revoke an assignment');
$mutate('workspace_memberships',['user_id'=>$student['id'],'workspace_ids'=>[$source]]);
deniedWorkspace(fn()=>$app->view($student,$id),403,'removing membership blocks existing application access');
verifyWorkspace(count($app->view($other,$id)['events'])===count($events),'revoking membership preserves historical research data');
