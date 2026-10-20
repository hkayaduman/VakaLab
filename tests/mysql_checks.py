"""Optional portable MariaDB verification; only creates unique local test databases."""
import os, pathlib, secrets, socket, subprocess, time
root=pathlib.Path(__file__).resolve().parents[1]
home=pathlib.Path(os.environ['MARIADB_HOME']);php=os.environ.get('PHP_BIN',str(root/'.tools/php/php.exe'))
tag=secrets.token_hex(6);data=root/'.tools'/f'mariadb_data_test_{tag}';data.mkdir()
password=secrets.token_hex(24)
s=socket.socket();s.bind(('127.0.0.1',0));port=s.getsockname()[1];s.close()
subprocess.run([str(home/'bin/mariadb-install-db.exe'),f'--datadir={data}',f'--password={password}',f'--port={port}','--silent'],check=True,capture_output=True)
client_config=root/'.tools'/f'mariadb_client_test_{tag}.cnf';client_config.write_text(f'[client]\nuser=root\npassword={password}\nhost=127.0.0.1\nport={port}\n',encoding='utf-8')
log=open(data/'server.log','w')
server=subprocess.Popen([str(home/'bin/mariadbd.exe'),'--no-defaults',f'--basedir={home}',f'--datadir={data}',f'--port={port}','--bind-address=127.0.0.1','--console','--skip-log-bin','--innodb-buffer-pool-size=64M'],stdout=log,stderr=log,creationflags=getattr(subprocess,'CREATE_NO_WINDOW',0))
client=[str(home/'bin/mariadb.exe'),f'--defaults-extra-file={client_config}','--skip-ssl'] # disposable loopback fixture; no production connection
try:
    for _ in range(100):
        probe=subprocess.run(client+['-e','SELECT VERSION()'],capture_output=True,text=True)
        if probe.returncode==0:print(probe.stdout.strip(),flush=True);break
        time.sleep(.2)
    else:raise RuntimeError('MariaDB probe failed: '+probe.stderr.strip())
    name='vakalab_test_'+tag
    subprocess.run(client+['-e',f'CREATE DATABASE {name} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'],check=True,capture_output=True)
    env=os.environ|{'VAKALAB_TEST_DSN':f'mysql:host=127.0.0.1;port={port};dbname={name};charset=utf8mb4','VAKALAB_TEST_USER':'root','VAKALAB_TEST_PASSWORD':password}
    subprocess.run([php,'tests/run.php'],cwd=root,env=env,check=True)
    dump=data/'backup.sql'
    with open(dump,'wb') as out:subprocess.run([str(home/'bin/mariadb-dump.exe'),f'--defaults-extra-file={client_config}','--skip-ssl','--single-transaction',name],stdout=out,check=True)
    restore=name+'_restore';subprocess.run(client+['-e',f'CREATE DATABASE {restore} CHARACTER SET utf8mb4'],check=True,capture_output=True)
    with open(dump,'rb') as source:subprocess.run(client+[restore],stdin=source,check=True,capture_output=True)
    for table in ['users','applications','messages','snapshots','reports','analysis_steps','decisions','events']:
        query=f'SELECT (SELECT COUNT(*) FROM {name}.{table})=(SELECT COUNT(*) FROM {restore}.{table})'
        res=subprocess.run(client+['-N','-e',query],capture_output=True,text=True,check=True);assert res.stdout.strip()=='1',table
    print('BACKUP RESTORE: 8 table counts match in separate restored database.',flush=True)
    print('MYSQL schema, seed, workflow, FK, formats: passed on isolated MariaDB.',flush=True)
finally:
    subprocess.run([str(home/'bin/mariadb-admin.exe'),f'--defaults-extra-file={client_config}','--skip-ssl','shutdown'],capture_output=True)
    try:server.wait(timeout=10)
    except subprocess.TimeoutExpired:server.terminate()
    log.close()
