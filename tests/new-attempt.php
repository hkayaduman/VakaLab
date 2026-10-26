<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';require __DIR__.'/Seed.php';
$count=0;
function attemptCheck(bool $ok,string $name): void {global $count;demand($ok,$name);echo 'PASS '.(++$count).' '.$name.PHP_EOL;}
function attemptDenied(callable $fn,string $name): void {try{$fn();}catch(Fault $e){attemptCheck(true,$name);return;}throw new RuntimeException($name);}
$c=require __DIR__.'/../config/example.php';$c['dsn']='sqlite::memory:';$c['db_user']='';$c['db_password']='';
if(getenv('VAKALAB_TEST_DSN')){demand(str_contains(getenv('VAKALAB_TEST_DSN'),'_test_'),'Ayrı test DB gerekli');$c['dsn']=getenv('VAKALAB_TEST_DSN');$c['db_user']=getenv('VAKALAB_TEST_USER');$c['db_password']=getenv('VAKALAB_TEST_PASSWORD');}
$db=new Db($c);
// Populate the old schema before upgrading, including dependent historical records.
$db->pdo->exec(file_get_contents(__DIR__.'/../migrations/001.sql'));
$db->pdo->exec(file_get_contents(__DIR__.'/../migrations/002.sql'));
foreach([1,2] as $v)$db->insert('schema_migrations',['version'=>$v,'applied_at'=>now()]);
$db->insert('users',['id'=>'history-user','login'=>'history','name'=>'History','password'=>'unused','role'=>'student','metadata'=>'{}','created_at'=>now()]);
$db->insert('cases',['id'=>'history-case','title'=>'History','position'=>99,'status'=>'active']);
$db->insert('case_versions',['id'=>'history-version','case_id'=>'history-case','version'=>1,'body'=>'History','instructions'=>'','notes'=>'','created_at'=>now()]);
$db->insert('applications',['id'=>'history-app','user_id'=>'history-user','case_id'=>'history-case','version_id'=>'history-version','state'=>'submitted','draft'=>js(Rules::draft([])),'created_at'=>now(),'submitted_at'=>now()]);
$db->insert('snapshots',['id'=>'history-snapshot','app_id'=>'history-app','kind'=>'submit','body'=>'{}','revision'=>0,'created_at'=>now()]);
$tables=['applications','snapshots','messages','events','analysis_runs','reports'];$baseline=[];
foreach($tables as $table)$baseline[$table]=$db->all('SELECT * FROM '.$table.' ORDER BY id');
Migration::run($db);
foreach($tables as $table){$after=$db->all('SELECT * FROM '.$table.' ORDER BY id');if($table==='applications')foreach($after as &$row)unset($row['attempt_no']);unset($row);attemptCheck($after===$baseline[$table],'migration preserves '.$table);}
$once=$db->all('SELECT * FROM schema_migrations');Migration::run($db);attemptCheck($once===$db->all('SELECT * FROM schema_migrations'),'migration idempotent');
Seed::run($db,$c,uid());
$app=new App($db,$c);$admin=new Admin($app);$r=$db->one("SELECT * FROM users WHERE id='test-researcher'");
$previous=$db->one("SELECT * FROM applications WHERE state='submitted' AND user_id LIKE 'test-%' LIMIT 1");$u=$db->one('SELECT * FROM users WHERE id=?',[$previous['user_id']]);$id=$previous['id'];
$old=$app->view($r,$id);
$foreign=$r;$foreign['id']='history-user';
attemptDenied(fn()=>$admin->mutate($foreign,'new_attempt',['app_id'=>$id,'reason'=>'test']),'other researcher cannot restart unowned work');
attemptDenied(fn()=>$admin->mutate($u,'new_attempt',['app_id'=>$id,'reason'=>'test']),'student cannot open attempt');
attemptDenied(fn()=>$admin->mutate($r,'new_attempt',['app_id'=>$id]),'reason required');
$db->q('UPDATE assignments SET active=0 WHERE user_id=? AND case_id=?',[$u['id'],$previous['case_id']]);
attemptDenied(fn()=>$admin->mutate($r,'new_attempt',['app_id'=>$id,'reason'=>'test']),'inactive assignment denied');
$db->q('UPDATE assignments SET active=1 WHERE user_id=? AND case_id=?',[$u['id'],$previous['case_id']]);
$db->q('UPDATE cases SET collection_closed_at=? WHERE id=?',[now(),$previous['case_id']]);
$next=$admin->mutate($r,'new_attempt',['app_id'=>$id,'reason'=>'Öğrenci yeniden başlayacak']);$fresh=$db->one('SELECT * FROM applications WHERE id=?',[$next['id']]);
attemptCheck((int)$fresh['attempt_no']===2&&$fresh['state']==='initial'&&(int)$fresh['revision']===0,'new attempt starts at beginning');
attemptCheck(obj($fresh['draft'])===Rules::draft([])&&!$fresh['initial_at']&&!$fresh['submitted_at']&&!$fresh['active_request'],'new attempt completely blank');
attemptCheck($fresh['version_id']===$previous['version_id'],'same case version pinned');
foreach(['messages','snapshots','events','analysis_runs','reports'] as $table)attemptCheck(!$db->all('SELECT * FROM '.$table.' WHERE app_id=?',[$fresh['id']]),'no copied '.$table);
attemptCheck($db->one('SELECT * FROM applications WHERE id=?',[$id])===$previous,'first application unchanged');
foreach(['messages','snapshots','events','analysis_runs','reports'] as $table)attemptCheck($old[$table]===$app->view($r,$id)[$table],'old '.$table.' unchanged');
attemptCheck($app->start($u,$previous['case_id'])['id']===$fresh['id'],'student starts latest attempt');
attemptCheck(!$app->access($u,$fresh['id'],true)['submitted_at'],'closed collection grants personal access');
attemptCheck($admin->mutate($r,'new_attempt',['app_id'=>$id,'reason'=>'repeat'])['id']===$fresh['id'],'duplicate request returns same successor');
attemptDenied(fn()=>$admin->mutate($r,'new_attempt',['app_id'=>$fresh['id'],'reason'=>'test']),'unfinished attempt cannot restart');
attemptDenied(fn()=>$app->save($u,$id,['revision'=>$previous['revision'],'draft'=>obj($previous['draft'])]),'old submission stays locked');
attemptCheck(count($app->view($u,$fresh['id'])['attempts'])===2,'student sees both attempts');
$audit=$db->one("SELECT * FROM audit WHERE target=? AND action='new_attempt'",[$fresh['id']]);attemptCheck($audit['reason']==='Öğrenci yeniden başlayacak'&&obj($audit['detail'])['previous_app_id']===$id,'audit links attempts with reason');
$d=Rules::draft([]);$d['problem']='Yeni problem';$saved=$app->save($u,$fresh['id'],['revision'=>0,'draft'=>$d]);attemptCheck((int)$saved['revision']===1,'student edits new attempt independently');
attemptCheck($admin->dashboard($r)['applications'][0]['id']===$fresh['id'],'dashboard selects latest first');
$d=obj($previous['draft']);$initial=Rules::draft(array_intersect_key($d,array_flip(['knowledge','confidence','difficulty','severity','problem','decision','reason'])));
$saved=$app->save($u,$fresh['id'],['revision'=>$saved['revision'],'draft'=>$initial],'initial');
$submitted=$app->save($u,$fresh['id'],['revision'=>$saved['revision'],'draft'=>$d],'submit');
attemptCheck($submitted['state']==='submitted','second attempt can complete full workflow');
$third=$admin->mutate($r,'new_attempt',['app_id'=>$submitted['id'],'reason'=>'Üçüncü deneme']);attemptCheck($third['attempt_no']===3,'subsequent attempts numbered independently');
attemptCheck($admin->mutate($r,'new_attempt',['app_id'=>$id,'reason'=>'stale'])['id']===$third['id'],'stale restart cannot create fourth attempt');
attemptCheck(count($app->view($u,$third['id'])['attempts'])===3,'all historical attempts remain accessible');
if($db->sqlite())attemptCheck(!$db->all('PRAGMA foreign_key_check'),'foreign key integrity preserved');
echo "OK $count checks\n";
