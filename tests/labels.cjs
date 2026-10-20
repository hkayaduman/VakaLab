const assert=require('node:assert/strict');
const {labelReviewState}=require('../public/assets/labels.js');
const view={label_reviews:[
    {id:'r2',message_id:'m',created_at:'2026-10-20T16:01:00Z',reason:'second'},
    {id:'r1',message_id:'m',created_at:'2026-10-20T16:00:00Z',reason:'first'}
],labels:[
    {message_id:'m',source:'researcher',review_id:'r1',definition_id:'a'},
    {message_id:'m',source:'researcher',review_id:'r1',definition_id:'b'},
    {message_id:'m',source:'researcher',review_id:'r2',definition_id:'c'},
    {message_id:'m',source:'ai',review_id:'ai',definition_id:'b'},
    {message_id:'other',source:'researcher',review_id:'other',definition_id:'d'}
]};
const before=JSON.stringify(view),state=labelReviewState(view,'m');
assert.deepEqual(state.current,['c']);
assert.deepEqual(state.ai,['b']);
assert.deepEqual(state.history[1].added,['c']);
assert.deepEqual(state.history[1].removed,['a','b']);
assert.equal(JSON.stringify(view),before);
view.label_reviews.push({id:'r3',message_id:'m',created_at:'2026-10-20T16:02:00Z'});
assert.deepEqual(labelReviewState(view,'m').current,[]);
assert.deepEqual(labelReviewState(view,'unreviewed').history,[]);
assert.deepEqual(labelReviewState({labels:[{message_id:'m',source:'researcher',review_id:'legacy',definition_id:'a',created_at:'2026-10-20T16:00:00Z'}]},'m').current,['a']);
const latestView={labels:[{id:'a',message_id:'m',source:'ai',review_id:'old',definition_id:'x',created_at:'2026-10-20T16:00:00Z'},{id:'b',message_id:'m',source:'ai',review_id:'new',definition_id:'y',created_at:'2026-10-20T16:01:00Z'}]};
assert.deepEqual(labelReviewState(latestView,'m').ai,['y']);latestView.label_batches=[{id:'empty',created_at:'2026-10-20T16:02:00Z',detail:JSON.stringify({labels:{m:[]}})}];assert.deepEqual(labelReviewState(latestView,'m').ai,[]);assert.equal(labelReviewState(latestView,'m').aiKnown,true);
console.log('PASS LABELS 11 checks: latest review, separate AI, added/removed, immutable input, empty review, message isolation, legacy rows.');
