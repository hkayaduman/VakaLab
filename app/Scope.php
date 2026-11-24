<?php
declare(strict_types=1);
final class Scope {
    public static function owned(Db $db,array $u,string $table,string $id): array {
        Auth::staff($u);demand(in_array($table,['users','cases','definitions'],true),'Geçersiz kapsam');
        $row=$db->one('SELECT * FROM '.$table.' WHERE id=?',[$id]);
        demand((bool)$row,'Bu kayıt çalışma alanınıza ait değil',403);
        if($table==='users'){
            $shared=$row['owner_id']===$u['id']&&$db->one('SELECT a.workspace_id FROM workspace_memberships a JOIN workspace_memberships b ON b.workspace_id=a.workspace_id WHERE a.user_id=? AND b.user_id=?',[$u['id'],$id]);
            demand(Auth::isSuperAdmin($u)||$shared,'Bu hesabı düzenleme yetkiniz yok',403);
        }else Workspace::requireAccess($db,$u,$row['workspace_id']);
        return $row;
    }
    public static function workspaceDefinition(Db $db,array $u,string $id,string $workspace): array {
        $definition=self::owned($db,$u,'definitions',$id);
        demand($definition['workspace_id']===$workspace,'Tanım bu çalışma alanına ait değil',403);
        return $definition;
    }
    public static function application(Db $db,array $u,string $id): array {
        Auth::staff($u);
        $a=$db->one('SELECT a.*,c.workspace_id FROM applications a JOIN cases c ON c.id=a.case_id WHERE a.id=?',[$id]);
        demand((bool)$a,'Bu çalışma alanına erişemezsiniz',403);
        Workspace::requireAccess($db,$u,$a['workspace_id']);
        return $a;
    }
    public static function run(Db $db,array $u,string $rid): array {
        $r=$db->one('SELECT * FROM analysis_runs WHERE id=?',[$rid]);demand((bool)$r,'Analiz yok',404);self::application($db,$u,$r['app_id']);return $r;
    }
    public static function step(Db $db,array $u,string $sid): array {
        $s=$db->one('SELECT * FROM analysis_steps WHERE id=?',[$sid]);demand((bool)$s,'Analiz adımı yok',404);self::run($db,$u,$s['run_id']);return $s;
    }
    public static function collectionClosed(array $a): bool { return !empty($a['collection_closed_at'])&&empty($a['allow_late']); }
}
