<?php
declare(strict_types=1);
final class Migration {
    public static function run(Db $db): void {
        $exists=$db->sqlite()?$db->one("SELECT name FROM sqlite_master WHERE type='table' AND name='schema_migrations'"):$db->one("SHOW TABLES LIKE 'schema_migrations'");
        if(!$exists){
            if(!$db->sqlite()){$db->pdo->exec('SET default_storage_engine=InnoDB');$db->pdo->exec('SET NAMES utf8mb4');}
            $db->pdo->exec(file_get_contents(__DIR__.'/../migrations/001.sql'));
            $db->insert('schema_migrations',['version'=>1,'applied_at'=>now()]);
        }
        demand((bool)$db->one('SELECT version FROM schema_migrations WHERE version=1'),'Yarım kalmış migration: şemayı inceleyin');
        if(!$db->one('SELECT version FROM schema_migrations WHERE version=2')){
            $apply=function()use($db){
                foreach(explode(';',file_get_contents(__DIR__.'/../migrations/002.sql')) as $sql)if(trim($sql)!=='')$db->pdo->exec($sql);
                $first=$db->one("SELECT id FROM users WHERE role='researcher' ORDER BY created_at,id LIMIT 1");
                if($first){
                    foreach(['cases','definitions'] as $table)$db->q('UPDATE '.$table.' SET owner_id=? WHERE owner_id IS NULL',[$first['id']]);
                    $db->q("UPDATE users SET owner_id=? WHERE role='student' AND owner_id IS NULL",[$first['id']]);
                }
                // Preserve any historical global closure as individual case closures.
                $closed=$db->one('SELECT closed_at FROM study WHERE id=1')['closed_at'];
                if($closed)$db->q('UPDATE cases SET collection_closed_at=?',[$closed]);
                $db->insert('schema_migrations',['version'=>2,'applied_at'=>now()]);
            };
            if($db->sqlite())$db->tx($apply);else $apply(); // MySQL DDL requires prior backup; never auto-retry a partial migration.
        }
    }
}
