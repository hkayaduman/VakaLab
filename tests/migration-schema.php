<?php
declare(strict_types=1);
require __DIR__.'/../app/bootstrap.php';
require __DIR__.'/Seed.php';

$config=require __DIR__.'/../config/example.php';
$config['dsn']='sqlite::memory:';
$db=new Db($config);
Migration::run($db);
Seed::run($db,$config,uid());
$db->pdo->exec('DROP TABLE password_resets');
$db->pdo->exec('ALTER TABLE case_versions DROP COLUMN body_html');
$db->pdo->exec('DELETE FROM schema_migrations WHERE version IN (4,5)');
$before=$db->all('SELECT * FROM case_versions ORDER BY id');

function expectSchemaFailure(Db $db,string $message): void {
    try{Migration::verify($db);}catch(Fault $error){
        demand($error->getCode()===503&&str_contains($error->getMessage(),$message),'Wrong schema diagnostic');
        echo "PASS schema check rejects $message\n";
        return;
    }
    throw new RuntimeException('Schema check should have failed');
}

expectSchemaFailure($db,'4, 5');
Migration::run($db);
Migration::verify($db);
$columns=implode(',',array_keys($before[0]));
demand($db->all('SELECT '.$columns.' FROM case_versions ORDER BY id')===$before,'Migration changed historical case content');
echo "PASS upgrade from 003 and keep historical cases\n";

$db->q("UPDATE case_versions SET body_html='<p>Fixture</p>'");
$db->q('DELETE FROM schema_migrations WHERE version=5');
Migration::run($db);
$verified=$db->all('SELECT * FROM schema_migrations ORDER BY version');
Migration::run($db);
demand($db->all('SELECT * FROM schema_migrations ORDER BY version')===$verified,'Migration is not stable on rerun');
demand($db->one('SELECT body_html FROM case_versions')['body_html']==='<p>Fixture</p>','Rerun overwrote existing case HTML');
echo "PASS recover a missing 005 marker and rerun without changing data\n";

$db->pdo->exec('ALTER TABLE case_versions DROP COLUMN body_html');
expectSchemaFailure($db,'case_versions.body_html');
$db->pdo->exec('ALTER TABLE case_versions ADD COLUMN body_html LONGTEXT');
$db->pdo->exec('DROP TABLE password_resets');
expectSchemaFailure($db,'password_resets');
