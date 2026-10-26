<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';

// Only the disposable loopback MariaDB instance from mysql_checks.py may run this.
$dsn=getenv('VAKALAB_TEST_DSN')?:'';
demand(str_starts_with($dsn,'mysql:host=127.0.0.1;')&&str_contains($dsn,'dbname=vakalab_test_'),'Disposable local test database required');
$config=['dsn'=>$dsn,'db_user'=>getenv('VAKALAB_TEST_USER'),'db_password'=>getenv('VAKALAB_TEST_PASSWORD')];
$admin=new Db($config);
$original=(int)$admin->one('SELECT @@GLOBAL.wait_timeout n')['n'];
$count=0;
function idleCheck(bool $ok,string $name): void {
    global $count;
    if(!$ok)throw new RuntimeException('FAIL: '.$name);
    echo 'PASS '.(++$count).' '.$name.PHP_EOL;
}
try {
    $admin->pdo->exec('SET GLOBAL wait_timeout=2');
    $raw=new PDO($dsn,$config['db_user'],$config['db_password'],[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    idleCheck((int)$raw->query('SELECT @@SESSION.wait_timeout')->fetchColumn()===2,'unprotected connection inherits short hosting timeout');
    $protected=new Db($config);
    idleCheck((int)$protected->one('SELECT @@SESSION.wait_timeout n')['n']>=600,'application connection extends its own idle timeout');
    idleCheck((int)$protected->one('SELECT @@GLOBAL.wait_timeout n')['n']===2,'application leaves global server timeout unchanged');
    // Simulate provider latency while neither connection sends database queries.
    sleep(3);
    $closed=false;
    try {$raw->query('SELECT 1');} catch(PDOException $e) {$closed=in_array((int)($e->errorInfo[1]??0),[2006,2013],true);}
    idleCheck($closed,'unprotected connection is lost during provider wait');
    idleCheck((int)$protected->one('SELECT 1 n')['n']===1,'application connection remains usable after provider wait');
    $protected->tx(function()use($protected){$protected->pdo->exec('CREATE TEMPORARY TABLE idle_probe (n INT)');$protected->q('INSERT INTO idle_probe VALUES (?)',[1]);});
    idleCheck((int)$protected->one('SELECT n FROM idle_probe')['n']===1,'result can be saved transactionally after provider wait');
} finally {
    $admin->pdo->exec('SET GLOBAL wait_timeout='.(int)$original);
}
