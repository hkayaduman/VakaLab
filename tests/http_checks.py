"""Real HTTP/session/concurrency checks. Only creates unique disposable test DBs."""
import concurrent.futures, http.cookiejar, io, json, os, pathlib, socket, sqlite3, subprocess, sys, time, urllib.request, urllib.error, uuid, zipfile

ROOT=pathlib.Path(__file__).resolve().parents[1]
PHP=os.environ.get('PHP_BIN',str(ROOT/'.tools/php/php.exe'))
TMP=ROOT/'tests/tmp'; TMP.mkdir(exist_ok=True)
tag=uuid.uuid4().hex
dbpath=TMP/f'http_test_{tag}.sqlite'; config=TMP/f'http_config_{tag}.php'
password=uuid.uuid4().hex
def config_write(mode):
    config.write_text("<?php $c=require __DIR__.'/../../config/example.php'; $c['dsn']='sqlite:"+dbpath.as_posix()+"'; $c['db_user']=''; $c['db_password']=''; $c['https']=false; $c['ai']['mode']='"+mode+"'; return $c;",encoding='utf-8')
config_write('normal'); env=os.environ|{'VAKALAB_CONFIG':str(config),'VAKALAB_SEED_PASSWORD':password}
for command in ['migrate','seed','seed']:
    subprocess.run([PHP,'bin/console.php',command],cwd=ROOT,env=env,check=True,capture_output=True)
def free_port():
    s=socket.socket();s.bind(('127.0.0.1',0));p=s.getsockname()[1];s.close();return p
ports=[free_port(),free_port()]; servers=[]; count=0
def check(ok,name):
    global count
    assert ok,name
    count+=1;print(f'PASS HTTP {count} {name}',flush=True)
class Client:
    def __init__(self): self.jar=http.cookiejar.CookieJar();self.opener=urllib.request.build_opener(urllib.request.HTTPCookieProcessor(self.jar));self.csrf=''
    def call(self,op,data=None,port=None,csrf=True,**query):
        import urllib.parse
        url=f'http://127.0.0.1:{port or ports[0]}/api.php?'+urllib.parse.urlencode({'op':op}|query)
        headers={'Content-Type':'application/json'}
        if csrf: headers['X-CSRF-Token']=self.csrf
        req=urllib.request.Request(url,data=json.dumps(data).encode() if data is not None else None,headers=headers)
        try:
            res=self.opener.open(req,timeout=30);status=res.status;body=res.read()
        except urllib.error.HTTPError as e:status=e.code;body=e.read()
        try:return status,json.loads(body)
        except json.JSONDecodeError:raise AssertionError(f'Non-JSON HTTP {status}: {body[:500]}')
    def login(self,user):
        self.csrf=self.call('token')[1]['data']['csrf'];status,data=self.call('login',{'login':user,'password':password});assert status==200,data;self.csrf=data['data']['csrf']
    def download(self,**query):
        import urllib.parse
        return self.opener.open(f'http://127.0.0.1:{ports[0]}/api.php?'+urllib.parse.urlencode({'op':'download'}|query),timeout=30).read()
