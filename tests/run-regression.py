"""Run every local regression suite with isolated fixtures; save individual logs.

Live-provider checks are a separate opt-in run because their semantic results vary.
Seed.php and review-editor.cjs are helpers, not standalone test suites.
"""
import datetime, hashlib, json, os, pathlib, re, subprocess, sys, time

if hasattr(sys.stdout,'reconfigure'):sys.stdout.reconfigure(encoding='utf-8',errors='replace')

root=pathlib.Path(__file__).resolve().parents[1]
tag=datetime.datetime.now(datetime.timezone.utc).strftime('%Y%m%dT%H%M%SZ')
out=root/'test-evidence'/'disco-attempts-2026-10-24'/tag
out.mkdir(parents=True)
sources=[p for folder in ['app','public','public/assets','bin','tests'] for p in sorted((root/folder).iterdir()) if p.is_file() and p.suffix in ['.php','.js','.cjs','.py']]
(out/'source-sha256.json').write_text(json.dumps({str(p.relative_to(root)):hashlib.sha256(p.read_bytes()).hexdigest() for p in sources},indent=2),encoding='utf-8')
php=os.environ.get('PHP_BIN',str(root/'.tools/php/php.exe'))
node=os.environ.get('NODE_BIN','node')
env=os.environ.copy()
# Never allow caller flags to turn a local run into paid calls or working-DB tests.
for key in ['VAKALAB_RUN_LIVE_E2E','VAKALAB_RUN_LIVE_PROMPT_TEST','VAKALAB_TEST_DSN','VAKALAB_TEST_USER','VAKALAB_TEST_PASSWORD']:
    env.pop(key,None)
live={'label-catalog-live.php','live-workflow.php','openrouter-prompts.php','scoring-live.php','student-label-overrides-live.php','student-labels-live.php'}
results=[]
def run(name,args,timeout=600):
    start=time.monotonic()
    try:
        p=subprocess.run(args,cwd=root,env=env,capture_output=True,text=True,encoding='utf-8',errors='replace',timeout=timeout,creationflags=getattr(subprocess,'CREATE_NO_WINDOW',0))
        text=p.stdout+p.stderr;code=p.returncode
    except subprocess.TimeoutExpired as e:
        text='TIMEOUT '+str(timeout)+' seconds\n'+str(e.stdout or '')+str(e.stderr or '');code=-1
    except Exception as e:
        text=repr(e);code=-2
    (out/(name+'.txt')).write_text(text,encoding='utf-8')
    row={'suite':name,'exit_code':code,'passed':code==0,'seconds':round(time.monotonic()-start,2),'pass_lines':len(re.findall(r'^PASS\b',text,re.M))}
    results.append(row)
    (out/'results.json').write_text(json.dumps({'suites':results,'live_provider':'not run by local runner','helper_files':['Seed.php','review-editor.cjs']},ensure_ascii=False,indent=2),encoding='utf-8')
    print(('PASS' if code==0 else 'FAIL')+' '+name+' '+str(row['seconds'])+'s',flush=True)
    if code:print(text[-1800:],flush=True)

for file in sorted((root/'tests').glob('*.php')):
    if file.name not in live|{'Seed.php','db-idle-timeout.php'}:run(file.name,[php,str(file)])
for file in sorted((root/'tests').glob('*.cjs')):
    if file.name!='review-editor.cjs':run(file.name,[node,str(file)])
for name in ['http_checks.py','access_publication_checks.py','mysql_checks.py']:
    run(name,[sys.executable,str(root/'tests'/name)])
# All production and test PHP sources are linted, including guarded live scripts.
lint=[]
for folder in ['app','public','bin','tests']:
    lint.extend(str(p) for p in sorted((root/folder).glob('*.php')))
for file in lint:
    run('lint-'+pathlib.Path(file).parent.name+'-'+pathlib.Path(file).name,[php,'-l',file],60)
for pattern in ['public/assets/*.js','tests/*.cjs']:
    for file in sorted(root.glob(pattern)):
        run('lint-'+file.parent.name+'-'+file.name,[node,'--check',str(file)],60)
for file in sorted((root/'tests').glob('*.py')):
    run('lint-tests-'+file.name,[sys.executable,'-m','py_compile',str(file)],60)
run('git-diff-check',['git','-c','core.safecrlf=false','diff','--check'],60)
print('RESULTS '+str(out),flush=True)
failed=[r['suite'] for r in results if not r['passed']]
print('TOTAL '+str(len(results))+' checks/suites; failures: '+str(failed),flush=True)
sys.exit(1 if failed else 0)
