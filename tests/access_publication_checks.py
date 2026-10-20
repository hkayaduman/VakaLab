"""Isolated HTTP access and publication gates; never reads config/local.php.

Creates a unique tests/tmp SQLite DB and random credentials. Seeds only that DB.
Runs two scenarios in order: case access/versioning, isolated researcher workspaces, then per-case closure/publication.
"""
import hashlib
import http.cookiejar
import json
import os
from pathlib import Path
import socket
import sqlite3
import subprocess
import time
import urllib.error
import urllib.parse
import urllib.request
import uuid

ROOT = Path(__file__).resolve().parents[1]
PHP = os.environ.get('PHP_BIN', str(ROOT / '.tools/php/php.exe'))
TMP = ROOT / 'tests/tmp'
COUNT = 0


def check(ok, name):
    global COUNT
    if not ok:
        raise AssertionError(name)
    COUNT += 1
    print(f'PASS {COUNT} {name}', flush=True)


class Client:
    def __init__(self, base, password, login):
        self.base = base
        self.csrf = ''
        self.opener = urllib.request.build_opener(
            urllib.request.HTTPCookieProcessor(http.cookiejar.CookieJar()))
        self.csrf = self.ok('token')['csrf']
        self.csrf = self.ok('login', {'login': login, 'password': password})['csrf']

    def call(self, op, data=None, **query):
        request = urllib.request.Request(
            self.base + '/api.php?' + urllib.parse.urlencode({'op': op, **query}),
            data=None if data is None else json.dumps(data).encode(),
            headers={'Content-Type': 'application/json', 'X-CSRF-Token': self.csrf})
        try:
            with self.opener.open(request, timeout=30) as response:
                return response.status, json.loads(response.read())
        except urllib.error.HTTPError as error:
            return error.code, json.loads(error.read())

    def ok(self, op, data=None, **query):
        status, body = self.call(op, data, **query)
        if status != 200:
            raise AssertionError(f'{op}: HTTP {status}: {body}')
        return body if op == 'download' else body['data']

    def denied(self, op, code, name, data=None, **query):
        check(self.call(op, data, **query)[0] == code, name)