try:
    log=open(TMP/f'http_server_{tag}.log','w',encoding='utf-8')
    for port in ports:servers.append(subprocess.Popen([PHP,'-S',f'127.0.0.1:{port}','-t','public'],cwd=ROOT,env=env,stdout=log,stderr=log,creationflags=getattr(subprocess,'CREATE_NO_WINDOW',0)))
    for port in ports:
        for _ in range(100):
            try:urllib.request.urlopen(f'http://127.0.0.1:{port}/',timeout=1);break
            except OSError:time.sleep(.1)
        else:raise RuntimeError('server did not start')
    student=Client();check(student.call('me')[0]==401,'unauthenticated me denied')
    check(student.call('login',{'login':'x','password':'x'},csrf=False)[0]==403,'CSRF missing denied')
    student.login('TEST-OGR-01');check(student.call('dashboard')[0]==403,'student dashboard forbidden')
    researcher=Client();researcher.login('TEST-ARASTIRMACI');check(researcher.call('dashboard')[0]==200,'researcher dashboard allowed')
    check(researcher.call('admin',{'action':'participant','id':'test-student-1','login':'TEST-OGR-01','name':'Synthetic','status':'active','metadata':{'researcher_notes':'PRIVATE-PARTICIPANT-NOTE','program':'TEST'},'reason':'Private metadata test'})[0]==200,'private participant note saved')
    check('PRIVATE-PARTICIPANT-NOTE' not in json.dumps(student.call('me')[1]),'private participant note excluded from student me')
    student.call('onboarding',{'program':'Updated TEST','consent':'SENTETİK'})
    dashboard=researcher.call('dashboard')[1]['data'];check('PRIVATE-PARTICIPANT-NOTE' in next(x['metadata'] for x in dashboard['users'] if x['id']=='test-student-1'),'student onboarding preserves private researcher note')
    check(student.call('admin',{'action':'close_study','reason':'test','confirm':True})[0]==403,'student cannot close study')
    check(student.call('start',{'case_id':'test-case-5'})[0]==403,'unassigned HTTP denied')
    a=student.call('start',{'case_id':'test-case-1'})[1]['data'];aid=a['id']
    check(student.call('chat',{'app_id':aid,'key':uuid.uuid4().hex,'text':'test'})[0]==409,'HTTP AI initial gate')
    d=json.loads(a['draft']);d.update(knowledge=3,confidence=8,difficulty=5,severity=7,problem='SENTETİK sorun',decision='SENTETİK karar',reason='SENTETİK gerekçe')
    a=student.call('initial',{'app_id':aid,'revision':0,'draft':d})[1]['data'];check(bool(a['initial_at']),'HTTP atomic initial lock')
    other=Client();other.login('TEST-OGR-02');check(other.call('view',app_id=aid)[0]==403,'HTTP IDOR view denied')
    # Two independent PHP workers share database and session files; same browser session.
    config_write('delay')
    pool=concurrent.futures.ThreadPoolExecutor(2);key=uuid.uuid4().hex
    future=pool.submit(student.call,'chat',{'app_id':aid,'key':key,'text':'SENTETİK gecikmeli soru'})
    connection=sqlite3.connect(dbpath)
    for _ in range(100):
        row=connection.execute('select active_request from applications where id=?',(aid,)).fetchone()
        if row[0]:break
        time.sleep(.05)
    else:raise AssertionError('AI reservation not observed')
    check(not future.done(),'delayed provider running')
    d['final_decision']='AI sürerken yazılan SENTETİK metin';t=time.monotonic();status,res=student.call('save',{'app_id':aid,'revision':a['revision'],'draft':d},port=ports[1]);elapsed=time.monotonic()-t
    check(status==200 and elapsed<3 and not future.done(),f'same-session autosave during AI {elapsed:.3f}s')
    a=res['data'];check(student.call('chat',{'app_id':aid,'key':uuid.uuid4().hex,'text':'ikinci'},port=ports[1])[0]==409,'parallel second message rejected')
    check(student.call('submit',{'app_id':aid,'revision':a['revision'],'draft':d},port=ports[1])[0]==409,'parallel submit rejected')
    status,res=future.result();check(status==200 and res['data']['status']=='completed','delayed AI completes')
    check(student.call('chat',{'app_id':aid,'key':key,'text':'same'})[1]['data']['id']==res['data']['id'],'HTTP duplicate key idempotent')
    check(connection.execute('select count(*) from messages where app_id=?',(aid,)).fetchone()[0]==2,'only one user and one assistant message')
    # Failure and explicit retry: one immutable student message, separate attempts.
    config_write('error');badkey=uuid.uuid4().hex;status,res=student.call('chat',{'app_id':aid,'key':badkey,'text':'SENTETİK hata sorusu'});req=res['data'];check(req['status']=='failed','mock provider error recorded')
    config_write('normal');status,res=student.call('chat',{'app_id':aid,'key':badkey,'retry_request':req['id']});check(status==200 and res['data']['status']=='completed','explicit retry successful')
    check(connection.execute('select count(*) from messages where request_id=? and role=?',(req['id'],'user')).fetchone()[0]==1,'retry does not duplicate student message')
    check(connection.execute('select count(*) from ai_attempts where request_id=?',(req['id'],)).fetchone()[0]==2,'attempt history preserved')
    config_write('partial');status,res=student.call('chat',{'app_id':aid,'key':uuid.uuid4().hex,'text':'Kısmi test'});check(res['data']['status']=='partial','partial response explicitly marked')
    # Fencing: researcher closes a timed-out reservation while old worker returns later.
    config_write('delay');late=pool.submit(student.call,'chat',{'app_id':aid,'key':uuid.uuid4().hex,'text':'Geç yanıt'})
    for _ in range(100):
        rid=connection.execute('select active_request from applications where id=?',(aid,)).fetchone()[0]
        if rid:break
        time.sleep(.05)
    connection.execute("update ai_requests set started_at='2020-01-01T00:00:00Z' where id=?",(rid,));connection.commit()
    check(researcher.call('admin',{'action':'resolve_request','id':rid,'reason':'SENTETİK fencing testi'},port=ports[1])[0]==200,'researcher resolves expired reservation')
    late.result();check(connection.execute('select count(*) from messages where request_id=? and role=?',(rid,'assistant')).fetchone()[0]==0,'late fenced reply not committed')
    submitted=connection.execute("select id from applications where user_id='test-student-6'").fetchone()[0]
    report=connection.execute('select id from reports where app_id=? and approved_at is not null',(submitted,)).fetchone()[0]
    s6=Client();s6.login('TEST-OGR-06');check(s6.call('report_data',id=report)[0]==403,'early report denied over HTTP')
    check(student.call('download',report_ids=report,format='json')[0]==403,'foreign report download denied')
    frozen=Client();frozen.csrf=frozen.call('token')[1]['data']['csrf'];check(frozen.call('login',{'login':'TEST-OGR-07','password':password})[0]==401,'frozen account login rejected')
    status,res=student.call('view',app_id=aid);check('steps' not in res['data'] and 'labels' not in res['data'],'student view excludes internal analysis and labels')
    check(student.call('admin',{'action':'delete','id':aid,'reason':'test'})[0]==403,'no destructive student endpoint')
    check(researcher.call('admin',{'action':'delete','id':aid,'reason':'test'})[0]==404,'no permanent delete admin endpoint')
    check(researcher.call('admin',{'action':'close_study','confirm':True,'reason':'SENTETİK beş vaka kapalı; eksikler fixture'})[0]==200,'global close HTTP')
    check(researcher.call('report',{'action':'publish','id':report})[0]==200,'separate HTTP publication')
    check(s6.call('report_data',id=report)[0]==200,'published own report readable')
    pdf=s6.download(report_ids=report,format='pdf');check(pdf.startswith(b'%PDF-'),'student real PDF download over HTTP')
    docx=s6.download(report_ids=report,format='docx');check(docx.startswith(b'PK'),'student real DOCX download over HTTP')
    xml=zipfile.ZipFile(io.BytesIO(docx)).read('word/document.xml').decode();check('Araştırmacı iç eki' not in xml,'student DOCX excludes private appendix')
    internal=researcher.download(report_ids=report,format='docx');xml=zipfile.ZipFile(io.BytesIO(internal)).read('word/document.xml').decode();check('Araştırmacı iç eki' in xml,'researcher DOCX contains private appendix')
    xlsx=researcher.download(format='xlsx',include_test=1,coded=1);check('xl/workbook.xml' in zipfile.ZipFile(io.BytesIO(xlsx)).namelist(),'real XLSX HTTP download')
    check(student.call('save',{'app_id':aid,'revision':a['revision'],'draft':d})[0]==409,'global closed blocks student changes')
    check(student.call('logout',{})[0]==200 and student.call('view',app_id=aid)[0]==401,'logout revokes session')
    connection.close();pool.shutdown();print(f'TOTAL HTTP {count} assertions passed. Two PHP workers; SQLite; mock.',flush=True)
finally:
    for server in servers:
        server.terminate()
        try:server.wait(timeout=5)
        except subprocess.TimeoutExpired:server.kill()
    if 'log' in globals():log.close()
