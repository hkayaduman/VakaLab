const assert = require('node:assert/strict');
const {spawn, spawnSync} = require('node:child_process');
const {randomUUID} = require('node:crypto');
const fs = require('node:fs');
const net = require('node:net');
const path = require('node:path');
const {chromium} = require(process.env.PLAYWRIGHT_PATH || 'playwright');

const root = path.resolve(__dirname, '..');
const tmp = path.join(__dirname, 'tmp', 'super-admin-' + randomUUID());
fs.mkdirSync(tmp, {recursive: true});
const php = process.env.PHP_BIN || path.join(root, '.tools/php/php.exe');
const config = path.join(tmp, 'config.php');
const password = randomUUID();
const env = {...process.env, VAKALAB_CONFIG: config, VAKALAB_SEED_PASSWORD: password, VAKALAB_ADMIN_PASSWORD: password};
fs.writeFileSync(config, "<?php $c=require __DIR__.'/../../../config/example.php';$c['dsn']='sqlite:'.__DIR__.'/test.sqlite';$c['https']=false;return $c;");
for (const args of [['migrate'], ['seed'], ['super-admin', 'TEST-SUPER-ADMIN']]) {
    const result = spawnSync(php, ['bin/console.php', ...args], {cwd: root, env, encoding: 'utf8', windowsHide: true});
    assert.equal(result.status, 0, result.stdout + result.stderr);
}

