const assert = require('node:assert/strict');
const {formatTimestamp} = require('../public/assets/time.js');
const samples = [
    ['2026-10-20T16:19:58.041990Z', 'Europe/Istanbul', '20.10.2026 19:19:58 (UTC+03:00)'],
    ['2026-10-20T16:29:55.583166Z', 'Europe/Istanbul', '20.10.2026 19:29:55 (UTC+03:00)'],
    ['2026-12-31T22:30:00Z', 'Europe/Istanbul', '01.01.2027 01:30:00 (UTC+03:00)'],
    ['2026-10-20T16:19:58Z', 'UTC', '20.10.2026 16:19:58 (UTC+00:00)'],
    ['2026-01-20T16:19:58Z', 'Europe/Berlin', '20.01.2026 17:19:58 (UTC+01:00)'],
    ['2026-07-20T16:19:58Z', 'Europe/Berlin', '20.07.2026 18:19:58 (UTC+02:00)'],
    [null, 'Europe/Istanbul', '—'], ['', 'Europe/Istanbul', '—'],
    ['invalid', 'Europe/Istanbul', '—']
];
for (const [input, zone, expected] of samples) assert.equal(formatTimestamp(input, zone), expected);
const raw = {initial_at: samples[0][0], submitted_at: samples[1][0]};
const before = JSON.stringify(raw);
Object.values(raw).forEach(value => formatTimestamp(value));
assert.equal(JSON.stringify(raw), before);
console.log('PASS TIME 10 checks: Istanbul, rollover, UTC, DST, missing/invalid values, raw data preserved.');
