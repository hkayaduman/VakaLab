const assert = require('node:assert/strict');
const {spawn, spawnSync} = require('node:child_process');
const {createHash, randomUUID} = require('node:crypto');
const fs = require('node:fs');
const net = require('node:net');
const path = require('node:path');
const {chromium} = require(process.env.PLAYWRIGHT_PATH || 'playwright');

const root = path.resolve(__dirname, '..');
const tmp = path.join(__dirname, 'tmp', 'account-access-' + randomUUID());
fs.mkdirSync(tmp, {recursive: true});
const php = process.env.PHP_BIN || path.join(root, '.tools/php/php.exe');
const password = randomUUID();
const config = path.join(tmp, 'config.php');
const env = {...process.env, VAKALAB_CONFIG: config, VAKALAB_SEED_PASSWORD: password, VAKALAB_ADMIN_PASSWORD: password};
fs.writeFileSync(config, "<?php $c=require __DIR__.'/../../../config/example.php';$c['dsn']='sqlite:'.__DIR__.'/test.sqlite';$c['https']=false;return $c;");
function phpRun(args, override = {}) {
    const run = spawnSync(php, args, {cwd: root, env: {...env, ...override}, encoding: 'utf8', windowsHide: true});
    assert.equal(run.status, 0, run.stdout + run.stderr);
    return run.stdout.trim();
}
for (const command of [['migrate'], ['seed'], ['researcher', 'TEST-OTHER-STAFF'], ['super-admin', 'TEST-ACCOUNT-ROOT']]) {
    phpRun(['bin/console.php', ...command]);
}
const inspect = (code, ...args) => phpRun(['-r', 'require "app/bootstrap.php";$db=new Db(cfg());' + code, ...args]);
const hash = value => createHash('sha256').update(value).digest('hex');
const call = (page, op, body) => page.evaluate(({op, body}) => api(op, body), {op, body});
const status = (page, op, body) => page.evaluate(async ({op, body}) => {
    try { await api(op, body); return 200; }
    catch (error) { return error.status; }
}, {op, body});
let count = 0;
function check(condition, label) {
    assert.ok(condition, label);
    console.log(`PASS ACCOUNT ${++count} ${label}`);
}

