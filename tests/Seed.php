<?php
declare(strict_types=1);
final class Seed {
    public static function run(Db $db,array $c,string $password): void {
        $db->tx(function()use($db,$c,$password){
            $hash=password_hash($password,PASSWORD_DEFAULT);
            $add=function(string $table,array $row)use($db){if(in_array($table,['cases','definitions'],true)||($table==='users'&&$row['role']==='student'))$row['owner_id']='test-researcher';if(!$db->one('SELECT id FROM '.$table.' WHERE id=?',[$row['id']]))$db->insert($table,$row);};
            $add('users',['id'=>'test-researcher','login'=>'TEST-ARASTIRMACI','name'=>'Sentetik Araştırmacı','password'=>$hash,'role'=>'researcher','status'=>'active','metadata'=>'{}','test'=>1,'created_at'=>now()]);
            for($i=1;$i<=7;$i++)$add('users',['id'=>'test-student-'.$i,'login'=>'TEST-OGR-0'.$i,'name'=>'Sentetik Katılımcı '.$i,'password'=>$hash,'role'=>'student','status'=>$i===7?'frozen':'active','metadata'=>js(['program'=>'TEST Program','year'=>'3','consent'=>'SENTETİK onam, gerçek katılımcı yok']),'test'=>1,'created_at'=>now()]);
            $rubric=['text'=>'TEST RUBRİĞİ — gerçek araştırmada kullanılmaz.','dimensions'=>[['id'=>'reasoning','name'=>'Gerekçelendirme','min'=>0,'max'=>4,'weight'=>2,'levels'=>[['name'=>'Yetersiz','score'=>0,'description'=>'Kanıt yok'],['name'=>'Gelişiyor','score'=>2,'description'=>'Kısmi'],['name'=>'Ayrıntılı','score'=>4,'description'=>'Açık gerekçe']]],['id'=>'perspectives','name'=>'Perspektifler','min'=>0,'max'=>4,'weight'=>1,'levels'=>[['name'=>'Yetersiz','score'=>0,'description'=>'Kanıt yok'],['name'=>'Gelişiyor','score'=>2,'description'=>'Kısmi'],['name'=>'Ayrıntılı','score'=>4,'description'=>'Çoklu perspektif']]]]];
            $add('definitions',['id'=>'test-rubric-v1','family'=>'rubric','name'=>'TEST RUBRİĞİ','version'=>1,'body'=>js($rubric),'archived'=>0,'created_at'=>now()]);
            foreach(['Bilgi istedi','Açıklama istedi','Metin düzenletmek istedi','Eleştiri istedi','Alternatif fikir istedi','Karar istedi','Diğer','Belirsiz'] as $i=>$name)$add('definitions',['id'=>'test-label-'.$i,'family'=>'label','name'=>$name,'version'=>1,'body'=>js(['description'=>'SENTETİK başlangıç tanımı','target_role'=>'user','topic_scope'=>'pedagogical']),'archived'=>0,'created_at'=>now()]);
            $texts=[['Grup çalışmasında adalet','Grup çalışmasında bir öğrenci görevlerin çoğunu üstlenirken iki öğrenci katılamamıştır. Bireysel not ile ortak ürün değerlendirmesi arasında bir karar vermeniz gerekiyor. Öğrencilerin gerekçeleri henüz dinlenmemiştir.'],['Sınıfta dijital araç kullanımı','Bir öğrenci evinde internete erişemediği için dijital ödevi tamamlayamamıştır. Bazı öğrenciler geç teslimin ortak kuralları zayıflatacağını düşünür.'],['Akranlar arası anlaşmazlık','İki öğrencinin tartışması sınıfa yayılmıştır. Öğrenciler birbirinden farklı olay anlatımları sunar. Öğretmen olaya doğrudan tanık olmamıştır.'],['Ölçme ve geri bildirim','Sınav sonuçları düşük olan sınıfta bazı öğrenciler soruların anlaşılmadığını söylüyor. Yeniden değerlendirme ve öğretim desteği seçeneklerini inceleyin.'],['Katılım ve kapsayıcılık','Bir öğrenci sınıf önünde konuşmaktan kaçınırken küçük grupta katkı vermektedir. Sözlü katılım ölçütünü belirlemeniz isteniyor.'],['Deneme: sınıf kitaplığı','Sınıf kitaplığındaki kitapları paylaşmak için bir yöntem önerin. Bu vaka araştırma dışıdır.']];
            foreach($texts as $i=>[$title,$text]){$cid='test-case-'.($i+1);$vid=$cid.'-v1';$add('cases',['id'=>$cid,'title'=>'SENTETİK · '.$title,'position'=>$i+1,'status'=>$i===5?'draft':'active','practice'=>$i===5?1:0,'test'=>1,'current_version'=>$vid,'stop_access'=>0,'rubric_id'=>'test-rubric-v1']);$add('case_versions',['id'=>$vid,'case_id'=>$cid,'version'=>1,'body'=>'[SENTETİK VAKA] '.$text,'instructions'=>'Vakadaki kararınızı ve gerekçenizi kendi görüşünüzle açıklayınız.','notes'=>'Araştırmacıya özel SENTETİK not; AI sohbetine gitmez.','created_at'=>now()]);
                for($j=1;$j<=7;$j++)if(!($j===1&&$i===4))$add('assignments',['id'=>'test-assign-'.$j.'-'.($i+1),'user_id'=>'test-student-'.$j,'case_id'=>$cid,'active'=>1]);
            }
        });
        // Only first invocation populates workflow; reruns never edit existing responses.
        $app=new App($db,$c);
        for($i=2;$i<=6;$i++){
            if($db->one('SELECT id FROM applications WHERE user_id=? AND case_id=?',['test-student-'.$i,'test-case-1']))continue;
            $u=$db->one('SELECT * FROM users WHERE id=?',['test-student-'.$i]);$a=$app->start($u,'test-case-1');$d=Rules::draft(['knowledge'=>5,'confidence'=>8,'difficulty'=>6,'severity'=>7,'problem'=>'SENTETİK: Grup içi katkıları ve koşulları anlamalıyım.','decision'=>'SENTETİK: Öğrencileri ayrı dinlerim.','reason'=>'SENTETİK: Adil karar için farklı bilgileri toplarım.']);
            $a=$app->save($u,$a['id'],['revision'=>0,'draft'=>$d],$i===2?'save':'initial');
            if($i>=4)$app->chat($u,$a['id'],['key'=>'seed-request-test-'.$i,'text'=>'SENTETİK: Durumun olası sonuçları neler olabilir?']);
            if($i>=5){$d['final_decision']='SENTETİK: Öğrencileri dinleyip bireysel katkıyı ayrı değerlendiririm.';$d['final_reason']='SENTETİK: Ortak ürün ile bireysel koşulları birlikte dikkate alırım.';$d['final_confidence']=10;$d['final_difficulty']=4;$d['final_severity']=7;$d['reflection']='SENTETİK: AI önerisini gözden geçirip kendi kararımı gerekçelendirdim.';$app->save($u,$a['id'],['revision'=>$a['revision'],'draft'=>$d],$i===6?'submit':'save');}
        }
        $a=$db->one("SELECT * FROM applications WHERE user_id='test-student-6' AND case_id='test-case-1'");$researcher=$db->one("SELECT * FROM users WHERE id='test-researcher'");
        if(!$db->one('SELECT id FROM analysis_runs WHERE app_id=?',[$a['id']])){
            $analysis=new Analysis($app);$run=$analysis->newRun($researcher,$a['id'],'test-rubric-v1');
            for($n=1;$n<=8;$n++){$s=$analysis->step($researcher,$run['id'],$n);demand($s['status']==='completed','Seed analizi başarısız');if($n>=3&&$n<=7)$analysis->decide($researcher,$s['id'],['action'=>'accept','reason'=>'SENTETİK test kararı']);}
            $r=$analysis->report($researcher,'create',['run_id'=>$run['id']]);$analysis->report($researcher,'approve',['id'=>$r['id']]);$analysis->report($researcher,'create',['run_id'=>$run['id']]);
            $mid=$db->one('SELECT id FROM messages WHERE app_id=? AND role=?',[$a['id'],'user'])['id'];
            $db->insert('message_labels',['id'=>uid(),'message_id'=>$mid,'definition_id'=>'test-label-0','source'=>'ai','review_id'=>$run['id'],'actor'=>$researcher['id'],'reason'=>'SENTETİK AI önerisi','created_at'=>now()]);
            (new Admin($app))->mutate($researcher,'labels',['message_id'=>$mid,'labels'=>['test-label-1','test-label-4'],'reason'=>'SENTETİK farklı çoklu araştırmacı kararı']);
            $batch=[];$time=time()-120;foreach(['section_open','heartbeat','visibility','section_open','copy','offline','online','recovered','page_exit'] as $n=>$type)$batch[]=['key'=>'synthetic-event-'.str_pad((string)$n,8,'0',STR_PAD_LEFT),'type'=>$type,'section'=>$n<3?'ai':'final','tab'=>'synthetic-tab','seq'=>$n+1,'at'=>gmdate('Y-m-d\TH:i:s\Z',$time+$n*10),'payload'=>['synthetic'=>true,'visible'=>$n!==2,'focused'=>true,'interactive'=>true,'source'=>'test-case-1-v1','start'=>0,'end'=>12,'quote'=>'SENTETİK']];
            (new Telemetry($app))->ingest($db->one("SELECT * FROM users WHERE id='test-student-6'"),$a['id'],['events'=>$batch],'synthetic-session');
        }
    }
}
