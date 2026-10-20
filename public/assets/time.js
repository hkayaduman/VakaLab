'use strict';
// Display only: stored timestamps and downloaded research data retain full UTC precision.
function formatTimestamp(value, timeZone = 'Europe/Istanbul') {
    if (!value) return '—';
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '—';
    const parts = new Intl.DateTimeFormat('tr-TR', {
        timeZone, day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit', second: '2-digit', hourCycle: 'h23',
        timeZoneName: 'longOffset'
    }).formatToParts(date);
    const part = type => parts.find(p => p.type === type).value;
    const offset = part('timeZoneName').replace(/GMT|UTC/, 'UTC').replace(/^UTC$/, 'UTC+00:00');
    return `${part('day')}.${part('month')}.${part('year')} ${part('hour')}:${part('minute')}:${part('second')} (${offset})`;
}
if (typeof module !== 'undefined') module.exports = {formatTimestamp};
