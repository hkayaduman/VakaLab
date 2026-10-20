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
        $u=$this->db->one('SELECT * FROM users WHERE id=?',[$id]);demand($u&&$u['status']==='active','Giriş gerekli / hesap erişime kapalı',401);$_SESSION['last']=time();return $u;
    }
    public function csrf(): void {$this->session();demand(hash_equals($_SESSION['csrf'],$_SERVER['HTTP_X_CSRF_TOKEN']??''),'CSRF doğrulanamadı',403);}
    public function login(string $login,string $password): array {
        $this->session();$key=hash('sha256',($_SERVER['REMOTE_ADDR']??'cli').'|'.$login);
        $allowed=$this->db->tx(function()use($key){$r=$this->db->one('SELECT * FROM rate_limits WHERE id=?'.($this->db->sqlite()?'':' FOR UPDATE'),[$key]);$n=($r&&time()-(int)$r['window_start']<900)?(int)$r['count']+1:1;if($r)$this->db->q('UPDATE rate_limits SET count=?,window_start=? WHERE id=?',[$n,$n===1?time():$r['window_start'],$key]);else $this->db->insert('rate_limits',['id'=>$key,'count'=>1,'window_start'=>time()]);return $n<=15;});
        demand($allowed,'Çok fazla giriş denemesi; 15 dakika bekleyin',429);
        $u=$this->db->one('SELECT * FROM users WHERE login=?',[$login]);$hash=$u['password']??'$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.';
        demand(password_verify($password,$hash)&&$u&&$u['status']==='active','Giriş bilgileri geçersiz veya hesap kapalı',401);
        session_regenerate_id(true);$_SESSION=['uid'=>$u['id'],'csrf'=>uid(),'last'=>time(),'telemetry'=>uid()];return $u;
    }
    public static function researcher(array $u): void {demand($u['role']==='researcher','Araştırmacı yetkisi gerekli',403);}
}
