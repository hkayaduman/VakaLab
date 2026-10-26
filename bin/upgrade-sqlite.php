<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require __DIR__.'/../app/bootstrap.php';
try{
    $db=new Db(cfg());demand($db->sqlite(),'Bu yedekli geçiş yalnız yerel SQLite içindir');
    $tableRows=$db->all("SELECT name FROM sqlite_master WHERE type='table' AND name<>'schema_migrations' ORDER BY name");
    $baseline=[];
    foreach($tableRows as $table){
        $name=$table['name'];demand(preg_match('/^[a-z_]+$/',$name)===1,'Beklenmeyen tablo');
        $columns=array_column($db->all('PRAGMA table_info('.$name.')'),'name');
        $rows=$db->all('SELECT '.implode(',',$columns).' FROM '.$name.' ORDER BY 1');
        $baseline[$name]=['columns'=>$columns,'count'=>count($rows),'hash'=>hash('sha256',js($rows))];
    }
    $backup=__DIR__.'/../storage/backup-before-0.2.0-'.uid().'.sqlite';
    $db->pdo->exec('VACUUM INTO '.$db->pdo->quote($backup));
    demand(is_file($backup)&&filesize($backup)>0,'Yedek oluşturulamadı');
    $restore=new Db(['dsn'=>'sqlite:'.$backup]);
    foreach($baseline as $name=>$old){$rows=$restore->all('SELECT '.implode(',',$old['columns']).' FROM '.$name.' ORDER BY 1');demand(hash('sha256',js($rows))===$old['hash'],'Yedek doğrulaması başarısız');}
    echo "Private SQLite backup verified.\n";
    Migration::run($db);
    foreach($baseline as $name=>$old){$rows=$db->all('SELECT '.implode(',',$old['columns']).' FROM '.$name.' ORDER BY 1');demand(hash('sha256',js($rows))===$old['hash'],'Geçiş sonrası eski alanlar değişti; özel yedeği koruyun');}
    echo 'MIGRATION 003 OK: original columns and records identical in '.count($baseline)." tables; seed/reset/collection changes not performed.\n";
    $once=$db->all('SELECT * FROM schema_migrations ORDER BY version');Migration::run($db);demand($once===$db->all('SELECT * FROM schema_migrations ORDER BY version'),'Migration tekrarında değişiklik');
    echo "REPEAT MIGRATION OK: no changes.\n";
}catch(Throwable $e){fwrite(STDERR,$e instanceof Fault?$e->getMessage().PHP_EOL:'Geçiş tamamlanamadı: '.get_class($e).PHP_EOL);exit(1);}
