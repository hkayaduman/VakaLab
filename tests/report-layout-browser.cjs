const fs=require('node:fs'),path=require('node:path');
const {chromium}=require(process.env.PLAYWRIGHT_PATH||'playwright');
const root=path.resolve(__dirname,'..'),tmp=path.join(__dirname,'tmp');
const source=fs.readFileSync(path.join(root,'public/assets/app.js'),'utf8');
const render=new Function('esc',source.slice(source.indexOf('function studentReportHtml('),source.indexOf('\nfunction reportLinks('))+';return studentReportHtml;')(value=>String(value??'').replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])));
const fixture=JSON.parse(fs.readFileSync(path.join(tmp,'report-layout-fixture.json'),'utf8'));
const check=(ok,label)=>{if(!ok)throw new Error(label);console.log('PASS '+label);};
(async()=>{
 const browser=await chromium.launch({headless:true,executablePath:process.env.CHROME_BIN||undefined});
 try{
  const page=await browser.newPage();
  const css=fs.readFileSync(path.join(root,'public/assets/app.css'),'utf8');
  await page.setContent(`<html lang="tr"><style>${css}</style><main>${render(fixture)}</main></html>`);
  check((await page.locator('.report-total').innerText()).includes('Toplam alınan puan: 7 / 24 puan'),'overall score clearly labelled');
  check(await page.locator('.report-answer').count()===6,'each question includes student answer');
  check(await page.locator('.report-criterion .report-feedback').count()===6,'each question includes labelled evaluation comment');
  check((await page.locator('.report-question-score').first().innerText()).includes('Bu sorudan alınan puan: 1 / 4 puan'),'question score clearly labelled');
  check((await page.locator('.report-section-total').first().innerText()).includes('Bu bölümden alınan toplam puan: 3 / 12 puan'),'section total clearly labelled');
  for(const width of [1440,390]){
   await page.setViewportSize({width,height:1000});
   check(await page.evaluate(()=>document.documentElement.scrollWidth<=innerWidth+1),`report fits ${width}px`);
   await page.screenshot({path:path.join(tmp,`report-labels-${width}.png`),fullPage:true});
  }
  const partial=structuredClone(fixture);partial.report.sections.reflection.total=null;
  check(render(partial).includes('Toplam alınan puan: Puanlanmadı'),'unscored section does not produce partial aggregate');
  const unsafe=structuredClone(fixture);unsafe.report.sections.initial.criteria[0].answer='<img src=x onerror=alert(1)>';
  check(render(unsafe).includes('&lt;img src=x onerror=alert(1)&gt;'),'student answers are escaped');
 }finally{await browser.close();}
})().catch(error=>{console.error(error);process.exitCode=1;});
