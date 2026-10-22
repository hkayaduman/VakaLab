// Drive the rubric controls as a researcher; never write the calculated total.
module.exports=async function selectQuestionScore(page,score){
    const assert=require('node:assert/strict');
    const level=score===2?'Gelişiyor':'Ayrıntılı';
    const selects=page.locator('dialog select[id^="review-v2-0-"][id$="-level"]');
    assert.ok(await selects.count()>0);
    for(const select of await selects.all())await select.selectOption(level);
    assert.equal(await page.locator('#review-v2-score-0').getAttribute('readonly'),'');
    assert.equal(Number(await page.locator('#review-v2-score-0').inputValue()),score);
};