(async () => {
    const socket = net.createServer();
    await new Promise(resolve => socket.listen(0, '127.0.0.1', resolve));
    const port = socket.address().port;
    await new Promise(resolve => socket.close(resolve));
    const url = `http://127.0.0.1:${port}/`;
    const server = spawn(php, ['-S', `127.0.0.1:${port}`, '-t', 'public'], {cwd: root, env, stdio: 'ignore', windowsHide: true});
    let browser;
    try {
        for (let i = 0; i < 50; i++) {
            try { if ((await fetch(url)).ok) break; } catch {}
            await new Promise(resolve => setTimeout(resolve, 100));
        }
        browser = await chromium.launch({headless: true, executablePath: process.env.CHROME_BIN});
        const errors = [];
        const open = async options => {
            const page = await browser.newPage(options);
            page.on('pageerror', error => errors.push(error.message));
            await page.goto(url);
            await page.locator('#login-form').waitFor();
            return page;
        };
        const signIn = async (page, username, secret = password) => {
            await page.locator('#login').fill(username);
            await page.locator('#password').fill(secret);
            await page.getByRole('button', {name: 'Giriş yap'}).click();
            await page.locator('#logout').waitFor();
        };
        const owner = await open(), outsider = await open(), admin = await open(), student = await open({viewport: {width: 390, height: 844}});
        await signIn(owner, 'TEST-ARASTIRMACI');
        await signIn(outsider, 'TEST-OTHER-STAFF');
        await signIn(admin, 'TEST-ACCOUNT-ROOT');
        check(await student.getByRole('button', {name:'Giriş yap'}).isVisible() && await student.locator('#auth-register').isVisible() && await student.locator('#auth-forgot').isVisible(), 'three functional entry options are visible');
        check(await student.evaluate(() => document.documentElement.scrollWidth <= innerWidth), 'login entry fits a mobile viewport');
        check(await status(student, 'register') === 405 && await status(student, 'update_profile', {name:'Guest'}) === 401, 'registration requires POST and profile updates require authentication');
        const withoutCsrf = await student.evaluate(async () => (await fetch('api.php?op=request_password_reset', {method:'POST', body:'{}'})).status);
        check(withoutCsrf === 403, 'public account forms require CSRF');

        const username = 'TEST-SELF-REGISTERED';
        const secret = randomUUID();
        const registration = {login:username, name:'Sentetik öğrenci', password:secret};
        await owner.locator('[data-admin-tab="participants"]').click();
        await student.locator('#auth-register').click();
        await student.locator('#login').fill(username);
        await student.locator('#account-name').fill(registration.name);
        check(await student.locator('#account-code').count()===0,'registration has no invitation field');
        await student.locator('#password').fill(secret);
        await student.locator('#password-confirm').fill(secret);
        await student.locator('#auth-submit').click();
        await student.getByText('Hesabınız oluşturuldu.', {exact:false}).waitFor();
        const account = (await call(admin, 'dashboard')).users.find(u => u.login === username);
        check(account.role === 'student' && account.owner_id === null, 'public registration creates only an unlinked student account');
        check(await status(student, 'register', registration) === 409, 'registration cannot replace an existing account');
        await signIn(student, username, secret);
        check((await call(student, 'cases')).length === 0, 'new student can sign in without access to other workspaces');
        await call(admin,'admin',{action:'assign',case_id:'test-case-1',user_id:account.id,active:true});
        const assigned = await call(owner, 'dashboard');
        check(assigned.users.some(u=>u.id===account.id&&u.metadata==='{}') && assigned.assignments.some(a=>a.user_id===account.id&&a.case_id==='test-case-1'&&Number(a.active)),
            'self-registered student is a workspace member with a separate case assignment');
        check((await call(student,'cases')).some(c=>c.id==='test-case-1'),'super admin can assign a directly registered student without linking an owner');

        const updateProfile = async (page, name) => {
            await page.locator('#my-profile').click();
            await page.locator('#profile-name').fill(name);
            await page.getByRole('button',{name:'Profili kaydet'}).click();
            await page.getByText('Profil kaydedildi.',{exact:true}).waitFor();
            return call(page,'me');
        };
        const studentProfile = await updateProfile(student,'Güncellenmiş Öğrenci');
        check(studentProfile.name==='Güncellenmiş Öğrenci' && studentProfile.login===username, 'student profile updates the name and retains the login');
        await student.locator('#dialog-close').click();
        const adminProfile = await updateProfile(admin,'Güncellenmiş Yönetici');
        check(adminProfile.name==='Güncellenmiş Yönetici' && adminProfile.role==='super_admin', 'super admin can update their own profile');
        const secondAdmin = await open();
        await signIn(secondAdmin,'TEST-ACCOUNT-ROOT');
        const profilePassword = randomUUID();
        check(await status(admin,'change_password',{current_password:'incorrect',password:profilePassword})===422,'password change requires the current password');
        await admin.locator('#profile-current').fill(password);
        await admin.locator('#profile-new').fill(profilePassword);
        await admin.locator('#profile-confirm').fill(profilePassword);
        await admin.getByRole('button',{name:'Parolayı değiştir'}).click();
        await admin.getByText('Parola değiştirildi.',{exact:true}).waitFor();
        await admin.locator('#dialog-close').click();
        check(await status(admin,'me')===200 && await status(secondAdmin,'me')===401,'password change preserves the current session and revokes other sessions');
        const adminNewSession = await open();
        await signIn(adminNewSession,'TEST-ACCOUNT-ROOT',profilePassword);
        check((await call(adminNewSession,'me')).role==='super_admin','super admin can log in with the new password');

        const guest = await open({viewport: {width: 390, height: 844}});
        assert.deepEqual(await call(guest, 'request_password_reset', {login: 'TEST-UNKNOWN'}), {ok:true});
        await guest.locator('#auth-forgot').click();
        await guest.locator('#login').fill(username);
        await guest.locator('#auth-submit').click();
        await guest.getByText('Bu hesap uygunsa talep yetkili kişiye ulaştı.', {exact:false}).waitFor();
        check(await guest.locator('#account-code').isVisible() && await guest.evaluate(() => document.documentElement.scrollWidth <= innerWidth), 'recovery request opens the mobile reset form');
        check((await call(outsider, 'password_reset_requests')).length === 0 &&
            await status(outsider, 'password_reset_code', {user_id: account.id}) === 403, 'an unrelated researcher cannot list or issue recovery for the student');
        check(await status(student, 'password_reset_requests') === 403, 'student cannot list requests');
        check(await status(owner, 'password_reset_code', {user_id: account.id}) === 403, 'case access does not grant password recovery access');
        await adminNewSession.locator('[data-admin-tab="participants"]').click();
        await adminNewSession.locator('#account-reset-panel summary').click();
        await adminNewSession.locator(`[data-issue-reset="${account.id}"]`).click();
        await adminNewSession.locator('#commit').click();
        const reset = await adminNewSession.locator('#issued-account-code').inputValue();
        await adminNewSession.locator('#dialog-close').click();
        check(inspect('echo $db->one("SELECT token_hash FROM password_resets WHERE user_id=?",[$argv[1]])["token_hash"];', account.id) === hash(reset), 'database stores only the recovery code digest');
        const newPassword = randomUUID();
        inspect('$db->q("UPDATE password_resets SET expires_at=0 WHERE user_id=?",[$argv[1]]);', account.id);
        check(await status(guest, 'reset_password', {login:username, password:newPassword, code:reset}) === 422, 'expired recovery code is rejected');
        const replacement = await call(adminNewSession, 'password_reset_code', {user_id:account.id});
        assert.deepEqual(await call(guest, 'request_password_reset', {login: username}), {ok:true}, 'known and unknown requests return the same result');
        check(await status(guest, 'reset_password', {login:'TEST-OGR-01',password:newPassword,code:replacement.code}) === 422 &&
            await status(guest, 'reset_password', {login:username,password:newPassword,code:reset}) === 422, 'code is bound to its account and reissue replaces the previous code');
        await guest.locator('#account-code').fill(replacement.code);
        await guest.locator('#password').fill(newPassword);
        await guest.locator('#password-confirm').fill(newPassword);
        await guest.locator('#auth-submit').click();
        await guest.getByText('Parolanız yenilendi.', {exact:false}).waitFor();
        check(await status(student, 'me') === 401 && await status(guest, 'login', {login:username,password:secret}) === 401, 'reset rejects the previous password and invalidates earlier sessions');
        check(await status(guest, 'reset_password', {login:username,password:newPassword,code:replacement.code}) === 422 &&
            !(await call(owner, 'password_reset_requests')).some(u=>u.id===account.id), 'successful reset consumes the code and closes the request');
        await signIn(guest, username, newPassword);
        check((await call(guest, 'me')).login === username, 'student can sign in with the renewed password');
        const studentPassword=randomUUID();
        check(await status(guest,'change_password',{current_password:newPassword,password:studentPassword})===200 &&
            await status(guest,'me')===200,'student can change their own password while remaining signed in');

        await call(guest, 'request_password_reset', {login:'TEST-OTHER-STAFF'});
        await call(guest, 'request_password_reset', {login:'TEST-ACCOUNT-ROOT'});
        const queue = await call(admin, 'password_reset_requests');
        const researcher = queue.find(u=>u.login==='TEST-OTHER-STAFF');
        check(researcher && !queue.some(u=>u.login==='TEST-ACCOUNT-ROOT') &&
            await status(owner, 'password_reset_code', {user_id:researcher.id}) === 403, 'researcher requests go to the super admin and the privileged account has a server-only recovery path');
        check(Boolean((await call(admin, 'password_reset_code', {user_id:researcher.id})).code), 'super admin can issue a researcher’s recovery code');
        const adminPassword = randomUUID();
        phpRun(['bin/console.php','super-admin-password','TEST-ACCOUNT-ROOT'], {VAKALAB_ADMIN_PASSWORD:adminPassword});
        check(await status(admin, 'me') === 401, 'server-assisted super admin reset also invalidates its old session');
        const verified = await open();
        await signIn(verified, 'TEST-ACCOUNT-ROOT', adminPassword);
        check((await call(verified, 'me')).role === 'super_admin' && errors.length === 0, 'super admin logs in with the renewed password with no browser errors');
        console.log(`TOTAL ACCOUNT ACCESS ${count} checks passed on isolated SQLite and Chromium.`);
    } finally {
        if (browser) await browser.close();
        server.kill();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
