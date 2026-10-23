<?php
declare(strict_types=1);
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
require __DIR__.'/../app/bootstrap.php';
try{
    $cmd=$argv[1]??'help';
    if($cmd==='check'){
        echo 'PHP '.PHP_VERSION.PHP_EOL;$missing=[];foreach(['pdo','pdo_mysql','mbstring','curl','zip','gd','dom','xml','xmlreader','xmlwriter','fileinfo'] as $ext){echo "$ext: ".(extension_loaded($ext)?'OK':'MISSING').PHP_EOL;if(!extension_loaded($ext))$missing[]=$ext;}
        $patchedPhp=(PHP_VERSION_ID>=80336&&PHP_VERSION_ID<80400)||(PHP_VERSION_ID>=80427&&PHP_VERSION_ID<80500)||PHP_VERSION_ID>=80512;
        demand($patchedPhp&&!$missing,'PHP güvenlik güncellemesi gerekli: 8.3.36+, 8.4.27+ veya 8.5.12+ ve gerekli eklentiler',503);demand(is_writable(__DIR__.'/../storage'),'storage yazılabilir değil');echo 'Vendor: '.(class_exists('Mpdf\\Mpdf')?'OK':'MISSING').PHP_EOL;exit;
    }
    if($cmd==='researcher-sql'){
        $login=$argv[2]??'';$password=getenv('VAKALAB_ADMIN_PASSWORD')?:'';demand(preg_match('/^[A-Za-z0-9@._-]{3,100}$/',$login)===1&&strlen($password)>=14,'ASCII hesap adı ve VAKALAB_ADMIN_PASSWORD (14+ karakter) gerekli');
        $id=uid();$hash=password_hash($password,PASSWORD_DEFAULT);$time=now();echo "INSERT INTO users (id,login,name,password,role,status,metadata,test,created_at) VALUES ('$id','$login','Researcher','$hash','researcher','active','{}',0,'$time');\n";exit;
    }
    $c=cfg();$db=new Db($c);
    if($cmd==='migrate'){
        Migration::run($db);echo "Migration 001/002 hazır; mevcut kayıtlar korunur.\n";
    }elseif($cmd==='seed'){
        demand($c['env']==='test','Seed yalnız test ortamında çalışır');demand(str_contains($c['dsn'],'_test')||str_starts_with($c['dsn'],'sqlite:'),'Test DB adı _test içermeli');
        $password=getenv('VAKALAB_SEED_PASSWORD')?:'';demand(strlen($password)>=12,'VAKALAB_SEED_PASSWORD en az 12 karakter olmalı');
        require __DIR__.'/../tests/Seed.php';Seed::run($db,$c,$password);echo "SENTETİK seed hazır; mevcut seed kayıtları korunur. Hesap haritası: TEST_DATA.md\n";
    }elseif($cmd==='researcher'){
        $login=$argv[2]??'';$password=getenv('VAKALAB_ADMIN_PASSWORD')?:'';demand($login!==''&&strlen($password)>=14,'Hesap adı ve VAKALAB_ADMIN_PASSWORD (14+ karakter) gerekli');demand(!$db->one('SELECT id FROM users WHERE login=?',[$login]),'Hesap zaten var');
        $db->insert('users',['id'=>uid(),'login'=>$login,'name'=>'Araştırmacı','password'=>password_hash($password,PASSWORD_DEFAULT),'role'=>'researcher','status'=>'active','metadata'=>'{}','test'=>0,'created_at'=>now()]);echo "Araştırmacı oluşturuldu.\n";
    }else echo "php bin/console.php check | migrate | seed | researcher LOGIN\n";
}catch(Throwable $e){fwrite(STDERR,$e instanceof Fault?$e->getMessage().PHP_EOL:'İşlem başarısız: '.get_class($e).PHP_EOL);exit(1);}
