<?php
declare(strict_types=1);
// Reopen every exported table and compare each cell to the isolated source dataset.
require __DIR__.'/../app/bootstrap.php';
require __DIR__.'/Seed.php';
$c=require __DIR__.'/../config/example.php';
$c['dsn']='sqlite::memory:';$c['db_user']='';$c['db_password']='';$c['https']=false;
$db=new Db($c);Migration::run($db);Seed::run($db,$c,uid());
$app=new App($db,$c);$export=new Export($app);$admin=new Admin($app);
$researcher=$db->one("SELECT * FROM users WHERE id='test-researcher'");
$student=$db->one("SELECT * FROM users WHERE id='test-student-1'");
$a=$app->start($student,'test-case-2');
$draft=Rules::draft(['knowledge'=>0,'confidence'=>0,'difficulty'=>10,'severity'=>0,
    'problem'=>"Türkçe ğüşöçıİ; virgül, noktalı virgül; ve \"alıntı\".\nİkinci satır.",
    'decision'=>'Öğrenciyi ve farklı koşulları dinlerim.','reason'=>'Farklı koşulları birlikte incelerim.']);
$a=$app->save($student,$a['id'],['revision'=>0,'draft'=>$draft],'initial');
$draft=array_replace($draft,['final_decision'=>'Nihai karar: eşit katılımı desteklerim.','final_reason'=>'İnternet erişimi olmayan öğrenci için farklı bir yol düşünürüm.',
    'final_confidence'=>0,'final_difficulty'=>0,'final_severity'=>0,'reflection'=>"Öneriyi kendi gerekçemle karşılaştırdım.\nTürkçe ve satır sonları korunmalı."]);
$a=$app->save($student,$a['id'],['revision'=>$a['revision'],'draft'=>$draft],'submit');
// Fixed observations exercise all seven tabs, confirmation markers and absence.
$observations=[['section_open','case',0,[]],['section_open','profile',10,[]],['section_open','initial',20,[]],
    ['workflow_confirmed','initial',30,['kind'=>'initial']],['section_open','ai',30,[]],
    ['app_presence','ai',34,['app_active'=>false]],['app_presence','ai',38,[]],
    ['section_open','final',40,[]],['section_open','reflection',50,[]],['section_open','confirm',60,[]],
    ['workflow_confirmed','confirm',70,['kind'=>'submit','submitted'=>true]]];
