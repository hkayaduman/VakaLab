const assert = require('node:assert/strict');
const {spawn, spawnSync} = require('node:child_process');
const {randomUUID} = require('node:crypto');
const fs = require('node:fs');
const net = require('node:net');
const path = require('node:path');
const {chromium} = require(process.env.PLAYWRIGHT_PATH || 'playwright');

const root = path.resolve(__dirname, '..');
const temp = path.join(__dirname, 'tmp', 'workspace-' + randomUUID());
fs.mkdirSync(path.join(temp, 'sessions'), {recursive: true});
const config = path.join(temp, 'config.php');
const password = randomUUID();
const php = process.env.PHP_BIN || path.join(root, '.tools/php/php.exe');
const env = {...process.env, VAKALAB_CONFIG:config, VAKALAB_SEED_PASSWORD:password, VAKALAB_ADMIN_PASSWORD:password};
fs.writeFileSync(config, "<?php $c=require __DIR__.'/../../../config/example.php';$c['dsn']='sqlite:'.__DIR__.'/workspace.sqlite';$c['https']=false;return $c;");
for (const args of [['migrate'], ['seed'], ['super-admin', 'TEST-WS-ADMIN']]) {
    const result = spawnSync(php, ['bin/console.php', ...args], {cwd:root, env, encoding:'utf8', windowsHide:true});
    assert.equal(result.status, 0, result.stderr);
}

const call = (page, op, body, params) => page.evaluate(({op, body, params}) => api(op, body, params), {op, body, params});
function check(value, message) {
    assert.ok(value, message);
    console.log('PASS WORKSPACE BROWSER ' + message);
}

async function checkHeader(page, role, widths) {
    const viewport = page.viewportSize();
    for (const width of widths) {
        await page.setViewportSize({width, height:900});
        const layout = await page.evaluate(() => {
            const rect = selector => document.querySelector(selector).getBoundingClientRect().toJSON();
            const header = rect('.masthead');
            const controls = ['.workspace-picker', '#identity > span', '#my-profile', '#logout'].map(rect);
            const center = box => (box.top + box.bottom) / 2;
            return {
                inside:controls.every(box => box.top >= header.top && box.bottom <= header.bottom &&
                    box.left >= header.left && box.right <= header.right),
                oneRow:controls.every(box => Math.abs(center(box) - center(controls[0])) <= 1),
                buttonsTogether:Math.abs(center(controls[2]) - center(controls[3])) <= 1,
            };
        });
        check(layout.inside && layout.buttonsTogether && (width < 1280 || layout.oneRow),
            `${role} header contains its controls and keeps both buttons together at ${width}px`);
    }
    await page.setViewportSize(viewport);
}

