<?php
declare(strict_types=1);
final class Export {
    public function __construct(private App $app) {}
    public function reportData(array $u,string $id): array {
        $db=$this->app->db;$r=$db->one('SELECT * FROM reports WHERE id=?',[$id]);demand((bool)$r,'Rapor yok',404);$this->app->access($u,$r['app_id']);
        if($u['role']!=='researcher')demand($r['approved_at']&&$r['published_at']&&$db->one('SELECT closed_at FROM study WHERE id=1')['closed_at'],'Rapor henüz yayımlanmadı',403);
        $body=obj($r['body']);if($u['role']!=='researcher')unset($body['internal']);return ['id'=>$r['id'],'version'=>$r['version'],'created_at'=>$r['created_at'],'approved_at'=>$r['approved_at'],'published_at'=>$r['published_at'],'report'=>$body];
    }
    public function dataset(array $u,array $p): array {
        Auth::researcher($u);$db=$this->app->db;$where=['1=1'];$args=[];
        if(empty($p['include_test']))$where[]='u.test=0 AND c.test=0';if(empty($p['include_practice']))$where[]='c.practice=0';if(empty($p['include_excluded']))$where[]='a.excluded=0';
        if(!empty($p['app_id'])){$where[]='a.id=?';$args[]=$p['app_id'];}if(!empty($p['user_id'])){$where[]='a.user_id=?';$args[]=$p['user_id'];}
        $apps=$db->all('SELECT a.*,u.login,u.name FROM applications a JOIN users u ON u.id=a.user_id JOIN cases c ON c.id=a.case_id WHERE '.implode(' AND ',$where),$args);$coded=!empty($p['coded']);
        $out=['applications'=>[],'events'=>[],'server_events'=>[],'visits'=>[],'messages'=>[],'labels'=>[],'label_reviews'=>[],'drafts'=>[],'snapshots'=>[],'ai_requests'=>[],'ai_attempts'=>[],'analyses'=>[],'decisions'=>[],'definitions'=>[],'dictionary'=>[['field'=>'times','meaning'=>'UTC ISO 8601; client_at ayrı, received_at sunucu'],['field'=>'quality','meaning'=>'Gözlenen görünürlük okuma/düşünme değildir; AI süreleri öğrenciyle toplanmaz'],['field'=>'coded','meaning'=>'Kodlanmış çıktı metinleri ve kimlik eşlemesini içermez']]];
        $out['dictionary']=array_merge($out['dictionary'],EventLabels::dictionary());
        foreach($apps as $a){$id=$a['id'];$code='P-'.substr(hash('sha256',$a['user_id']),0,12);$out['applications'][]=$coded?['app_id'=>$id,'participant'=>$code,'case_id'=>$a['case_id'],'state'=>$a['state'],'revision'=>$a['revision'],'submitted_at'=>$a['submitted_at']]:$a;
            foreach($db->all('SELECT id,action,target,detail,created_at FROM audit WHERE target=? ORDER BY created_at',[$id]) as $serverEvent){if($coded)$serverEvent['detail']='[çıkarıldı]';$out['server_events'][]=$serverEvent;}
            foreach($db->all('SELECT t.* FROM ai_attempts t JOIN ai_requests r ON r.id=t.request_id WHERE r.app_id=?',[$id]) as $attempt)$out['ai_attempts'][]=$attempt;
            $events=$db->all('SELECT * FROM events WHERE app_id=? ORDER BY received_at,client_seq',[$id]);foreach($events as $e){if($coded){$e['payload']='[metin ve cihaz ayrıntıları çıkarıldı]';unset($e['session_id'],$e['tab_id']);}$out['events'][]=EventLabels::describe($e);}
            foreach(Telemetry::visits($events)['visits'] as $v)$out['visits'][]=['app_id'=>$id]+$v;
            foreach($db->all('SELECT * FROM messages WHERE app_id=? ORDER BY seq',[$id]) as $m){if($coded)$m['content']='[kodlanmış çıktıda metin çıkarıldı]';$out['messages'][]=$m;
                foreach($db->all('SELECT * FROM message_labels WHERE message_id=?',[$m['id']]) as $l){if($coded){unset($l['actor']);$l['reason']='[çıkarıldı]';}$out['labels'][]=$l;}
                foreach($db->all('SELECT * FROM label_reviews WHERE message_id=?',[$m['id']]) as $review){if($coded){unset($review['actor']);$review['reason']='[çıkarıldı]';}$out['label_reviews'][]=$review;}}
            foreach(['draft_revisions'=>'drafts','snapshots'=>'snapshots','ai_requests'=>'ai_requests','analysis_runs'=>'analyses'] as $table=>$key){foreach($db->all('SELECT * FROM '.$table.' WHERE app_id=?',[$id]) as $row){if($coded){foreach(['body','payload','error'] as $k)if(isset($row[$k]))$row[$k]='[çıkarıldı]';}$out[$key][]=$row;}}
            foreach($db->all('SELECT d.* FROM decisions d JOIN analysis_steps s ON s.id=d.step_id JOIN analysis_runs r ON r.id=s.run_id WHERE r.app_id=?',[$id]) as $d){if($coded){unset($d['actor']);$d['body']='[çıkarıldı]';$d['reason']='[çıkarıldı]';}$out['decisions'][]=$d;}
            foreach($db->all('SELECT s.* FROM analysis_steps s JOIN analysis_runs r ON r.id=s.run_id WHERE r.app_id=?',[$id]) as $s){if($coded){$s['input']='[çıkarıldı]';$s['output']='[çıkarıldı]';}$out['analyses'][]=$s;}
        }
        if(!$coded)$out['definitions']=$db->all('SELECT * FROM definitions');$db->audit($u['id'],'export','dataset','',['coded'=>$coded,'count'=>count($apps),'include_test'=>!empty($p['include_test'])]);return $out;
    }
    public static function csv(array $rows): string {
        $f=fopen('php://temp','w+');fwrite($f,"\xEF\xBB\xBF");$keys=[];foreach($rows as $r)$keys=array_values(array_unique(array_merge($keys,array_keys($r))));fputcsv($f,$keys,',','"','');
        foreach($rows as $r)fputcsv($f,array_map(fn($k)=>Rules::csv(is_array($r[$k]??null)?js($r[$k]):(string)($r[$k]??'')),$keys),',','"','');rewind($f);return stream_get_contents($f);
    }
    public static function xlsx(array $data,string $path): void {
        demand(class_exists('PhpOffice\\PhpSpreadsheet\\Spreadsheet'),'Vendor kurulmadı',503);$book=new \PhpOffice\PhpSpreadsheet\Spreadsheet();$index=0;
        foreach($data as $name=>$rows){$sheet=$index++===0?$book->getActiveSheet():$book->createSheet();$sheet->setTitle(substr($name,0,31));$keys=[];foreach($rows as $r)$keys=array_values(array_unique(array_merge($keys,array_keys($r))));$all=[$keys];foreach($rows as $r)$all[]=array_map(fn($k)=>is_array($r[$k]??null)?js($r[$k]):(string)($r[$k]??''),$keys);
            foreach($all as $ri=>$row)foreach($row as $ci=>$value)$sheet->setCellValueExplicit([$ci+1,$ri+1],$value,\PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);$sheet->freezePane('A2');$sheet->getDefaultColumnDimension()->setWidth(24);}
        (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($book))->save($path);$book->disconnectWorksheets();clearstatcache(true,$path);
    }
    public static function reportFile(array $reports,string $format,string $path): void {
        if($format==='docx'){
            demand(class_exists('PhpOffice\\PhpWord\\PhpWord'),'Vendor kurulmadı',503);\PhpOffice\PhpWord\Settings::setOutputEscapingEnabled(true);$w=new \PhpOffice\PhpWord\PhpWord();$w->setDefaultFontName('DejaVu Sans');$w->setDefaultFontSize(11);
            foreach($reports as $r){$s=$w->addSection();$s->addText('VakaLab — Vaka Raporu',['bold'=>true,'size'=>18]);foreach(self::blocks($r) as $block){if($block['type']==='table'){$t=$s->addTable(['borderSize'=>4,'borderColor'=>'DCE4DE','cellMargin'=>90]);foreach($block['rows'] as $row){$t->addRow();foreach($row as $cell)$t->addCell(2400)->addText((string)$cell);}}else $s->addText($block['text'],$block['type']==='heading'?['bold'=>true,'size'=>13]:[]);}}(new \PhpOffice\PhpWord\Writer\Word2007($w))->save($path);
        }elseif($format==='pdf'){
            demand(class_exists('Mpdf\\Mpdf'),'Vendor kurulmadı',503);$m=new \Mpdf\Mpdf(['tempDir'=>__DIR__.'/../storage','default_font'=>'dejavusans','default_font_size'=>10]);$escape=fn($x)=>htmlspecialchars((string)$x,ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8');$html='<style>h1{color:#183b40;font-size:22pt}h2{color:#246852;font-size:13pt;margin-top:22px}p{line-height:1.6}table{border-collapse:collapse;width:100%;margin:12px 0}td{border:1px solid #dce4de;padding:9px}tr:first-child{background:#edf3ef;font-weight:bold}</style>';foreach($reports as $i=>$r){if($i)$html.='<pagebreak />';$html.='<h1>VakaLab — Vaka Raporu</h1>';foreach(self::blocks($r) as $b){if($b['type']==='table'){$html.='<table>';foreach($b['rows'] as $row){$html.='<tr>';foreach($row as $cell)$html.='<td>'.$escape($cell).'</td>';$html.='</tr>';}$html.='</table>';}else{$tag=$b['type']==='heading'?'h2':'p';$html.="<$tag>".nl2br($escape($b['text']))."</$tag>";}}}$m->WriteHTML($html);$m->Output($path,\Mpdf\Output\Destination::FILE);
        }else throw new Fault('Dosya biçimi geçersiz');
    }
    private static function blocks(array $r): array {
        $d=$r['report'];$b=[];$p=function(string $s)use(&$b){$b[]=['type'=>'paragraph','text'=>$s];};$h=function(string $s)use(&$b){$b[]=['type'=>'heading','text'=>$s];};$score=fn($x)=>$x===null?'Karar verilemedi':(string)$x;
        $p('Rapor sürümü: '.$r['version'].' · Oluşturma (UTC): '.$r['created_at']);$h('Katılımcı ve vaka');$p('Katılımcı: '.($d['participant']??'—').' · '.($d['case_title']??'Vaka').' · Vaka sürümü: '.($d['case_version']??'—'));$p('Rubrik: '.($d['rubric_name']??'—').' · Sürüm: '.($d['rubric_number']??'—'));$p($d['notice']);
        $h('Veri bütünlüğü ve teknik sınırlar');$p($d['limitations']??'Teknik ölçümler sınırlıdır.');$h('Başlangıç profili');$labels=['knowledge'=>'Ön bilgi','confidence'=>'Başlangıç güveni','difficulty'=>'Algılanan zorluk','severity'=>'Algılanan ciddiyet'];foreach($d['profile']??[] as $k=>$v)$p(($labels[$k]??$k).': '.$v.' / 10');
        $h('Başlangıç ve nihai güven');$c=$d['confidence'];$b[]=['type'=>'table','rows'=>[['Başlangıç','Nihai','Fark'],[$c['initial_confidence'],$c['final_confidence'],sprintf('%+d',$c['difference'])]]];
        $titles=[3=>'Başlangıç rubriği',4=>'Nihai rubriği',5=>'Dönüşüm karşılaştırması',6=>'Etkileşim ve etiketler',7=>'Yansıtma karşılaştırması'];$actions=['accept'=>'Kabul edildi','edit'=>'Düzenlenerek onaylandı','reject'=>'Reddedildi','uncertain'=>'Karar verilemedi'];$dims=array_column($d['dimensions']??[],'name','id');
        foreach($d['parts'] as $part){$h($titles[$part['step']]);$p('Araştırmacı kararı: '.$actions[$part['action']]);$approved=$part['approved'];if(isset($approved['scores'])){$rows=[['Boyut','Onaylı puan','Düzey']];foreach($approved['scores'] as $key=>$s){$rows[]=[$dims[$key]??$key,$s['score'],$s['level']];}$b[]=['type'=>'table','rows'=>$rows];foreach($approved['scores'] as $key=>$s){$p(($dims[$key]??$key).' — '.$s['reason']);$p('Kanıt: “'.$s['quote'].'”');}$p('Ağırlıklı ortalama: '.$score($approved['weighted_mean']??null));}elseif(isset($approved['summary'])){$p($approved['summary']);foreach($approved['evidence']??[] as $e)$p('Kanıt: “'.$e['quote'].'”');}$p('Karar gerekçesi: '.$part['reason']);}
        $h('Onaylı puan karşılaştırması');$b[]=['type'=>'table','rows'=>[['Başlangıç ortalaması','Nihai ortalama','Fark'],[$score($d['initial_mean']),$score($d['final_mean']),$score($d['difference'])]]];
        if(isset($d['internal'])){$h('Araştırmacı iç eki — öğrenci kopyasına dahil edilmez');foreach($d['internal']['notes'] as $note){$p('İç not: '.$note['body']);$p('Gerekçe: '.$note['reason'].' · '.$note['created_at']);}foreach($d['internal']['suggestions'] as $s){$p('Adım '.$s['step'].' · Deneme '.$s['attempt'].' · '.$s['status']);$p('AI önerisi / uygulama hesabı: '.$s['output']);}}
        if(isset($d['unicode_long_text'])){$h('Uzun metin sınama eki (sentetik)');$p($d['unicode_long_text']);}return $b;
    }
}
