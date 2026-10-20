<?php
declare(strict_types=1);
final class Scope {
    public static function owned(Db $db,array $u,string $table,string $id): array {
        Auth::researcher($u);demand(in_array($table,['users','cases','definitions'],true),'Geçersiz kapsam');
        $row=$db->one('SELECT * FROM '.$table.' WHERE id=?',[$id]);
        $allowed=$row&&$row['owner_id']===$u['id'];
        demand($allowed,'Bu kayıt çalışma alanınıza ait değil',403);return $row;
    }
    public static function application(Db $db,array $u,string $id): array {
        Auth::researcher($u);$a=$db->one('SELECT a.* FROM applications a JOIN cases c ON c.id=a.case_id WHERE a.id=? AND c.owner_id=?',[$id,$u['id']]);
        demand((bool)$a,'Bu çalışma alanına erişemezsiniz',403);return $a;
    }
    public static function run(Db $db,array $u,string $rid): array {
        $r=$db->one('SELECT * FROM analysis_runs WHERE id=?',[$rid]);demand((bool)$r,'Analiz yok',404);self::application($db,$u,$r['app_id']);return $r;
    }
    public static function step(Db $db,array $u,string $sid): array {
        $s=$db->one('SELECT * FROM analysis_steps WHERE id=?',[$sid]);demand((bool)$s,'Analiz adımı yok',404);self::run($db,$u,$s['run_id']);return $s;
    }
    public static function collectionClosed(array $a): bool { return !empty($a['collection_closed_at'])&&empty($a['allow_late']); }
}
