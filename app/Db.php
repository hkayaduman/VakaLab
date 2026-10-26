<?php
declare(strict_types=1);
final class Db {
    public PDO $pdo;
    public function __construct(array $c) {
        $this->pdo = new PDO($c['dsn'],$c['db_user']??null,$c['db_password']??null,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
        if ($this->sqlite()) $this->pdo->exec('PRAGMA foreign_keys=ON; PRAGMA busy_timeout=5000;');
        // Scoring can make several provider calls before the next database query.
        // Keep this connection alive without changing the hosting server's global limit.
        else $this->pdo->exec('SET SESSION wait_timeout = 600');
    }
    public function sqlite(): bool { return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME)==='sqlite'; }
    public function q(string $sql,array $p=[]): PDOStatement { $s=$this->pdo->prepare($sql);$s->execute($p);return $s; }
    public function one(string $sql,array $p=[]): ?array { return $this->q($sql,$p)->fetch()?:null; }
    public function all(string $sql,array $p=[]): array { return $this->q($sql,$p)->fetchAll(); }
    public function insert(string $table,array $data): void {
        $keys=array_keys($data);$this->q('INSERT INTO '.$table.' ('.implode(',',$keys).') VALUES ('.implode(',',array_fill(0,count($keys),'?')).')',array_values($data));
    }
    public function tx(callable $fn): mixed {
        if ($this->sqlite()) $this->pdo->exec('BEGIN IMMEDIATE'); else $this->pdo->beginTransaction();
        try {$r=$fn(); if ($this->sqlite()) $this->pdo->exec('COMMIT');else $this->pdo->commit();return $r;}
        catch(Throwable $e){if($this->sqlite())$this->pdo->exec('ROLLBACK');elseif($this->pdo->inTransaction())$this->pdo->rollBack();throw $e;}
    }
    public function lock(string $id): array {
        $a=$this->one('SELECT * FROM applications WHERE id=?'.($this->sqlite()?'':' FOR UPDATE'),[$id]);demand((bool)$a,'Uygulama bulunamadı',404);return $a;
    }
    public function audit(string $actor,string $action,string $target,string $reason='',array $detail=[]): void {
        $this->insert('audit',['id'=>uid(),'actor'=>$actor,'action'=>$action,'target'=>$target,'reason'=>$reason,'detail'=>js($detail),'created_at'=>now()]);
    }
}