foreach($observations as $i=>[$type,$section,$seconds,$payload]){
    $at=gmdate('Y-m-d\TH:i:s\Z',strtotime('2026-10-20T10:00:00Z')+$seconds);
    $db->insert('events',['id'=>uid(),'app_id'=>$a['id'],'client_key'=>uid(),'tab_id'=>'roundtrip-tab','session_id'=>'roundtrip-session',
        'client_seq'=>$i+1,'client_at'=>$at,'received_at'=>$at,'type'=>$type,'section'=>$section,
        'payload'=>js($payload+['view_id'=>'roundtrip-view','app_active'=>true,'visible'=>true,'focused'=>true]),'quality'=>'synthetic_fixture']);
}
$admin->mutate($researcher,'note',['app_id'=>$a['id'],'body'=>'PRIVATE_SYNTHETIC_NOTE','reason'=>'Synthetic export verification']);
$count=0;$cells=0;
function roundtripCheck(bool $ok,string $label): void {global $count;demand($ok,$label);echo 'PASS ROUNDTRIP '.(++$count).' '.$label.PHP_EOL;}
function tableValues(array $rows): array {
    $headers=[];foreach($rows as $row)foreach(array_keys($row) as $key)if(!in_array($key,$headers,true))$headers[]=$key;
    $values=[];foreach($rows as $row){$line=[];foreach($headers as $key){$value=$row[$key]??'';$line[]=is_array($value)?json_encode($value,JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR):(string)$value;}$values[]=$line;}
    return [$headers,$values];
}
$dir=__DIR__.'/tmp/export-roundtrip-'.uid();demand(mkdir($dir,0700,true),'Cannot create isolated output directory');
$before=[];foreach(['applications','snapshots','messages','events','reports','draft_revisions','research_notes'] as $table)$before[$table]=$db->all('SELECT * FROM '.$table.' ORDER BY id');
$summary=[];
foreach(['raw'=>0,'coded'=>1] as $mode=>$coded){
    $data=$export->dataset($researcher,['include_test'=>1,'include_practice'=>1,'include_excluded'=>1,'coded'=>$coded]);
    $visits=array_values(array_filter($data['navigation_visits'],fn($row)=>$row['app_id']===$a['id']));
    roundtripCheck(array_sum(array_column($visits,'duration_seconds'))===70.0,$mode.' known seven-tab journey totals exactly 70 seconds');
    roundtripCheck(array_sum(array_column(array_filter($visits,fn($row)=>$row['section']==='outside'),'duration_seconds'))===4.0,$mode.' outside time remains exactly four separate seconds');
    roundtripCheck(count(array_filter($data['navigation_milestones'],fn($row)=>$row['app_id']===$a['id']))===2,$mode.' initial and submission markers retained');
    $jsonPath=$dir.'/'.$mode.'.json';file_put_contents($jsonPath,js($data));
    roundtripCheck(json_decode(file_get_contents($jsonPath),true,512,JSON_THROW_ON_ERROR)==$data,$mode.' JSON preserves all tables and values');
    Export::xlsx($data,$dir.'/'.$mode.'.xlsx');
    $book=\PhpOffice\PhpSpreadsheet\IOFactory::load($dir.'/'.$mode.'.xlsx');
    roundtripCheck($book->getSheetCount()===count($data),$mode.' Excel contains every dataset table');
    $summary[$mode]=[];
    foreach($data as $name=>$rows){
        [$headers,$expected]=tableValues($rows);
        $csv=Export::csv($rows);file_put_contents($dir.'/'.$mode.'-'.$name.'.csv',$csv);
        roundtripCheck(str_starts_with($csv,"\xEF\xBB\xBF"),$mode.'/'.$name.' CSV has UTF-8 BOM');
        $stream=fopen('php://temp','w+');fwrite($stream,substr($csv,3));rewind($stream);
        $csvHeaders=fgetcsv($stream,0,',','"','');$actual=[];
        while(($line=fgetcsv($stream,0,',','"',''))!==false)$actual[]=$line;fclose($stream);
        if(!$headers)$csvHeaders=[];
        roundtripCheck($csvHeaders===$headers&&count($actual)===count($rows),$mode.'/'.$name.' CSV columns and row count');
        foreach($actual as $ri=>&$line)foreach($line as $ci=>&$value){if($value==="'".$expected[$ri][$ci])$value=substr($value,1);}unset($line,$value);
        roundtripCheck($actual===$expected,$mode.'/'.$name.' CSV every cell matches source');
        $sheet=$book->getSheetByName(substr($name,0,31));demand($sheet!==null,'Missing worksheet '.$name);
        $actualExcel=[];
        if($headers){
            $last=\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
            $matrix=$sheet->rangeToArray('A1:'.$last.(count($rows)+1),null,false,false);
            foreach($matrix as $line)$actualExcel[]=array_map(fn($value)=>(string)($value??''),$line);
            roundtripCheck(array_shift($actualExcel)===$headers,$mode.'/'.$name.' Excel column names');
            roundtripCheck($actualExcel===$expected,$mode.'/'.$name.' Excel every cell matches source');
            $cells+=count($headers)*count($rows);
        }else roundtripCheck($sheet->getHighestDataRow()===1,$mode.'/'.$name.' empty Excel sheet remains empty');
        $summary[$mode][$name]=['rows'=>count($rows),'columns'=>count($headers)];
    }
    $book->disconnectWorksheets();
    if($coded)roundtripCheck(!str_contains(js($data),'PRIVATE_SYNTHETIC_NOTE')&&!str_contains(js($data),'TEST-OGR-01')&&!str_contains(js($data),$draft['problem']),'coded exports exclude private note identity and answer text');
    else{
        $row=current(array_filter($data['applications'],fn($row)=>$row['id']===$a['id']));
        roundtripCheck(obj($row['draft'])===$draft,'raw export preserves exact submitted answers and zero measurements');
        $snapshot=current(array_filter($data['snapshots'],fn($row)=>$row['app_id']===$a['id']&&$row['kind']==='submit'));
        $body=obj($snapshot['body']);roundtripCheck($body['final_confidence']===0&&$body['final_difficulty']===0&&$body['final_severity']===0,'submission snapshot retains real zero values');
    }
}
$formulaRows=[['value'=>'=1+1'],['value'=>'+SUM(A1)'],['value'=>'@SUM(A1)'],['value'=>'-1+1']];
$csv=Export::csv($formulaRows);$stream=fopen('php://temp','w+');fwrite($stream,substr($csv,3));rewind($stream);fgetcsv($stream,0,',','"','');
foreach($formulaRows as $row)roundtripCheck(fgetcsv($stream,0,',','"','')[0]==="'".$row['value'],'CSV spreadsheet formula escaped: '.$row['value']);fclose($stream);
foreach($before as $table=>$rows)roundtripCheck($db->all('SELECT * FROM '.$table.' ORDER BY id')===$rows,'export preserves source table '.$table);
$summary=['checks'=>$count,'compared_excel_data_cells'=>$cells,'tables'=>$summary,'working_database_opened'=>false,'provider'=>'mock','outputs'=>$dir];
file_put_contents($dir.'/summary.json',js($summary));echo js($summary).PHP_EOL;
echo 'TOTAL EXPORT ROUNDTRIP '.$count.' checks passed.'.PHP_EOL;
