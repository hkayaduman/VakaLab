<?php
declare(strict_types=1);
final class Admin {
    public function __construct(private App $app) {}
    public function dashboard(array $u): array {
        Auth::researcher($u);$db=$this->app->db;
        $cases=$db->all('SELECT c.*,v.body,v.instructions,v.notes,v.version FROM cases c JOIN case_versions v ON v.id=c.current_version WHERE c.owner_id=? ORDER BY position',[$u['id']]);
        $cids=array_column($cases,'id');$inCase=fn($row)=>in_array($row['case_id'],$cids,true);
        $assignments=array_values(array_filter($db->all('SELECT * FROM assignments'),$inCase));
        $apps=array_values(array_filter($db->all('SELECT id,user_id,case_id,version_id,state,initial_at,submitted_at,last_section,last_activity,active_request,excluded FROM applications'),$inCase));
        $aids=array_column($apps,'id');$inApp=fn($row)=>in_array($row['app_id'],$aids,true);
        $users=$db->all('SELECT id,login,name,role,status,metadata,test,owner_id FROM users ORDER BY login');
        $users=array_values(array_filter($users,fn($row)=>$row['owner_id']===$u['id']||$row['id']===$u['id']));
        foreach($users as &$row)if($row['owner_id']!==$u['id'])$row['metadata']='{}';unset($row);
        $defids=array_column($cases,'rubric_id');
        foreach($db->all('SELECT ml.definition_id,a.case_id FROM message_labels ml JOIN messages m ON m.id=ml.message_id JOIN applications a ON a.id=m.app_id') as $row)if($inCase($row))$defids[]=$row['definition_id'];
        foreach($db->all('SELECT r.rubric_id,a.case_id FROM analysis_runs r JOIN applications a ON a.id=r.app_id') as $row)if($inCase($row))$defids[]=$row['rubric_id'];
        $defs=array_values(array_filter($db->all('SELECT * FROM definitions ORDER BY created_at,version'),fn($row)=>$row['owner_id']===$u['id']||in_array($row['id'],$defids,true)));
        $targets=array_merge($cids,$aids,array_column($assignments,'id'),array_column($defs,'id'));
        return ['users'=>$users,'cases'=>$cases,'versions'=>array_values(array_filter($db->all('SELECT id,case_id,version FROM case_versions ORDER BY case_id,version'),$inCase)),
            'assignments'=>$assignments,'applications'=>$apps,
            'analysis_status'=>array_values(array_filter($db->all("SELECT r.app_id,MAX(CASE WHEN s.status='completed' THEN s.step ELSE 0 END) completed_step FROM analysis_runs r LEFT JOIN analysis_steps s ON s.run_id=r.id GROUP BY r.app_id"),$inApp)),
            'report_status'=>array_values(array_filter($db->all('SELECT app_id,MAX(approved_at) approved_at,MAX(published_at) published_at FROM reports GROUP BY app_id'),$inApp)),
            'technical'=>array_values(array_filter($db->all("SELECT DISTINCT app_id FROM ai_requests WHERE status IN ('failed','partial')"),$inApp)),
            'definitions'=>$defs,'audit'=>array_slice(array_values(array_filter($db->all('SELECT * FROM audit ORDER BY created_at DESC'),fn($row)=>$row['actor']===$u['id']||in_array($row['target'],$targets,true))),0,100)];
    }
    public function mutate(array $u,string $op,array $p): array {
        Auth::researcher($u);$db=$this->app->db;$reason=trim($p['reason']??'');if(in_array($op,['labels','note','exclude','resolve_request','resolve_analysis'],true))demand($reason!=='','İşlem gerekçesi gerekli');
        return $db->tx(function()use($db,$u,$op,$p,$reason){$id=$p['id']??uid();
            switch($op){
            case 'participant':
                $old=$db->one('SELECT * FROM users WHERE id=?',[$id]);if($old)Scope::owned($db,$u,'users',$id);$role=$old['role']??($p['role']??'student');demand(in_array($role,['student','researcher'],true),'Hesap türü geçersiz');
                $login=trim($p['login']??'');demand($login!==''&&strlen($login)<=100,'Öğrenci numarası gerekli');$status=$p['status']??'active';demand(in_array($status,['active','frozen','archived'],true),'Hesap durumu geçersiz');
                $meta=array_replace($old?obj($old['metadata']):[],$p['metadata']??[]);demand(is_array($meta),'Profil bilgisi geçersiz');$pass=$p['password']??'';demand(($old&&$pass==='')||strlen($pass)>=12,'Parola en az 12 karakter olmalı');
                $hash=$pass!==''?password_hash($pass,PASSWORD_DEFAULT):$old['password'];
                if($old)$db->q('UPDATE users SET login=?,name=?,password=?,status=?,metadata=? WHERE id=?',[$login,$p['name']??'',$hash,$status,js($meta),$id]);
                else $db->insert('users',['id'=>$id,'login'=>$login,'name'=>$p['name']??'','password'=>$hash,'role'=>$role,'owner_id'=>$u['id'],'status'=>$status,'metadata'=>js($meta),'test'=>$this->app->config['env']==='test'?1:0,'created_at'=>now()]);break;
            case 'case':
                $old=$db->one('SELECT * FROM cases WHERE id=?'.($db->sqlite()?'':' FOR UPDATE'),[$id]);if($old)Scope::owned($db,$u,'cases',$id);$status=$p['status']??'draft';demand(in_array($status,['draft','active','closed','archived'],true),'Vaka durumu geçersiz');demand(trim($p['title']??'')!==''&&trim($p['body']??'')!=='','Başlık ve metin gerekli');
                $rubric=($p['rubric_id']??'')?:null;if($rubric){Scope::owned($db,$u,'definitions',$rubric);demand((bool)$db->one("SELECT id FROM definitions WHERE id=? AND family='rubric' AND archived=0 AND deleted_at IS NULL",[$rubric]),'Rubrik yok');}
                if(!$old)$db->insert('cases',['owner_id'=>$u['id'],'id'=>$id,'title'=>$p['title'],'position'=>(int)($p['position']??1),'status'=>$status,'practice'=>!empty($p['practice'])?1:0,'test'=>$this->app->config['env']==='test'?1:0,'stop_access'=>!empty($p['stop_access'])?1:0,'rubric_id'=>$rubric]);
                else $db->q('UPDATE cases SET title=?,position=?,status=?,practice=?,stop_access=?,rubric_id=? WHERE id=?',[$p['title'],(int)($p['position']??1),$status,!empty($p['practice'])?1:0,!empty($p['stop_access'])?1:0,$rubric,$id]);
                $v=(int)($db->one('SELECT MAX(version) n FROM case_versions WHERE case_id=?',[$id])['n']??0)+1;$vid=uid();$db->insert('case_versions',['id'=>$vid,'case_id'=>$id,'version'=>$v,'body'=>$p['body'],'instructions'=>$p['instructions']??'','notes'=>$p['notes']??'','created_at'=>now()]);$db->q('UPDATE cases SET current_version=? WHERE id=?',[$vid,$id]);break;
            case 'assign':case 'assign_bulk':case 'late_access':
                $case=Scope::owned($db,$u,'cases',$p['case_id']);
                if($op==='late_access'&&!empty($p['open']))demand((bool)$case['collection_closed_at'],'Ek süre için önce bu vakada veri toplamayı kapatın');
                $ids=$op==='assign'?[$p['user_id']]:($p['user_ids']??[]);demand(is_array($ids)&&count($ids)>0&&count($ids)<=1000,'En az bir öğrenci seçin');
                foreach(array_unique($ids) as $sid){
                    $student=$db->one("SELECT * FROM users WHERE id=? AND role='student'",[$sid]);demand((bool)$student,'Öğrenci yok');
                    $old=$db->one('SELECT * FROM assignments WHERE user_id=? AND case_id=?',[$sid,$p['case_id']]);
                    demand($student['owner_id']===$u['id'],'Bu katılımcıya erişemezsiniz',403);
                    if($op==='late_access'){
                        demand($old&&(bool)$old['active'],'Önce vakaya atayın');$id=$old['id'];
                        $db->q('UPDATE assignments SET allow_late=? WHERE id=?',[(int)(bool)($p['open']??false),$id]);
                    }else{
                        if($old){$id=$old['id'];$db->q('UPDATE assignments SET active=?,allow_late=0 WHERE id=?',[(int)(bool)$p['active'],$id]);}
                        else{$id=uid();$db->insert('assignments',['id'=>$id,'user_id'=>$sid,'case_id'=>$p['case_id'],'active'=>(int)(bool)$p['active']]);}
                    }
                    $db->audit($u['id'],$op,$id,$reason,['case_id'=>$p['case_id'],'user_id'=>$sid,'active'=>$p['active']??null,'open'=>$p['open']??null]);
                }return ['id'=>$id,'count'=>count(array_unique($ids))];
            case 'collection':
                Scope::owned($db,$u,'cases',$p['case_id']);$id=$p['case_id'];
                $db->q('UPDATE cases SET collection_closed_at=? WHERE id=?',[!empty($p['closed'])?now():null,$id]);
                $db->q('UPDATE assignments SET allow_late=0 WHERE case_id=?',[$id]);break;
            case 'delete_definition':
                $d=Scope::owned($db,$u,'definitions',$id);demand($d['owner_id']===$u['id'],'Tanımı yalnız sahibi silebilir',403);
                // Remove the whole version chain from new choices, preserving all historical references.
                $root=$d;while($root['parent_id'])$root=$db->one('SELECT * FROM definitions WHERE id=?',[$root['parent_id']]);
                $queue=[$root['id']];while($queue){$did=array_shift($queue);foreach($db->all('SELECT id FROM definitions WHERE parent_id=?',[$did]) as $child)$queue[]=$child['id'];$db->q('UPDATE definitions SET deleted_at=?,archived=1 WHERE id=?',[now(),$did]);}break;
            case 'definition':
                $family=$p['family']??'';demand(in_array($family,['rubric','label'],true),'Tanım tipi geçersiz');$body=$p['body']??[];if($family==='rubric')Rules::rubric($body);
                $parent=$p['parent_id']??null;if($parent){$d=Scope::owned($db,$u,'definitions',$parent);demand($d['owner_id']===$u['id']&&!$d['deleted_at'],'Tanım düzenlenemez',403);}$old=$parent?$db->one('SELECT * FROM definitions WHERE id=?'.($db->sqlite()?'':' FOR UPDATE'),[$parent]):null;demand(!$parent||($old&&$old['family']===$family),'Önceki tanım yok');if($parent)demand(!$db->one('SELECT id FROM definitions WHERE parent_id=?',[$parent]),'Yalnız son sürümden devam edin',409);demand(trim($p['name']??'')!=='','Tanım adı gerekli');
                $db->insert('definitions',['owner_id'=>$u['id'],'id'=>$id,'family'=>$family,'name'=>$p['name'],'version'=>$old?(int)$old['version']+1:1,'parent_id'=>$parent,'body'=>js($body),'archived'=>!empty($p['archived'])?1:0,'created_at'=>now()]);break;
            case 'labels':
                $m=$db->one('SELECT * FROM messages WHERE id=?',[$p['message_id']]);demand((bool)$m,'Mesaj yok');Scope::application($db,$u,$m['app_id']);$review=uid();$db->insert('label_reviews',['id'=>$review,'message_id'=>$m['id'],'actor'=>$u['id'],'reason'=>$reason,'created_at'=>now()]);
                foreach(array_unique($p['labels']??[]) as $label){Scope::owned($db,$u,'definitions',$label);demand((bool)$db->one("SELECT id FROM definitions WHERE id=? AND family='label' AND archived=0 AND deleted_at IS NULL",[$label]),'Etiket geçersiz');$db->insert('message_labels',['id'=>uid(),'message_id'=>$m['id'],'definition_id'=>$label,'source'=>'researcher','review_id'=>$review,'actor'=>$u['id'],'reason'=>$reason,'created_at'=>now()]);}break;
            case 'note': Scope::application($db,$u,$p['app_id']);$db->lock($p['app_id']);demand(trim($p['body']??'')!=='','Not metni gerekli');$db->insert('research_notes',['id'=>$id,'app_id'=>$p['app_id'],'actor'=>$u['id'],'body'=>$p['body'],'reason'=>$reason,'created_at'=>now()]);break;
            case 'exclude': Scope::application($db,$u,$id);$db->lock($id);$db->q('UPDATE applications SET excluded=?,exclusion_reason=? WHERE id=?',[(int)(bool)$p['excluded'],$reason,$id]);break;
            case 'resolve_request':
                $r=$db->one('SELECT * FROM ai_requests WHERE id=?',[$id]);demand((bool)$r,'İstek yok');Scope::application($db,$u,$r['app_id']);$a=$db->lock($r['app_id']);demand($r['status']==='running','İstek zaten kapalı');demand(time()-strtotime($r['started_at'])>$this->app->config['ai']['timeout']+60,'Sağlayıcı timeout + 60 s dolmadan kapatılamaz',409);$db->q("UPDATE ai_requests SET status='failed',attempt=?,error='researcher_closed',completed_at=? WHERE id=?",[uid(),now(),$id]);$db->q("UPDATE ai_attempts SET status='failed',error='researcher_closed',completed_at=? WHERE id=?",[now(),$r['attempt']]);if($a['active_request']===$id)$db->q('UPDATE applications SET active_request=NULL WHERE id=?',[$a['id']]);break;
            case 'resolve_analysis':Scope::step($db,$u,$id);
                $s=$db->one('SELECT * FROM analysis_steps WHERE id=?'.($db->sqlite()?'':' FOR UPDATE'),[$id]);demand($s&&$s['status']==='running','Çalışan analiz adımı yok');demand(time()-strtotime($s['created_at'])>$this->app->config['ai']['timeout']+60,'Timeout + 60 s dolmadan kapatılamaz',409);$db->q("UPDATE analysis_steps SET status='failed',error='researcher_closed' WHERE id=?",[$id]);break;
            case 'close_study':throw new Fault('Vaka seçerek veri toplamayı açın veya kapatın',422);
            default:throw new Fault('Bilinmeyen yönetim işlemi',404);
            }
            $db->audit($u['id'],$op,$id,$reason,array_diff_key($p,array_flip(['password','body','metadata'])));return ['id'=>$id];
        });
    }
}