(async () => {
    const socket = net.createServer();
    await new Promise(resolve => socket.listen(0, '127.0.0.1', resolve));
    const port = socket.address().port;
    await new Promise(resolve => socket.close(resolve));
    const url = `http://127.0.0.1:${port}/`;
    const server = spawn(php, ['-d', 'session.save_path=' + path.join(temp, 'sessions'), '-S', `127.0.0.1:${port}`, '-t', 'public'], {cwd:root, env, stdio:'ignore', windowsHide:true});
    let browser;
    try {
        for (let i=0; i<60; i++) {
            try { if ((await fetch(url)).ok) break; } catch {}
            await new Promise(resolve => setTimeout(resolve, 100));
        }
        browser = await chromium.launch({headless:true, executablePath:process.env.CHROME_BIN});
        const login = async (page, username) => {
            await page.goto(url);
            await page.locator('#login').fill(username);
            await page.locator('#password').fill(password);
            await page.locator('#auth-submit').click();
            await page.locator('#active-workspace').waitFor();
        };
        const openTab = (page, name) => page.locator(`[data-admin-tab="${name}"]`).click();
        const rootPage = await browser.newPage();
        await login(rootPage, 'TEST-WS-ADMIN');
        await checkHeader(rootPage, 'Super admin', [2560, 1440, 1024, 768, 700, 390, 320]);
        await openTab(rootPage, 'workspaces');
        await rootPage.locator('#workspace-create').click();
        await rootPage.locator('#workspace-name').fill('SENTETİK hedef');
        await rootPage.locator('#commit').click();
        await rootPage.locator('dialog').waitFor({state:'detached'});
        const target = (await call(rootPage, 'me')).workspaces.find(w => w.name === 'SENTETİK hedef').id;
        const sourceOnly = await browser.newPage({viewport:{width:390,height:844}});
        await login(sourceOnly, 'TEST-ARASTIRMACI');
        check(await sourceOnly.locator('#active-workspace option').count() === 1, 'researcher only sees assigned workspaces');
        await openTab(rootPage, 'participants');
        await rootPage.locator('[data-user-memberships="test-researcher"]').click();
        await rootPage.locator(`[data-workspace-membership][value="${target}"]`).check();
        await rootPage.locator('#commit').click();
        await rootPage.locator('dialog').waitFor({state:'detached'});
        await sourceOnly.reload();
        await sourceOnly.locator(`#active-workspace option[value="${target}"]`).waitFor({state:'attached'});
        await sourceOnly.locator('#active-workspace').selectOption(target);
        await sourceOnly.waitForFunction(id => dash.workspace_id === id, target);
        await checkHeader(sourceOnly, 'Researcher', [2560, 1440, 1024, 768, 700, 390, 320]);
        await openTab(sourceOnly, 'participants');
        await sourceOnly.locator('#bulk-participants').click();
        check(await sourceOnly.locator('#bulk-workspace').inputValue() === 'default', 'bulk defaults to the system default when it is accessible');
        await sourceOnly.locator('#bulk-workspace').selectOption(target);
        await sourceOnly.locator('[data-bulk-login]').first().fill('TEST-WS-BULK');
        await sourceOnly.locator('[data-bulk-password]').first().fill(password);
        await sourceOnly.locator('#commit').click();
        await sourceOnly.locator('dialog').waitFor({state:'detached'});
        await sourceOnly.locator('#add-participant').click();
        await sourceOnly.locator('#p-role').selectOption('researcher');
        await sourceOnly.locator('#account-workspace').selectOption(target);
        await sourceOnly.locator('#p-login').fill('TEST-WS-RESEARCHER');
        await sourceOnly.locator('#p-password').fill(password);
        await sourceOnly.locator('#commit').click();
        await sourceOnly.locator('dialog').waitFor({state:'detached'});
        const colleague = await browser.newPage();
        await login(colleague,'TEST-WS-RESEARCHER');
        check(await colleague.locator('#active-workspace option').count() === 1 && await colleague.locator('#active-workspace').inputValue() === target, 'researcher can create a researcher with immediate scoped access');
        await openTab(colleague, 'participants');
        await colleague.locator('#bulk-participants').click();
        check(await colleague.locator('#bulk-workspace').inputValue() === target, 'bulk uses the active workspace if the default is unavailable');
        await colleague.locator('#dialog-close').click();

        const student = await browser.newPage();
        await student.goto(url);
        await student.locator('#auth-register').click();
        check(await student.locator('#registration-workspace option').count() === 2, 'registration names both workspaces');
        await student.locator('#registration-workspace').selectOption(target);
        await student.locator('#login').fill('TEST-WS-STUDENT');
        await student.locator('#account-name').fill('Sentetik Öğrenci');
        await student.locator('#password').fill(password);
        await student.locator('#password-confirm').fill(password);
        await student.locator('#auth-submit').click();
        await student.locator('#auth-feedback').waitFor({state:'visible'});
        await login(student, 'TEST-WS-STUDENT');
        await checkHeader(student, 'Student', [1440, 768, 390, 320]);
        check(await student.locator('#active-workspace').inputValue() === target && (await call(student,'cases')).length === 0, 'registration immediately grants only membership');
        await openTab(rootPage, 'cases');
        await rootPage.locator('[data-move-case="test-case-1"]').click();
        await rootPage.locator('#move-workspace').selectOption(target);
        await rootPage.locator('#commit').click();
        await rootPage.locator('dialog').waitFor({state:'detached'});
        check(await rootPage.locator('[data-edit-case="test-case-1"]').count() === 0, 'moving removes the case from the source view');
        const users = (await call(rootPage,'dashboard')).users;
        const account = users.find(u => u.login === 'TEST-WS-STUDENT');
        const bulk = users.find(u => u.login === 'TEST-WS-BULK');
        const dashboard = await call(colleague, 'dashboard', undefined, {workspace_id:target});
        check(dashboard.cases.some(c=>c.id==='test-case-1') && dashboard.member_ids.includes(bulk.id), 'destination colleague sees moved case and bulk-created student');
        await openTab(colleague, 'cases');
        await colleague.locator('#assignment').click();
        await colleague.locator('#a-case').selectOption('test-case-1');
        await colleague.locator(`[data-assign-student][value="${account.id}"]`).check();
        await colleague.locator('#commit').click();
        await colleague.locator('dialog').waitFor({state:'detached'});
        await student.reload();
        await student.locator('[data-case="test-case-1"]').click();
        await student.locator('[data-section="profile"]').click();
        await student.evaluate(()=>drainEvents());
        const appId=await student.evaluate(()=>work.application.id);
        const observed=await call(colleague,'view',undefined,{app_id:appId});
        check(observed.events.some(e=>e.type==='section_open'&&e.section==='profile'), 'newly assigned student events reach the destination researcher');
    } finally {
        if (browser) await browser.close();
        server.kill();
    }
})().catch(error=>{console.error(error);process.exitCode=1;});
