<?php
declare(strict_types=1);

final class AccountAccess {
    public function __construct(private Db $db,private bool $test) {}

    private function lock(): string {return $this->db->sqlite()?'':' FOR UPDATE';}

    private static function loginName(mixed $value): string {
        demand(is_string($value),'Hesap adını girin');
        $name=trim($value);
        demand($name!==''&&strlen($name)<=100,'Hesap adını girin (en fazla 100 karakter)');
        return $name;
    }

    public static function password(mixed $password): string {
        demand(is_string($password)&&mb_strlen($password)>=12&&strlen($password)<=72&&!str_contains($password,"\0"),'Parola 12 karakter ile 72 bayt arasında olmalı');
        return $password;
    }

    private static function codeHash(mixed $value): string {
        $code=is_string($value)?strtolower(trim($value)):'';
        demand(preg_match('/^[a-f0-9]{32}$/D',$code)===1,'Kod geçersiz veya süresi dolmuş');
        return hash('sha256',$code);
    }

    private static function validCode(?array $record,string $hash): bool {
        return $record&&is_string($record['token_hash'])&&(int)$record['expires_at']>time()&&hash_equals($record['token_hash'],$hash);
    }

    private static function canRecover(?array $user): bool {
        return $user&&$user['status']==='active'&&in_array($user['role'],['student','researcher'],true);
    }

    public function register(array $input): array {
        $login=self::loginName($input['login']??null);
        $password=self::password($input['password']??null);
        $name=self::profileName($input['name']??null);
        $workspace=$input['workspace_id']??Workspace::DEFAULT_ID;
        demand(is_string($workspace)&&(bool)$this->db->one('SELECT id FROM workspaces WHERE id=?',[$workspace]),'Geçerli bir çalışma alanı seçin');
        try {
            return $this->db->tx(function()use($login,$password,$name,$workspace){
                demand(!$this->db->one('SELECT id FROM users WHERE login=?',[$login]),'Bu numara zaten kayıtlı',409);
                $id=uid();
                $this->db->insert('users',['id'=>$id,'login'=>$login,'name'=>$name,'password'=>password_hash($password,PASSWORD_DEFAULT),
                    'role'=>'student','status'=>'active','owner_id'=>null,'metadata'=>'{}','test'=>$this->test?1:0,'created_at'=>now()]);
                Workspace::grant($this->db,$workspace,$id);
                $this->db->audit($id,'account_registered',$id,'',['workspace_id'=>$workspace]);
                return ['ok'=>true,'login'=>$login];
            });
        } catch(PDOException $e) {
            if($e->getCode()==='23000'&&$this->db->one('SELECT id FROM users WHERE login=?',[$login]))
                throw new Fault('Bu numara zaten kayıtlı',409);
            throw $e;
        }
    }

    public function addStudents(array $actor,mixed $rows,mixed $workspaceId=null): array {
        Auth::staff($actor);
        $workspace=Workspace::required($this->db,$actor,$workspaceId);
        demand(is_array($rows)&&array_is_list($rows)&&count($rows)<=100,'En fazla 100 satır gönderebilirsiniz');
        $students=[];
        foreach($rows as $index=>$row){
            $number=$index+1;
            demand(is_array($row),"$number. satır geçersiz");
            $login=$row['login']??'';
            $password=$row['password']??'';
            if(is_string($login)&&trim($login)===''&&$password==='')continue;
            try {
                $login=self::loginName($login);
                $password=self::password($password);
            } catch(Fault $e) {
                throw new Fault("$number. satır: ".$e->getMessage());
            }
            $students[$number]=['login'=>$login,'password'=>password_hash($password,PASSWORD_DEFAULT)];
        }
        demand((bool)$students,'En az bir öğrenci girin');
        return $this->db->tx(function()use($actor,$students,$workspace){
            Workspace::requireAccess($this->db,$actor,$workspace);
            foreach($students as $number=>$student){
                $id=uid();
                $duplicate="$number. satır: Bu kullanıcı adı zaten kayıtlı";
                demand(!$this->db->one('SELECT id FROM users WHERE login=?',[$student['login']]),$duplicate,409);
                try {
                    $this->db->insert('users',$student+['id'=>$id,'name'=>'','role'=>'student','owner_id'=>$actor['id'],
                        'status'=>'active','metadata'=>'{}','test'=>$this->test?1:0,'created_at'=>now()]);
                } catch(PDOException $e) {
                    if(in_array((int)($e->errorInfo[1]??0),[19,1062,2067],true))throw new Fault($duplicate,409);
                    throw $e;
                }
                Workspace::grant($this->db,$workspace,$id);
                $this->db->audit($actor['id'],'participant',$id,'',['login'=>$student['login'],'role'=>'student','status'=>'active','created'=>true,'workspace_id'=>$workspace]);
            }
            return ['count'=>count($students)];
        });
    }

    private static function profileName(mixed $value): string {
        $name=is_string($value)?trim($value):'';
        demand($name!==''&&mb_strlen($name)<=200,'Ad soyad gerekli (en fazla 200 karakter)');
        return $name;
    }

