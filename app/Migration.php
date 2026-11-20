<?php
declare(strict_types=1);
final class Migration {
    private const VERSIONS=[1,2,3,4,5];
    private const RESET_COLUMNS=['user_id','requested_at','token_hash','password_stamp','expires_at'];

    private static function columns(Db $db,string $table): array {
        $rows=$db->sqlite()
            ?$db->all('SELECT name,type FROM pragma_table_info(?)',[$table])
            :$db->all('SELECT COLUMN_NAME AS name,DATA_TYPE AS type FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=?',[$table]);
        return array_column($rows,'type','name');
    }

    private static function checkFeature(Db $db,int $version): void {
        if($version===4){
            $missing=array_diff(self::RESET_COLUMNS,array_keys(self::columns($db,'password_resets')));
            demand(!$missing,'Şema 004 eksik: password_resets sütunları '.implode(', ',$missing),503);
        }else{
            $type=self::columns($db,'case_versions')['body_html']??'';
            demand(strtolower($type)==='longtext','Şema 005 eksik veya uyumsuz: case_versions.body_html LONGTEXT gerekli',503);
        }
    }

    public static function verify(Db $db): void {
        demand(isset(self::columns($db,'schema_migrations')['version']),'Şema sürüm tablosu eksik. Geçişi uygulayın.',503);
        $missing=array_diff(self::VERSIONS,array_column($db->all('SELECT version FROM schema_migrations'),'version'));
        demand(!$missing,'Eksik şema sürümleri: '.implode(', ',$missing),503);
        self::checkFeature($db,4);
        self::checkFeature($db,5);
    }

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
        if(!$db->one('SELECT version FROM schema_migrations WHERE version=3')){
            if($db->sqlite()){
                // Rebuild only the parent table; dependent records keep their original IDs.
                $db->pdo->exec('PRAGMA foreign_keys=OFF');
                try{$db->tx(function()use($db){
                    $schema=$db->one("SELECT sql FROM sqlite_master WHERE type='table' AND name='applications'")['sql'];
                    $schema=str_replace('CREATE TABLE applications','CREATE TABLE applications_new',$schema);
                    $schema=str_replace('UNIQUE(user_id,case_id)',"attempt_no INT NOT NULL DEFAULT 1, UNIQUE(user_id,case_id,attempt_no)",$schema);
                    $db->pdo->exec($schema);
                    $columns=implode(',',array_column($db->all('PRAGMA table_info(applications)'),'name'));
                    $db->pdo->exec('INSERT INTO applications_new ('.$columns.') SELECT '.$columns.' FROM applications');
                    $db->pdo->exec('DROP TABLE applications');
                    $db->pdo->exec('ALTER TABLE applications_new RENAME TO applications');
                    demand(!$db->all('PRAGMA foreign_key_check'),'Deneme migration bağlantı denetimi başarısız');
                    $db->insert('schema_migrations',['version'=>3,'applied_at'=>now()]);
                });}finally{$db->pdo->exec('PRAGMA foreign_keys=ON');}
            }else{
                // A single ALTER keeps the old unique index until its replacement is ready.
                $indexes=[];foreach($db->all('SHOW INDEX FROM applications') as $index)if(!(int)$index['Non_unique'])$indexes[$index['Key_name']][(int)$index['Seq_in_index']]=$index['Column_name'];
                $old=null;foreach($indexes as $name=>$columns){ksort($columns);if(array_values($columns)===['user_id','case_id'])$old=$name;}
                demand($old!==null,'Uygulama benzersiz indeksi bulunamadı');
                $db->pdo->exec('ALTER TABLE applications ADD COLUMN attempt_no INT NOT NULL DEFAULT 1, ADD UNIQUE KEY application_attempt (user_id,case_id,attempt_no), DROP INDEX `'.str_replace('`','``',$old).'`');
                $db->insert('schema_migrations',['version'=>3,'applied_at'=>now()]);
            }
        }
        foreach([4,5] as $version){
            if($db->one('SELECT version FROM schema_migrations WHERE version=?',[$version]))continue;
            $apply=function()use($db,$version){
                if($db->one('SELECT version FROM schema_migrations WHERE version=?',[$version]))return;
                if(!$db->sqlite())$db->pdo->exec('SET default_storage_engine=InnoDB');
                // MariaDB/MySQL may have committed the ALTER before its version was recorded.
                if($version!==5||!isset(self::columns($db,'case_versions')['body_html'])){
                    $sql=file_get_contents(sprintf('%s/../migrations/%03d.sql',__DIR__,$version));
                    demand($sql!==false,'Geçiş dosyası okunamadı: '.$version,503);
                    $db->pdo->exec($sql);
                }
                self::checkFeature($db,$version);
                $db->insert('schema_migrations',['version'=>$version,'applied_at'=>now()]);
            };
            if($db->sqlite())$db->tx($apply);else $apply();
        }
        self::verify($db);
    }
}
