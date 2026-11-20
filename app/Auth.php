<?php
declare(strict_types=1);
final class Auth {
    public function __construct(private Db $db) {}
    public function session(): void {
        if(session_status()===PHP_SESSION_ACTIVE)return;
        session_name('vakalab'); session_set_cookie_params(['lifetime'=>0,'path'=>(cfg()['base_path']?:'').'/','secure'=>cfg()['https'],'httponly'=>true,'samesite'=>'Strict']);session_start();
        if(!isset($_SESSION['csrf']))$_SESSION['csrf']=uid();
    }
    public function user(): array {
        $this->session();$id=$_SESSION['uid']??'';$last=$_SESSION['last']??0;
        demand(time()-$last<7200,'Oturum sona erdi',401);
        $u=$this->db->one('SELECT * FROM users WHERE id=?',[$id]);demand($u&&$u['status']==='active','Giriş gerekli / hesap erişime kapalı',401);
        demand(hash_equals(self::passwordStamp($u['password']),$_SESSION['password_stamp']??''),'Oturum yenilenmeli. Tekrar giriş yapın.',401);
        $_SESSION['last']=time();return $u;
    }
    public function csrf(): void {$this->session();demand(hash_equals($_SESSION['csrf'],$_SERVER['HTTP_X_CSRF_TOKEN']??''),'CSRF doğrulanamadı',403);}
    public function login(string $login,string $password): array {
        $this->session();$this->rateLimit('login:'.$login,15);
        $u=$this->db->one('SELECT * FROM users WHERE login=?',[$login]);$hash=$u['password']??'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
        demand(password_verify($password,$hash)&&$u&&$u['status']==='active','Giriş bilgileri geçersiz veya hesap kapalı',401);
        session_regenerate_id(true);$_SESSION=['uid'=>$u['id'],'csrf'=>uid(),'last'=>time(),'telemetry'=>uid(),'password_stamp'=>self::passwordStamp($hash)];return $u;
    }
    public function rateLimit(string $action,int $max): void {
        $key=hash('sha256',($_SERVER['REMOTE_ADDR']??'cli').'|'.$action);
        $allowed=$this->db->tx(function()use($key,$max){
            $row=$this->db->one('SELECT * FROM rate_limits WHERE id=?'.($this->db->sqlite()?'':' FOR UPDATE'),[$key]);
            $time=time();$current=$row&&$time-(int)$row['window_start']<900;
            $count=$current?min($max+1,(int)$row['count']+1):1;
            if($row)$this->db->q('UPDATE rate_limits SET count=?,window_start=? WHERE id=?',[$count,$current?$row['window_start']:$time,$key]);
            else $this->db->insert('rate_limits',['id'=>$key,'count'=>$count,'window_start'=>$time]);
            return $count<=$max;
        });
        demand($allowed,'Çok fazla deneme; 15 dakika bekleyin',429);
    }
    public static function passwordStamp(string $hash): string {return hash('sha256',$hash);}
    public static function researcher(array $u): void {demand($u['role']==='researcher','Araştırmacı yetkisi gerekli',403);}
    public static function isSuperAdmin(array $u): bool {return $u['role']==='super_admin';}
    public static function isStaff(array $u): bool {return $u['role']==='researcher'||self::isSuperAdmin($u);}
    public static function staff(array $u): void {demand(self::isStaff($u),'Yönetim paneli yetkisi gerekli',403);}
}