let count = 0;
function check(condition, message) {
    assert.ok(condition, message);
    console.log(`PASS SUPER ADMIN ${++count} ${message}`);
}
const call = (page, op, body, params) => page.evaluate(({op, body, params}) => api(op, body, params), {op, body, params});
const status = (page, op, body, params) => page.evaluate(async ({op, body, params}) => {
    try { await api(op, body, params); return 200; }
    catch (error) { return error.status; }
}, {op, body, params});

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
        const login = async name => {
            const page = await browser.newPage();
            page.on('pageerror', error => errors.push(error.message));
            await page.goto(url);
            await page.locator('#login').fill(name);
            await page.locator('#password').fill(password);
            await page.getByRole('button', {name: 'Giriş yap'}).click();
            await page.locator('[data-admin-tab], [data-case]').first().waitFor();
            return page;
        };
        const admin = await login('TEST-SUPER-ADMIN');
        check(await admin.getByRole('heading', {name: 'Sistem yönetimi'}).isVisible(), 'CLI account opens the global panel');
        await admin.locator('[data-admin-tab="participants"]').click();
        await admin.getByText('TEST-OGR-07', {exact: true}).waitFor();
        const secondWorkspace=(await call(admin,'admin',{action:'workspace_create',name:'SENTETİK ikinci alan'})).id;
        await admin.reload();
        await admin.locator('[data-admin-tab="participants"]').click();
        const addAccount = async (role, login, workspace='default') => {
            await admin.locator('#add-participant').click();
            await admin.locator('#p-role').selectOption(role);
            await admin.locator('#account-workspace').selectOption(workspace);
            await admin.locator('#p-login').fill(login);
            await admin.locator('#p-name').fill(login);
            await admin.locator('#p-password').fill(password);
            await admin.locator('#commit').click();
            await admin.locator('dialog').waitFor({state: 'detached'});
            return (await call(admin, 'dashboard')).users.find(user => user.login === login);
        };
        const other = await addAccount('researcher', 'TEST-OTHER-RESEARCHER', secondWorkspace);
        const single = await addAccount('student', 'TEST-SINGLE-STUDENT');
        const adminId = (await call(admin, 'me')).id;
        check(other.owner_id === adminId && single.owner_id === adminId, 'single accounts retain the creating actor');
        await admin.locator('#account-search').fill('TEST-SUPER-ADMIN');
        check(await admin.locator('#account-rows').getByText(single.login, {exact: true}).count() > 0, 'account search matches the creating account');
        await admin.locator('#account-search').clear();

        await admin.locator('#bulk-participants').click();
        const logins = admin.locator('[data-bulk-login]');
        const passwords = admin.locator('[data-bulk-password]');
        check(await logins.count() === 3 && await admin.locator('#bulk-workspace').inputValue() === 'default', 'bulk dialog defaults to the system workspace');
        await admin.locator('#add-account-row').click();
        await admin.getByRole('button', {name: '4. satırı kaldır'}).click();
        check(await logins.count() === 3, 'rows can be added and removed');
        await logins.nth(0).fill('TEST-BULK-STUDENT-1');
        await passwords.nth(0).fill(password);
        await logins.nth(2).fill('TEST-BULK-STUDENT-2');
        await passwords.nth(2).fill('short');
        await admin.locator('#commit').click();
        await admin.getByText('3. satır: Parola', {exact: false}).waitFor();
        check(!(await call(admin, 'dashboard')).users.some(u => u.login === 'TEST-BULK-STUDENT-1'), 'an invalid row reports its number and prevents partial account creation');
        const secondPassword = randomUUID();
        await passwords.nth(2).fill(secondPassword);
        await logins.nth(2).fill(single.login);
        await admin.locator('#commit').click();
        await admin.getByText('3. satır: Bu kullanıcı adı zaten kayıtlı', {exact: true}).waitFor();
        check(!(await call(admin, 'dashboard')).users.some(u => u.login === 'TEST-BULK-STUDENT-1'), 'a duplicate account rolls the batch back');
        await logins.nth(2).fill('TEST-BULK-STUDENT-2');
        await admin.locator('#commit').click();
        await admin.locator('dialog').waitFor({state: 'detached'});
        const bulk = (await call(admin, 'dashboard')).users.filter(u => u.login.startsWith('TEST-BULK-STUDENT-'));
        check(bulk.length === 2 && bulk.every(u => u.owner_id === adminId && u.role === 'student' && u.status === 'active'), 'one save creates two active students and ignores the empty row');
        const student = bulk.find(u => u.login.endsWith('-1'));
        const duplicateBatch = {action: 'participants_bulk', rows: [{login: 'TEST-DUPLICATE', password}, {login: 'TEST-DUPLICATE', password}]};
        check(await status(admin, 'admin', duplicateBatch) === 409 &&
            !(await call(admin, 'dashboard')).users.some(u => u.login === 'TEST-DUPLICATE'), 'a repeated login within the table also rolls the batch back');

        const otherPage = await login(other.login);
        const firstPage = await login('TEST-ARASTIRMACI');
        const {id: caseId} = await call(otherPage, 'admin', {action: 'case', title: 'SENTETİK ikinci çalışma alanı', body: 'SENTETİK vaka metni', status: 'active'});
        check(await status(otherPage, 'admin', {action: 'assign', case_id: caseId, user_id: student.id, active: true}) === 403,
            'researchers cannot assign arbitrary students');
        await call(admin, 'admin', {action: 'participant', id: student.id, login: student.login, metadata: {researcher_notes: 'SENTETİK özel profil notu'}});
        await call(admin,'admin',{action:'workspace_memberships',user_id:student.id,workspace_ids:['default',secondWorkspace]});
        await call(admin, 'admin', {action: 'assign', case_id: caseId, user_id: student.id, active: true});
        await call(admin, 'admin', {action: 'assign', case_id: 'test-case-1', user_id: student.id, active: true});
        const studentPage = await login(student.login);
        check(await status(studentPage, 'admin', {action: 'participants_bulk', rows: [{login: 'TEST-FORBIDDEN', password}]}) === 403,
            'students cannot bulk-create accounts');
        const {id: appId} = await call(studentPage, 'start', {case_id: caseId});
        const {id: secondAppId} = await call(studentPage, 'start', {case_id: 'test-case-1'});
        check((await call(studentPage, 'cases')).some(c=>c.id==='test-case-1') &&
            (await call(studentPage, 'cases', undefined, {workspace_id:secondWorkspace})).some(c=>c.id===caseId),
            'one student login opens cases belonging to two researchers');
        const secondStudentPage = await browser.newPage();
        await secondStudentPage.goto(url);
        await secondStudentPage.locator('#login').fill('TEST-BULK-STUDENT-2');
        await secondStudentPage.locator('#password').fill(secondPassword);
        await secondStudentPage.getByRole('button', {name: 'Giriş yap'}).click();
        await secondStudentPage.locator('#logout').waitFor();
        check((await call(secondStudentPage, 'me')).login === 'TEST-BULK-STUDENT-2',
            'each bulk-created account can sign in with its own password');

        const all = await call(admin, 'dashboard');
        const targetDashboard = await call(admin, 'dashboard', undefined, {workspace_id:secondWorkspace});
        check(all.users.length === 13 && all.users.every(user => !('password' in user)), 'global dashboard lists every account without password hashes');
        check(targetDashboard.cases.some(c=>c.id===caseId) && all.cases.some(c => c.id === 'test-case-1'), 'super admin can inspect both workspaces');
        check(targetDashboard.assignments.some(a => a.case_id === caseId && a.user_id === student.id) && all.audit.some(a => a.actor === adminId && a.action === 'assign') && !JSON.stringify(all.audit).includes(secondPassword) && !JSON.stringify(all.audit).includes(password), 'super admin sees scoped assignments and audit history');
        const first = await call(firstPage, 'dashboard');
        const second = await call(otherPage, 'dashboard');
        check(!first.users.some(u => u.id===other.id) && !first.cases.some(c => c.id === caseId) && !first.audit.some(a => a.actor === other.id), 'first researcher cannot see the other workspace');
        check(second.cases.length === 1 && second.users.some(u => u.id === student.id&&u.metadata==='{}') &&
            !second.users.some(u => u.id === 'test-student-6'), 'shared students are scoped to memberships and private profile fields are withheld');
        check(await status(firstPage, 'view', undefined, {app_id: appId}) === 403 && await status(studentPage, 'dashboard') === 403, 'workspace and dashboard authorization is enforced by the API');
        check((await call(admin, 'view', undefined, {app_id: appId})).application.case_owner_id === other.id, 'super admin can open work from the second workspace');

        check(await status(otherPage, 'view', undefined, {app_id: appId}) === 200 &&
            await status(otherPage, 'view', undefined, {app_id: secondAppId}) === 403 &&
            await status(otherPage, 'admin', {action: 'participant', id: student.id}) === 403,
            'researcher can open their case file without editing the student account or reading another case');
        await otherPage.reload();
        await otherPage.locator('[data-admin-tab="participants"]').click();
        await otherPage.locator('#bulk-participants').waitFor();
        check(await otherPage.locator('#account-rows').getByText(student.login, {exact: true}).count() === 0,
            'account management excludes assigned external students');
        await otherPage.locator('[data-admin-tab="overview"]').click();
        await otherPage.locator('[data-process]').click();
        await otherPage.locator('#process-data').waitFor();
        check((await otherPage.locator('h1').innerText()).includes(student.login), 'researcher opens the assigned student file from the dashboard');
        const researcherExport = await otherPage.evaluate(async () => (await fetch('api.php?op=download&format=json&all_data=1')).json());
        check(researcherExport.participants.some(u => u.id === student.id && u.metadata === '{}') &&
            researcherExport.applications.some(a => a.id === appId) && !researcherExport.applications.some(a => a.id === secondAppId),
            'researcher export includes the case participant and excludes private profile data and unrelated work');

        const seeded = all.applications.find(a => a.user_id === 'test-student-6' && a.case_id === 'test-case-1');
        const view = await call(admin, 'view', undefined, {app_id: seeded.id});
        check(view.messages.length > 0 && view.steps.length === 8 && view.reports.length === 2, 'super admin can see chat, evaluation history and reports');
        check((await call(admin, 'report_data', undefined, {id: view.reports[0].id})).report.internal !== undefined, 'super admin can read an unpublished report with its internal content');
        const packet = await admin.evaluate(async () => (await fetch('api.php?op=download&format=json&all_data=1')).json());
        check(packet.applications.some(a => a.id === seeded.id) &&
            (await admin.evaluate(async id=>(await (await fetch('api.php?'+new URLSearchParams({op:'download',format:'json',all_data:1,workspace_id:id}))).json()),secondWorkspace)).applications.some(a=>a.id===appId), 'super admin can export both workspaces separately');

        await admin.locator('[data-admin-tab="overview"]').click();
        await admin.locator('#overview-case').selectOption('test-case-1');
        await admin.locator('#overview-search').fill('TEST-OGR-06');
        await admin.locator('[data-process]').click();
        await admin.locator('#process-data').waitFor();
        check(await admin.locator('[data-student-report]').count() === 2 && await admin.locator('#exclude,#add-note,#ai-message-labels,#holistic-save,#new-attempt').count() === 5, 'global workspace shows research controls and preserves historical reports');
        await admin.getByText('Değerlendirme ve rapor geçmişi', {exact: true}).click();
        await admin.locator('[data-student-report]').first().click();
        await admin.locator('dialog .student-report').waitFor();
        await admin.locator('#dialog-close').click();

        const generated = admin.waitForResponse(r => r.url().includes('op=holistic') && r.request().postDataJSON()?.action === 'generate');
        await admin.locator('#holistic-generate').click();
        const generationResponse = await generated;
        check(generationResponse.ok(), 'super admin generates a mock assessment from the case workspace rubric');
        const {data: report} = await generationResponse.json();
        const reportTarget = {app_id: seeded.id, report_id: report.id, version: report.version};
        await admin.locator('#holistic-approve:enabled').click();
        await admin.getByText('Rapor kaydedildi. Araştırmacı onaylı.', {exact: true}).waitFor();
        check(await admin.locator('#holistic-show').isDisabled() &&
              await status(admin, 'holistic', {action: 'show', ...reportTarget}) === 409, 'publication still requires closing data collection');
        await call(admin, 'admin', {action: 'collection', case_id: seeded.case_id, closed: true});
        const published = await call(admin, 'holistic', {action: 'show', ...reportTarget});
        const hidden = await call(admin, 'holistic', {action: 'hide', ...reportTarget});
        check(published.published_at && !hidden.published_at, 'super admin can publish and withdraw the approved report');
        check(await status(firstPage, 'holistic', {action: 'approve', app_id: appId}) === 403, 'researcher cannot assess work from another workspace');

        const message = view.messages.find(m => m.role === 'user');
        const labels = await call(admin, 'message_labels', {app_id: seeded.id, target_role: 'user'});
        const label = all.definitions.find(d => d.family === 'label' && JSON.parse(d.body).target_role === 'user');
        await call(admin, 'admin', {action: 'labels', message_id: message.id, labels: [label.id], reason: 'SENTETİK etiket kararı'});
        await call(admin, 'admin', {action: 'note', app_id: seeded.id, body: 'SENTETİK süper admin notu', reason: 'Test'});
        await call(admin, 'admin', {action: 'exclude', id: seeded.id, excluded: true, reason: 'Test'});
        const updated = await call(admin, 'view', undefined, {app_id: seeded.id});
        check(message.id in labels.labels && updated.label_reviews.some(r => r.actor === all.users.find(u => u.role === 'super_admin').id) &&
              updated.research_notes.some(n => n.body === 'SENTETİK süper admin notu') && Number(updated.application.excluded) === 1,
              'global labeling, review, notes and exclusion are saved with the actual actor');
        const next = await call(admin, 'admin', {action: 'new_attempt', app_id: seeded.id, reason: 'Test'});
        check(next.attempt_no === 2, 'super admin opens a new attempt for a submitted case');

        await admin.locator('#admin-back').click();
        await admin.locator('[data-admin-tab="definitions"]').click();
        await admin.locator('#active-workspace').selectOption(secondWorkspace);
        await admin.waitForFunction(id=>dash.workspace_id===id,secondWorkspace);
        await admin.locator('#add-rubric').click();
        await admin.locator('#d-name').fill('SENTETİK yönetici rubriği');
        await admin.locator('#r-text').fill('SENTETİK değerlendirme yönergesi');
        await admin.locator('[data-rubric-field="name"]').fill('Gerekçe');
        await admin.locator('[data-rubric-field="level-name"]').first().fill('Başlangıç');
        await admin.locator('[data-rubric-field="description"]').first().fill('SENTETİK başlangıç açıklaması');
        const levelCount = await admin.locator('[data-rubric-field="level-name"]').count();
        for (let i = 1; i < levelCount; i++) {
            await admin.locator('[data-rubric-field="level-name"]').nth(i).fill('Düzey ' + i);
            await admin.locator('[data-rubric-field="description"]').nth(i).fill('SENTETİK düzey açıklaması');
        }
        await admin.locator('#commit').click();
        await admin.locator('dialog').waitFor({state: 'detached'});
        const createdRubric = (await call(otherPage, 'dashboard')).definitions.find(d => d.name === 'SENTETİK yönetici rubriği');
        check(createdRubric?.workspace_id === secondWorkspace, 'super admin creates a rubric in the selected researcher workspace');

        await admin.locator('#add-label').click();
        await admin.locator('#d-name').fill('SENTETİK yönetici etiketi');
        await admin.locator('#d-target-role').selectOption('user');
        await admin.locator('#d-body').fill('SENTETİK etiket açıklaması');
        await admin.locator('#commit').click();
        await admin.locator('dialog').waitFor({state: 'detached'});
        const createdLabel = (await call(otherPage, 'dashboard')).definitions.find(d => d.name === 'SENTETİK yönetici etiketi');
        await admin.locator('[data-edit-def="' + createdLabel.id + '"]').click();
        await admin.locator('#d-name').fill('SENTETİK etiket ikinci sürüm');
        await admin.locator('#commit').click();
        await admin.locator('dialog').waitFor({state: 'detached'});
        const updatedLabel = (await call(otherPage, 'dashboard')).definitions.find(d => d.parent_id === createdLabel.id);
        check(updatedLabel?.workspace_id === secondWorkspace && Number(updatedLabel.version) === 2, 'editing a label creates a version under its original owner');
        check(await status(admin, 'admin', {action: 'labels', message_id: message.id, labels: [updatedLabel.id], reason: 'Test'}) === 403 &&
              await status(admin, 'holistic', {action: 'generate', app_id: seeded.id, rubric_id: createdRubric.id}) === 403,
              'global access preserves workspace restrictions for labels and rubrics');
        await call(admin, 'admin', {action: 'delete_definition', id: updatedLabel.id});
        check((await call(otherPage, 'dashboard')).definitions.filter(d => [createdLabel.id, updatedLabel.id].includes(d.id)).every(d => d.deleted_at),
              'super admin can remove the full definition version chain from active use');

        await admin.locator('[data-admin-tab="cases"]').click();
        await admin.locator('#active-workspace').selectOption('default');
        await admin.waitForFunction(()=>dash.workspace_id==='default');
        await admin.locator('#add-case').click();
        await admin.locator('#c-title').fill('SENTETİK yönetici vakası');
        await admin.locator('#c-body').fill('SENTETİK vaka metni');
        check(await admin.locator('#workspace-owner').count() === 0, 'case creation uses the active workspace shown in the panel');
        await admin.locator('#commit').click();
        await admin.locator('dialog').waitFor({state: 'detached'});
        const createdCase = (await call(admin, 'dashboard')).cases.find(c => c.title === 'SENTETİK yönetici vakası');
        check(createdCase?.owner_id === adminId && !(await call(otherPage, 'dashboard')).cases.some(c => c.id === createdCase.id),
            'super admin-created cases stay outside the researcher’s case management');
        await admin.locator('[data-edit-case="' + createdCase.id + '"]').click();
        await admin.locator('#c-body').fill('SENTETİK yeni sürüm');
        await admin.locator('#commit').click();
        await admin.locator('dialog').waitFor({state: 'detached'});
        await admin.locator('[data-assigned="' + createdCase.id + '"]').click();
        await admin.locator('#manage-case-assignments').click();
        await admin.locator('#a-case').waitFor();
        check(await admin.locator('#a-case').inputValue() === createdCase.id &&
              await admin.locator('[data-assign-student]').count() === 10, 'super admin assignment dialog lists the shared student pool');
        await admin.locator('[data-assign-student][value="' + student.id + '"]').check();
        await admin.locator('[data-assign-student][value="test-student-6"]').check();
        await admin.locator('#commit').click();
        await admin.locator('dialog').waitFor({state: 'detached'});
        await call(admin, 'admin', {action: 'collection', case_id: createdCase.id, closed: true});
        await call(admin, 'admin', {action: 'late_access', case_id: createdCase.id, user_ids: [student.id], open: true});
        const managed = await call(admin, 'dashboard');
        check(Number(managed.cases.find(c => c.id === createdCase.id).version) === 2 && managed.assignments.some(a => a.case_id === createdCase.id && a.user_id === student.id && Number(a.allow_late) === 1),
              'super admin versions cases, assigns students, closes collection and grants extra time');
        check(await status(otherPage, 'admin', {action: 'assign', case_id: createdCase.id, user_id: student.id, active: true}) === 403 &&
              await status(firstPage, 'admin', {action: 'case', id: caseId, title:'Test',body:'Test'}) === 403 &&
              await status(firstPage, 'admin', {action: 'case', workspace_id: secondWorkspace, title: 'Test', body: 'Test'}) === 403,
              'assignment and workspace ownership checks reject out-of-scope writes');
        const own = await call(admin, 'admin', {action: 'case', title: 'SENTETİK süper admin alanı', body: 'Test'});
        check((await call(admin, 'dashboard')).cases.find(c => c.id === own.id)?.owner_id === all.users.find(u => u.role === 'super_admin').id,
              'super admin can also create records in their own workspace');
        check(await status(firstPage, 'admin', {action: 'participant', role: 'super_admin', login: 'TEST-INVALID-ROLE', password}) === 422, 'researchers cannot provision the privileged role through the web API');

        await admin.locator('[data-admin-tab="participants"]').click();
        await admin.locator(`[data-edit-user="${student.id}"]`).click();
        await admin.locator('#p-status').selectOption('frozen');
        await admin.locator('#p-name').fill('SENTETİK güncellenmiş ad');
        await admin.locator('#commit').click();
        await admin.locator('dialog').waitFor({state: 'detached'});
        const frozen = (await call(admin, 'dashboard')).users.find(u => u.id === student.id);
        check(frozen.status === 'frozen' && frozen.name === 'SENTETİK güncellenmiş ad' && frozen.owner_id === adminId, 'super admin edits and freezes a shared student account while preserving its creator');
        check(await status(studentPage, 'me') === 401 && await status(firstPage, 'admin', {action: 'participant', id: student.id}) === 403, 'frozen session loses access and unrelated researcher cannot edit the account');
        await call(admin, 'admin', {action: 'participant', id: student.id, login: student.login, name: student.name, status: 'active'});
        check(await status(studentPage, 'me') === 200, 'global account management can restore access');
        const newPassword = randomUUID();
        await call(admin, 'admin', {action: 'participant', id: student.id, login: student.login, name: student.name, status: 'active', password: newPassword});
        check(await status(studentPage, 'login', {login: student.login, password}) === 401 &&
              await status(studentPage, 'login', {login: student.login, password: newPassword}) === 200, 'super admin can reset a student password');

        await otherPage.locator('#admin-back').click();
        await otherPage.locator('[data-admin-tab="participants"]').click();
        await otherPage.locator('#bulk-participants').click();
        const researcherLogins = otherPage.locator('[data-bulk-login]');
        const researcherPasswords = otherPage.locator('[data-bulk-password]');
        const firstLogin = 'TEST-RESEARCHER-BULK-1';
        const secondLogin = 'TEST-RESEARCHER-BULK-2';
        await researcherLogins.nth(0).fill(firstLogin);
        await researcherPasswords.nth(0).fill(password);
        await researcherLogins.nth(2).fill(secondLogin);
        await researcherPasswords.nth(2).fill('short');
        await otherPage.locator('#commit').click();
        await otherPage.getByText('3. satır: Parola', {exact: false}).waitFor();
        check(!(await call(admin, 'dashboard')).users.some(u => u.login === firstLogin),
            'an invalid researcher row prevents every account in the batch from being created');

        await researcherPasswords.nth(2).fill(password);
        await researcherLogins.nth(2).fill(single.login);
        await otherPage.locator('#commit').click();
        await otherPage.getByText('3. satır: Bu kullanıcı adı zaten kayıtlı', {exact: true}).waitFor();
        const rolledBack = await call(admin, 'dashboard');
        check(!rolledBack.users.some(u => u.login === firstLogin) && !rolledBack.audit.some(a => a.detail.includes(firstLogin)),
            'a username in another workspace rolls back both the accounts and their audit entries');

        await researcherLogins.nth(2).fill(secondLogin);
        await otherPage.locator('#commit').click();
        await otherPage.locator('dialog').waitFor({state: 'detached'});
        await otherPage.locator('#account-rows').getByText(firstLogin, {exact: true}).waitFor();
        await otherPage.locator('#account-rows').getByText(secondLogin, {exact: true}).waitFor();
        const researcherDashboard = await call(otherPage, 'dashboard');
        const createdStudents = researcherDashboard.users.filter(u => [firstLogin, secondLogin].includes(u.login));
        const globalDashboard = await call(admin, 'dashboard');
        check(createdStudents.length === 2 && createdStudents.every(u => u.owner_id === other.id && u.role === 'student' && u.status === 'active' &&
              globalDashboard.users.some(globalUser => globalUser.id === u.id) && researcherDashboard.audit.some(a => a.actor === other.id && a.target === u.id && a.action === 'participant')),
              'researcher bulk creation lists two owned active students with audit entries, also visible to the super admin');
        const unrelated = await call(firstPage, 'dashboard');
        check(createdStudents.every(u => !unrelated.users.some(row => row.id === u.id)) &&
              await status(firstPage, 'admin', {action: 'participant', id: createdStudents[0].id, login: createdStudents[0].login}) === 403,
              'another researcher cannot list or edit the bulk-created accounts');

        await otherPage.locator('[data-admin-tab="cases"]').click();
        await otherPage.locator('#assignment').click();
        await otherPage.locator('#a-case').selectOption(caseId);
        for (const user of createdStudents) await otherPage.locator(`[data-assign-student][value="${user.id}"]`).check();
        await otherPage.locator('#commit').click();
        await otherPage.locator('dialog').waitFor({state: 'detached'});
        const assignments = (await call(otherPage, 'dashboard')).assignments;
        const researcherStudent = await login(firstLogin);
        check(createdStudents.every(u => assignments.some(a => a.case_id === caseId && a.user_id === u.id && Number(a.active))) &&
              (await call(researcherStudent, 'cases')).some(c => c.id === caseId),
              'the researcher can bulk-assign the new students and a new student can sign in and see the case');

        const limitRows = Array.from({length: 101}, (_, index) => ({login: `TEST-RESEARCHER-LIMIT-${index + 1}`, password}));
        check(await status(otherPage, 'admin', {action: 'participants_bulk', rows: limitRows}) === 422 &&
              !(await call(otherPage, 'dashboard')).users.some(u => u.login === limitRows[0].login),
              'a researcher batch with 101 rows is rejected without creating accounts');
        const atLimit = await call(otherPage, 'admin', {action: 'participants_bulk', rows: limitRows.slice(0, 100)});
        const limitStudents = (await call(otherPage, 'dashboard')).users.filter(u => u.login.startsWith('TEST-RESEARCHER-LIMIT-'));
        check(atLimit.count === 100 && limitStudents.length === 100 && limitStudents.every(u => u.owner_id === other.id && u.role === 'student') &&
              !limitStudents.some(u => u.login === limitRows[100].login), 'a researcher can create exactly 100 owned student accounts in one batch');
        check(errors.length === 0, 'no browser JavaScript errors across the roles');
        console.log(`TOTAL SUPER ADMIN ${count} checks passed on isolated SQLite and Chromium.`);
    } finally {
        if (browser) await browser.close();
        server.kill();
    }
})().catch(error => { console.error(error); process.exitCode = 1; });
