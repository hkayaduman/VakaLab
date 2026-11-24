<?php
declare(strict_types=1);

final class Workspace {
    public const DEFAULT_ID='default';

    public static function all(Db $db): array {
        return $db->all("SELECT id,name FROM workspaces ORDER BY CASE WHEN id='default' THEN 0 ELSE 1 END,name,id");
    }

    public static function visible(Db $db,array $user): array {
        if(Auth::isSuperAdmin($user))return self::all($db);
        return $db->all("SELECT w.id,w.name FROM workspaces w JOIN workspace_memberships m ON m.workspace_id=w.id WHERE m.user_id=? ORDER BY CASE WHEN w.id='default' THEN 0 ELSE 1 END,w.name,w.id",[$user['id']]);
    }

    public static function member(Db $db,string $workspace,string $user): bool {
        return (bool)$db->one('SELECT user_id FROM workspace_memberships WHERE workspace_id=? AND user_id=?',[$workspace,$user]);
    }

    public static function grant(Db $db,string $workspace,string $user): void {
        $sql=$db->sqlite()
            ?'INSERT INTO workspace_memberships (workspace_id,user_id) VALUES (?,?) ON CONFLICT(workspace_id,user_id) DO NOTHING'
            :'INSERT INTO workspace_memberships (workspace_id,user_id) VALUES (?,?) ON DUPLICATE KEY UPDATE user_id=VALUES(user_id)';
        $db->q($sql,[$workspace,$user]);
    }

    public static function requireAccess(Db $db,array $user,string $workspace): void {
        $allowed=Auth::isSuperAdmin($user)
            ?(bool)$db->one('SELECT id FROM workspaces WHERE id=?',[$workspace])
            :self::member($db,$workspace,$user['id']);
        demand($allowed,'Bu çalışma alanına erişemezsiniz',403);
    }

    public static function selected(Db $db,array $user,mixed $requested=null): ?string {
        if($requested===null||$requested==='')return self::visible($db,$user)[0]['id']??null;
        demand(is_string($requested),'Çalışma alanı geçersiz');
        self::requireAccess($db,$user,$requested);
        return $requested;
    }

    public static function required(Db $db,array $user,mixed $requested=null): string {
        $id=self::selected($db,$user,$requested);
        demand($id!==null,'Önce bir çalışma alanına katılın',403);
        return $id;
    }
}