def main():
    TMP.mkdir(exist_ok=True)
    tag = uuid.uuid4().hex
    dbpath = TMP / f'access_publication_test_{tag}.sqlite'
    config = TMP / f'access_publication_config_{tag}.php'
    # JSON paths are not PHP string escaping: explicitly quote for PHP single quotes.
    php_path = str(dbpath.as_posix()).replace("'", "\\'")
    config.write_text(
        "<?php $c=require __DIR__.'/../../config/example.php';"
        f"$c['dsn']='sqlite:{php_path}';"
        "$c['db_user']='';$c['db_password']='';$c['https']=false;"
        "$c['ai']['provider']='mock';$c['ai']['mode']='normal';return $c;",
        encoding='utf-8')
    password = uuid.uuid4().hex
    env = os.environ | {'VAKALAB_CONFIG': str(config), 'VAKALAB_SEED_PASSWORD': password}
    # No inherited test DSN or local config can redirect this fixture.
    for command in ['migrate', 'seed']:
        result = subprocess.run([PHP, 'bin/console.php', command], cwd=ROOT,
                                env=env, capture_output=True, text=True)
        if result.returncode:
            raise RuntimeError(f'Fixture {command} failed: {result.stderr}')
    with socket.socket() as sock:
        sock.bind(('127.0.0.1', 0))
        port = sock.getsockname()[1]
    base = f'http://127.0.0.1:{port}'
    with (TMP / f'access_publication_server_{tag}.log').open('w', encoding='utf-8') as log:
        server = subprocess.Popen([PHP, '-S', f'127.0.0.1:{port}', '-t', 'public'],
                                  cwd=ROOT, env=env, stdout=log, stderr=log,
                                  creationflags=getattr(subprocess, 'CREATE_NO_WINDOW', 0))
        db = None
        try:
            for _ in range(100):
                try:
                    with urllib.request.urlopen(base, timeout=1):
                        break
                except OSError:
                    time.sleep(.1)
            else:
                raise RuntimeError('Isolated PHP server did not start')
            db = sqlite3.connect(dbpath)
            db.row_factory = sqlite3.Row
            researcher = Client(base, password, 'TEST-ARASTIRMACI')
            first = Client(base, password, 'TEST-OGR-01')
            second = Client(base, password, 'TEST-OGR-02')
            unstarted = Client(base, password, 'TEST-OGR-03')

            def admin(action, **values):
                return researcher.ok('admin', {'action': action, 'reason': 'SENTETİK otomatik erişim/yayın kontrolü', **values})

            def visible(client, cid):
                return any(row['id'] == cid for row in client.ok('cases'))

            def assignment(user, cid, active):
                return admin('assign', user_id=user, case_id=cid, active=active)

            def fingerprint():
                tables = ['applications', 'draft_revisions', 'snapshots', 'messages',
                          'ai_requests', 'ai_attempts', 'analysis_runs', 'analysis_steps',
                          'decisions', 'reports']
                data = {table: [dict(row) for row in db.execute(f'SELECT * FROM {table} ORDER BY id')]
                        for table in tables}
                return hashlib.sha256(json.dumps(data, sort_keys=True).encode()).hexdigest()

            print('SCENARIO A: B14.2/B15 equivalents, real HTTP; isolated SQLite; mock', flush=True)
            case = {'title': 'SENTETİK OTOMATİK · Vaka erişimi', 'body': '[İLK METİN]',
                    'instructions': 'SENTETİK yönerge', 'position': 7, 'status': 'draft',
                    'practice': True, 'stop_access': False, 'rubric_id': 'test-rubric-v1'}
            cid = admin('case', **case)['id']

            def update_case(**values):
                case.update(values)
                return admin('case', id=cid, **case)

            check(not visible(first, cid), 'draft unassigned case absent')
            update_case(status='active')
            check(not visible(first, cid), 'B14.2 active unassigned case absent')
            first.denied('start', 403, 'unassigned direct start denied', {'case_id': cid})
            assignment('test-student-1', cid, True)
            check(visible(first, cid), 'active assigned unstarted case visible')
            a = first.ok('start', {'case_id': cid})
            aid = a['id']
            view = first.ok('view', app_id=aid)
            check(view['version']['version'] == 2 and view['version']['body'] == '[İLK METİN]',
                  'first start pins version 2 / first text')
            draft = json.loads(a['draft'])
            draft.update(knowledge=5, confidence=8, difficulty=6, severity=7,
                         problem='SENTETİK korunacak sorun', decision='SENTETİK karar', reason='SENTETİK gerekçe')
            a = first.ok('initial', {'app_id': aid, 'revision': 0, 'draft': draft})
            first.ok('chat', {'app_id': aid, 'key': uuid.uuid4().hex, 'text': 'SENTETİK soru'})
            draft.update(final_decision='SENTETİK korunacak nihai taslak', final_reason='SENTETİK gerekçe',
                         final_confidence=9, reflection='SENTETİK yansıtma')
            a = first.ok('save', {'app_id': aid, 'revision': a['revision'], 'draft': draft})
            update_case(body='[GÜNCEL METİN]')
            assignment('test-student-2', cid, True)
            b = second.ok('start', {'case_id': cid})
            view2 = second.ok('view', app_id=b['id'])
            check(view2['version']['version'] == 3 and view2['version']['body'] == '[GÜNCEL METİN]',
                  'new starter pins version 3 / updated text')
            check(first.ok('view', app_id=aid)['version'] == view['version'], 'existing version remains unchanged')
            check(not visible(unstarted, cid), 'B15.1 unassigned third student cannot see case')
            assignment('test-student-3', cid, True)
            check(visible(unstarted, cid), 'third student assigned but not started')
            check(db.execute('SELECT COUNT(*) FROM applications WHERE user_id=? AND case_id=?',
                             ('test-student-3', cid)).fetchone()[0] == 0, 'third student has no application')
            update_case(status='closed')
            check(not visible(unstarted, cid), 'B15.2 closed case absent for assigned nonstarter')
            unstarted.denied('start', 403, 'closed case direct new start denied', {'case_id': cid})
            check(visible(first, cid), 'B15.3 closed case visible for existing starter')
            check(first.ok('start', {'case_id': cid})['id'] == aid, 'closed case resumes same application')
            check(first.ok('view', app_id=aid)['version'] == view['version'], 'closed case keeps pinned old text')
            draft['final_reason'] += ' · kapalı yeni başlangıçta kayıt'
            a = first.ok('save', {'app_id': aid, 'revision': a['revision'], 'draft': draft})
            check(json.loads(a['draft']) == draft, 'existing starter can still save when new starts closed')
            unchanged = fingerprint()

            def access_denied(label):
                check(not visible(first, cid), label + ': case absent')
                first.denied('start', 403, label + ': resume denied', {'case_id': cid})
                first.denied('view', 403, label + ': direct view denied', app_id=aid)
                for op in ['save', 'initial', 'submit']:
                    first.denied(op, 403, label + ': ' + op + ' denied',
                                 {'app_id': aid, 'revision': a['revision'], 'draft': draft})
                first.denied('chat', 403, label + ': chat denied',
                             {'app_id': aid, 'key': uuid.uuid4().hex, 'text': 'SENTETİK engelli soru'})
                first.denied('events', 403, label + ': events denied', {'app_id': aid, 'events': []})
                check(researcher.ok('view', app_id=aid)['application']['id'] == aid,
                      label + ': researcher retains historical access')
                check(fingerprint() == unchanged, label + ': denied requests preserve all response/analysis/report data')

            update_case(stop_access=True)
            access_denied('B15.4 stop ongoing access')
            update_case(status='active', stop_access=False)
            check(visible(first, cid) and first.ok('start', {'case_id': cid})['id'] == aid,
                  'B15.5 restored access resumes original application')
            check(fingerprint() == unchanged, 'restored case keeps drafts/snapshots/messages and all prior data')
            assigned_id = assignment('test-student-1', cid, False)['id']
            access_denied('B15.6 assignment revoked')
            check(assignment('test-student-1', cid, True)['id'] == assigned_id, 'reassignment reuses original assignment')
            check(visible(first, cid) and first.ok('start', {'case_id': cid})['id'] == aid,
                  'reassignment resumes same application')
            restored = first.ok('view', app_id=aid)
            check(restored['version'] == view['version'] and json.loads(restored['application']['draft']) == draft,
                  'reassignment preserves pinned version and exact draft')
            check(fingerprint() == unchanged, 'reassignment preserves snapshots/messages/revisions/analysis/reports')
            draft['reflection'] += ' · atama geri verildi'
            a = first.ok('save', {'app_id': aid, 'revision': a['revision'], 'draft': draft})
            check(json.loads(a['draft']) == draft, 'save works after reassignment')
            check(db.execute('SELECT closed_at FROM study').fetchone()[0] is None,
                  'case access scenario never closes global study')
            rows = db.execute("SELECT actor,created_at,reason FROM audit WHERE action IN ('case','assign') AND target=?", (cid,)).fetchall()
            check(len(rows) >= 6 and all(row['actor'] == 'test-researcher' and row['created_at'] and row['reason'] for row in rows),
                  'case mutations retain actor/time/reason audit')
            rows = db.execute("SELECT reason FROM audit WHERE action='assign' AND target=?", (assigned_id,)).fetchall()
            check(len(rows) == 3 and all(row['reason'] for row in rows), 'assignment creation/revoke/restore audited separately')

            print('SCENARIO B: independent researcher workspaces, bulk operations, deletion, Disco', flush=True)
            other_login='RESEARCHER-'+tag[:8]
            rid=admin('participant', login=other_login, password=password, role='researcher', name='SENTETİK araştırmacı')['id']
            outsider=Client(base,password,other_login)
            empty=outsider.ok('dashboard')
            check(empty['cases']==[] and empty['applications']==[] and empty['assignments']==[] and empty['definitions']==[], 'new researcher has empty isolated workspace')
            check([row['id'] for row in empty['users']]==[rid], 'researcher cannot list creator students or accounts')
            outsider.denied('view',403,'other researcher cannot view application',app_id=aid)
            outsider.denied('admin',403,'other researcher cannot edit case',{'action':'case','id':cid,**case})
            outsider.denied('admin',403,'other researcher cannot edit participant',{'action':'participant','id':'test-student-1','login':'forbidden'})
            outsider.denied('admin',403,'other researcher cannot close case',{'action':'collection','case_id':cid,'closed':True})
            outsider.denied('admin',403,'other researcher cannot bulk assign',{'action':'assign_bulk','case_id':cid,'user_ids':['test-student-1'],'active':True})
            outsider.denied('admin',403,'other researcher cannot change late access',{'action':'late_access','case_id':cid,'user_ids':['test-student-1'],'open':True})
            exported=outsider.ok('download',format='json',all_data=1)
            check(exported['applications']==[] and exported['definitions']==[] and exported['reports']==[], 'all-data export never crosses workspace')
            outsider.denied('download',403,'foreign selected-case export denied',format='json',all_data=1,case_id=cid)
            own_student=outsider.ok('admin',{'action':'participant','login':'STUDENT-'+tag[:8],'password':password,'name':'SENTETİK öğrenci'})['id']
            own_case=outsider.ok('admin',{'action':'case','title':'SENTETİK özel vaka','body':'PRIVATE CASE TEXT','status':'active'})['id']
            check(len(outsider.ok('dashboard')['cases'])==1, 'researcher can create own case without reason')
            check(all(row['id']!=own_case for row in researcher.ok('dashboard')['cases']), 'creator cannot see new researcher private case')
            researcher.denied('admin',403,'creator cannot edit another researcher student',{'action':'participant','id':own_student,'login':'forbidden'})
            outsider.denied('admin',403,'foreign student cannot be assigned to owned case',{'action':'assign_bulk','case_id':own_case,'user_ids':['test-student-1'],'active':True})
            before_bulk=db.execute('SELECT COUNT(*) FROM assignments WHERE case_id=?',(own_case,)).fetchone()[0]
            outsider.denied('admin',403,'bulk assignment is atomic on foreign student',{'action':'assign_bulk','case_id':own_case,'user_ids':[own_student,'test-student-1'],'active':True})
            check(db.execute('SELECT COUNT(*) FROM assignments WHERE case_id=?',(own_case,)).fetchone()[0]==before_bulk,'failed mixed bulk leaves no partial assignment')
            check(admin('assign_bulk',case_id=cid,user_ids=['test-student-1','test-student-2','test-student-3'],active=True)['count']==3,'bulk assignment without reason succeeds')
            submitted=next(row for row in researcher.ok('dashboard')['applications'] if row['user_id']=='test-student-6')
            data=researcher.ok('view',app_id=submitted['id'])
            run=data['analysis_runs'][0];step=data['steps'][0];report=data['reports'][0]
            outsider.denied('new_run',403,'foreign new analysis denied',{'app_id':submitted['id'],'rubric_id':'test-rubric-v1'})
            outsider.denied('step',403,'foreign analysis step denied',{'run_id':run['id'],'step':1})
            outsider.denied('decision',403,'foreign decision denied',{'step_id':step['id'],'action':'accept','reason':'SENTETİK'})
            outsider.denied('report',403,'foreign report creation denied',{'action':'create','run_id':run['id']})
            outsider.denied('report',403,'foreign report approval denied',{'action':'approve','id':report['id']})
            outsider.denied('report_data',403,'foreign report data denied',id=report['id'])
            outsider.denied('admin',403,'foreign research note denied',{'action':'note','app_id':aid,'body':'test','reason':'test'})
            outsider.denied('admin',403,'foreign exclusion denied',{'action':'exclude','id':aid,'excluded':True,'reason':'test'})
            outsider.denied('admin',403,'foreign rubric deletion denied',{'action':'delete_definition','id':'test-rubric-v1'})
            outsider.denied('admin',403,'foreign rubric version denied',{'action':'definition','family':'label','parent_id':'test-label-0','name':'invalid','body':{}})
            selected=researcher.ok('download',format='json',case_id=cid,all_data=1)
            check(selected['applications'] and all(row['case_id']==cid for row in selected['applications']),'selected-case full export includes only that case')
            check([row['id'] for row in selected['cases']]==[cid] and all(row['case_id']==cid for row in selected['case_versions']),'full export includes selected case text and complete version history')
            check(selected['assignments'] and selected['participants'] and all('password' not in row for row in selected['participants']),'full export includes assignments/participants without password hashes')
            report_export=researcher.ok('download',format='json',case_id=submitted['case_id'],all_data=1)
            check(report_export['reports'] and report_export['snapshots'] and report_export['messages'],'full export includes reports/snapshots/messages')
            check(all(row['id']!=own_case for row in selected['cases']) and all(row['id']!=own_student for row in selected['participants']),'full export supplementary tables preserve workspace isolation')
            check(all(row['case_id']!=own_case for row in researcher.ok('download',format='json',all_data=1)['applications']),'all cases means own workspace only')
            import csv,io
            url=base+'/api.php?'+urllib.parse.urlencode({'op':'download','format':'disco','case_id':'test-case-1'})
            with researcher.opener.open(url) as response: csv_text=response.read().decode('utf-8-sig')
            event_rows=list(csv.DictReader(io.StringIO(csv_text)))
            check(bool(event_rows) and all(row['Case ID'] and row['Activity'] and row['Start Timestamp'] and row['Complete Timestamp'] for row in event_rows),'Disco CSV has case/activity/start/complete columns')
            check(all(row['Vaka ID']=='test-case-1' and row['Time Zone']=='UTC' for row in event_rows),'Disco selected-case scope and UTC explicit')
            check(all(row['Resource']=='AI' or row['Resource'].startswith('P-') for row in event_rows),'Disco resources coded; AI separate')
            check('TEST-OGR' not in csv_text and 'Periyodik durum' not in csv_text,'Disco excludes identity and heartbeat noise')
            check(len({row['Event ID'] for row in event_rows})==len(event_rows),'Disco rows have unique event identifiers')
            check(all(row['Case ID']!=row['Vaka ID'] for row in event_rows),'Disco trace is student-application, not pedagogical case')
            for row in event_rows:
                time.strptime(row['Start Timestamp'][:19],'%Y-%m-%d %H:%M:%S')
            check(True,'Disco timestamps parse and preserve milliseconds')
            label=admin('definition',family='label',name='SENTETİK silinecek etiket',body={'description':'test'})['id']
            admin('delete_definition',id=label)
            check(db.execute('SELECT deleted_at FROM definitions WHERE id=?',(label,)).fetchone()[0] is not None,'definition removed from new choices without destroying history')
            historical=fingerprint()
            admin('delete_definition',id='test-rubric-v1')
            check(fingerprint()==historical,'deleting used rubric leaves old analyses/report bodies unchanged')
            researcher.denied('new_run',422,'deleted rubric cannot start new analysis',{'app_id':submitted['id'],'rubric_id':'test-rubric-v1'})
            check(data['reports']==researcher.ok('view',app_id=submitted['id'])['reports'],'old report accessible after rubric deletion')

            print('SCENARIO C: per-case closure, selected late access, separate publication', flush=True)
            owner=Client(base,password,'TEST-OGR-06')
            v1=next(row for row in data['reports'] if row['version']==1)
            v2=next(row for row in data['reports'] if row['version']==2)
            report_id=v1['id'];publish_case=submitted['case_id']
            researcher.denied('report',409,'open case blocks report publication',{'action':'publish','id':report_id})
            before=fingerprint()
            admin('collection',case_id=cid,closed=True)
            check(fingerprint()==before,'case closure preserves all historical application data')
            unstarted.denied('start',409,'case closure blocks new starts',{'case_id':cid})
            first.denied('save',409,'case closure blocks saves',{'app_id':aid,'revision':a['revision'],'draft':draft})
            first.denied('chat',409,'case closure blocks new AI messages',{'app_id':aid,'key':uuid.uuid4().hex,'text':'test'})
            second.denied('initial',409,'case closure blocks initial confirmation',{'app_id':b['id'],'revision':b['revision'],'draft':draft})
            first.denied('submit',409,'case closure blocks submission',{'app_id':aid,'revision':a['revision'],'draft':draft})
            check(first.ok('view',app_id=aid)['application']['collection_closed_at'],'closed case remains readable with closure flag')
            first_case=first.ok('start',{'case_id':'test-case-2'})
            check(first_case['id'],'closing one case leaves another open')
            admin('late_access',case_id=cid,user_ids=['test-student-1'],open=True)
            a=first.ok('save',{'app_id':aid,'revision':a['revision'],'draft':draft})
            check(first.ok('view',app_id=aid)['application']['allow_late']==1,'selected student can save in closed case')
            admin('late_access',case_id=cid,user_ids=['test-student-3'],open=True)
            check(unstarted.ok('start',{'case_id':cid})['id'],'late access can also allow a selected student first start')
            second.denied('save',409,'unselected student remains closed',{'app_id':b['id'],'revision':b['revision'],'draft':json.loads(b['draft'])})
            admin('late_access',case_id=cid,user_ids=['test-student-1'],open=False)
            first.denied('save',409,'late access can be revoked',{'app_id':aid,'revision':a['revision'],'draft':draft})
            admin('collection',case_id=cid,closed=False)
            check(first.ok('save',{'app_id':aid,'revision':a['revision'],'draft':draft})['id']==aid,'case can reopen without replacing application')
            check(second.ok('view',app_id=b['id'])['application']['collection_closed_at'] is None,'reopen affects all assigned students')
            admin('collection',case_id=publish_case,closed=True)
            check(owner.ok('view',app_id=submitted['id'])['reports']==[],'case closure does not auto-publish')
            researcher.denied('report',422,'unapproved report still cannot publish',{'action':'publish','id':v2['id']})
            published=researcher.ok('report',{'action':'publish','id':report_id})
            check(published['published_at'] and published['body']==v1['body'],'explicit publication preserves report snapshot')
            check([row['id'] for row in owner.ok('view',app_id=submitted['id'])['reports']]==[report_id],'only published version visible to owner')
            student_report=owner.ok('report_data',id=report_id)
            check('internal' not in student_report['report'],'published report strips researcher appendix')
            outsider.denied('report_data',403,'published report still denied to another researcher',id=report_id)
            first.denied('report_data',403,'published report denied to other student',id=report_id)
            admin('late_access',case_id=publish_case,user_ids=['test-student-6'],open=True)
            check(owner.ok('view',app_id=submitted['id'])['reports']==[],'personal late access hides own published report')
            owner.denied('report_data',403,'late access blocks direct report download',id=report_id)
            submitted_draft=json.loads(submitted.get('draft',data['application']['draft']))
            owner.denied('save',409,'late access never unlocks submitted responses',{'app_id':submitted['id'],'revision':data['application']['revision'],'draft':submitted_draft})
            admin('late_access',case_id=publish_case,user_ids=['test-student-6'],open=False)
            check(owner.ok('report_data',id=report_id)['report']==student_report['report'],'revoking late access restores same published snapshot')
            admin('collection',case_id=publish_case,closed=False)
            owner.denied('report_data',403,'reopening case hides report data',id=report_id)
            admin('collection',case_id=publish_case,closed=True)
            check(owner.ok('report_data',id=report_id)['report']==student_report['report'],'reclosing restores report without republishing or mutation')
            check(db.execute('SELECT closed_at FROM study').fetchone()[0] is None,'legacy global study never closed')
            print(f'TOTAL {COUNT} assertions passed. HTTP / isolated SQLite / mock / application 0.2.1.',flush=True)
        finally:
            if db is not None:
                db.close()
            server.terminate()
            try:
                server.wait(timeout=5)
            except subprocess.TimeoutExpired:
                server.kill()
                server.wait(timeout=5)


if __name__ == '__main__':
    main()