    public function updateProfile(array $user,array $input): array {
        $name=self::profileName($input['name']??null);
        $this->db->tx(function()use($user,$name){
            $this->db->q('UPDATE users SET name=? WHERE id=?',[$name,$user['id']]);
            $this->db->audit($user['id'],'profile_updated',$user['id']);
        });
        return ['name'=>$name];
    }

    public function changePassword(array $user,array $input): string {
        $current=$input['current_password']??null;
        $password=self::password($input['password']??null);
        return $this->db->tx(function()use($user,$current,$password){
            $fresh=$this->db->one('SELECT password,status FROM users WHERE id=?'.$this->lock(),[$user['id']]);
            demand($fresh&&$fresh['status']==='active'&&$fresh['password']===$user['password'],'Oturum yenilenmeli. Tekrar giriş yapın.',401);
            demand(is_string($current)&&password_verify($current,$fresh['password']),'Mevcut parola hatalı');
            $hash=password_hash($password,PASSWORD_DEFAULT);
            $this->db->q('UPDATE users SET password=? WHERE id=?',[$hash,$user['id']]);
            $this->db->q('DELETE FROM password_resets WHERE user_id=?',[$user['id']]);
            $this->db->audit($user['id'],'password_changed',$user['id']);
            return $hash;
        });
    }

    public function requestReset(array $input): array {
        $login=self::loginName($input['login']??null);
        $this->db->tx(function()use($login){
            $user=$this->db->one('SELECT id,role,status FROM users WHERE login=?'.$this->lock(),[$login]);
            if(!self::canRecover($user))return;
            if(!$this->db->one('SELECT user_id FROM password_resets WHERE user_id=?',[$user['id']]))
                $this->db->insert('password_resets',['user_id'=>$user['id'],'requested_at'=>now()]);
        });
        return ['ok'=>true];
    }

    public function resetRequests(array $actor): array {
        Auth::staff($actor);
        $global=Auth::isSuperAdmin($actor);
        $scope=$global?"u.role IN ('student','researcher')":"u.role='student' AND u.owner_id=? AND EXISTS (SELECT 1 FROM workspace_memberships a JOIN workspace_memberships b ON b.workspace_id=a.workspace_id WHERE a.user_id=u.id AND b.user_id=u.owner_id)";
        $rows=$this->db->all("SELECT u.id,u.login,u.name,r.requested_at,r.expires_at FROM password_resets r JOIN users u ON u.id=r.user_id WHERE u.status='active' AND ".$scope.' ORDER BY r.requested_at DESC',$global?[]:[$actor['id']]);
        foreach($rows as &$row)$row['expires_at']=$row['expires_at']===null?null:gmdate('c',(int)$row['expires_at']);
        return $rows;
    }

    public function issueReset(array $actor,array $input): array {
        Auth::staff($actor);
        $id=$input['user_id']??'';
        demand(is_string($id)&&$id!=='','Hesap gerekli');
        return $this->db->tx(function()use($actor,$id){
            $user=$this->db->one('SELECT * FROM users WHERE id=?'.$this->lock(),[$id]);
            $allowed=self::canRecover($user)&&(Auth::isSuperAdmin($actor)||($user['role']==='student'&&$user['owner_id']===$actor['id']&&$this->db->one('SELECT a.workspace_id FROM workspace_memberships a JOIN workspace_memberships b ON b.workspace_id=a.workspace_id WHERE a.user_id=? AND b.user_id=?',[$actor['id'],$id])));
            demand($allowed,'Bu hesap için yenileme kodu oluşturamazsınız',403);
            demand((bool)$this->db->one('SELECT user_id FROM password_resets WHERE user_id=?',[$id]),'Hesap için bekleyen parola talebi yok',404);
            $code=uid();$expires=time()+30*60;
            $this->db->q('UPDATE password_resets SET token_hash=?,password_stamp=?,expires_at=? WHERE user_id=?',
                [hash('sha256',$code),Auth::passwordStamp($user['password']),$expires,$id]);
            $this->db->audit($actor['id'],'password_reset_issued',$id);
            return ['code'=>$code,'expires_at'=>gmdate('c',$expires)];
        });
    }

    public function resetPassword(array $input): array {
        $login=self::loginName($input['login']??null);
        $hash=self::codeHash($input['code']??null);
        $password=self::password($input['password']??null);
        return $this->db->tx(function()use($login,$hash,$password){
            $user=$this->db->one('SELECT * FROM users WHERE login=?'.$this->lock(),[$login]);
            demand(self::canRecover($user),'Kod geçersiz veya süresi dolmuş');
            $reset=$this->db->one('SELECT * FROM password_resets WHERE user_id=?',[$user['id']]);
            demand(self::validCode($reset,$hash)&&hash_equals(Auth::passwordStamp($user['password']),$reset['password_stamp']??''),'Kod geçersiz veya süresi dolmuş');
            $this->db->q('UPDATE users SET password=? WHERE id=?',[password_hash($password,PASSWORD_DEFAULT),$user['id']]);
            $this->db->q('DELETE FROM password_resets WHERE user_id=?',[$user['id']]);
            $this->db->audit($user['id'],'password_reset_completed',$user['id']);
            return ['ok'=>true,'login'=>$user['login']];
        });
    }
}
