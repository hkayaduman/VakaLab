const {chromium}=require(process.env.PLAYWRIGHT_PATH||'playwright');
const path=require('node:path');
const assert=require('node:assert/strict');
const root=path.resolve(__dirname,'..');
(async()=>{
 const browser=await chromium.launch({headless:true,executablePath:process.env.CHROME_BIN||undefined});
 let checks=0;
 try {
  const page=await browser.newPage();
  await page.setContent('<main><section class="card" id="fixture"></section></main>');
  await page.addStyleTag({path:path.join(root,'public/assets/app.css')});
  await page.addScriptTag({content:`const esc=s=>String(s).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));const displayTime=s=>esc(s);`});
  await page.addScriptTag({path:path.join(root,'public/assets/process.js')});
  for(const width of [1440,768,390]){
   await page.setViewportSize({width,height:900});
   for(const kind of ['paragraph','unbroken']){
    await page.evaluate(kind=>{
     const text=kind==='paragraph'?('Uzun yanıt. Gerekçe ve değerlendirme.\n').repeat(1000):'A'.repeat(60000);
     window.expectedText=text;
     document.querySelector('#fixture').innerHTML=processAnswers({knowledge:0,confidence:10,difficulty:5,severity:6,final_confidence:8,final_severity:7},['knowledge','confidence','difficulty','severity','final_confidence','final_severity'])+processSnapshotComparison([{kind:'initial',created_at:'21.10.2026',body:{decision:text,reason:text}},{kind:'submit',created_at:'21.10.2026',body:{final_decision:text,final_reason:text,reflection:text}}]);
    },kind);
    await page.evaluate(()=>{document.querySelector('#fixture').insertAdjacentHTML('beforeend',processRevisionTable([{key:'reason',before:window.expectedText,after:window.expectedText,comparable:true,revision:2,at:'21.10.2026'}]));document.querySelectorAll('.revision-full-text').forEach(el=>el.open=true);document.querySelector('#fixture').insertAdjacentHTML('beforeend',processEventTable([{client_at:'21.10.2026',received_at:'21.10.2026',section:'final',type_tr:'Kopyalama gözlendi',event_description:'Uygulama içinde kopyalama olayı gözlendi.',payload:{quote:window.expectedText}}]));document.querySelectorAll('.event-table details').forEach(el=>el.open=true);});
    const result=await page.evaluate(()=>({
     pageFits:document.documentElement.scrollWidth<=innerWidth+1,
     cellsFit:[...document.querySelectorAll('td,th')].every(el=>el.scrollWidth<=el.clientWidth+1),
     textPreserved:document.querySelector('.snapshot-comparison tbody tr:nth-child(6) td').textContent===window.expectedText,
     grows:document.querySelector('.snapshot-comparison tbody tr:nth-child(6)').getBoundingClientRect().height>100,
     scrollLocal:innerWidth>700||[...document.querySelectorAll('.table-wrap')].some(el=>el.scrollWidth>el.clientWidth&&getComputedStyle(el).overflowX==='auto')
    }));
    for(const [name,ok] of Object.entries(result)){assert.ok(ok,`${width} ${kind}: ${name}`);checks++;}
    console.log(`PASS ${width}px ${kind}: wraps, full text, page fits, local scroll`);
   }
  }
  console.log(`TOTAL TABLE LAYOUT ${checks} checks passed. Synthetic DOM only; no database access.`);
 } finally {await browser.close();}
})().catch(e=>{console.error(e);process.exitCode=1;});
