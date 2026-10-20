<?php
declare(strict_types=1);
final class Admin {
    public function __construct(private App $app) {}
    public function dashboard(array $u): array {
        Auth::researcher($u);$db=$this->app->db;
        return ['users'=>$db->all('SELECT id,login,name,role,status,metadata,test FROM users ORDER BY login'),'cases'=>$db->all('SELECT c.*,v.body,v.instructions,v.notes,v.version FROM cases c JOIN case_versions v ON v.id=c.current_version ORDER BY position'),'versions'=>$db->all('SELECT id,case_id,version FROM case_versions ORDER BY case_id,version'),'assignments'=>$db->all('SELECT * FROM assignments'),'applications'=>$db->all('SELECT id,user_id,case_id,version_id,state,initial_at,submitted_at,last_section,last_activity,active_request,excluded FROM applications'),'analysis_status'=>$db->all("SELECT r.app_id,MAX(CASE WHEN s.status='completed' THEN s.step ELSE 0 END) completed_step FROM analysis_runs r LEFT JOIN analysis_steps s ON s.run_id=r.id GROUP BY r.app_id"),'report_status'=>$db->all('SELECT app_id,MAX(approved_at) approved_at,MAX(published_at) published_at FROM reports GROUP BY app_id'),'technical'=>$db->all("SELECT DISTINCT app_id FROM ai_requests WHERE status IN ('failed','partial')"),'definitions'=>$db->all('SELECT * FROM definitions ORDER BY created_at,version'),'study'=>$db->one('SELECT * FROM study WHERE id=1'),'audit'=>$db->all('SELECT * FROM audit ORDER BY created_at DESC LIMIT 100')];
    }
    public function mutate(array $u,string $op,array $p): array {
        Auth::researcher($u);$db=$this->app->db;$reason=trim($p['reason']??'');demand($reason!=='','İşlem gerekçesi gerekli');
        return $db->tx(function()use($db,$u,$op,$p,$reason){$id=$p['id']??uid();
            switch($op){
            case 'participant':
                $old=$db->one('SELECT * FROM users WHERE id=?',[$id]);demand(!$old||$old['role']==='student','Araştırmacı hesabı bu işlemle değişmez');
                $login=trim($p['login']??'');demand($login!==''&&strlen($login)<=100,'Öğrenci numarası gerekli');$status=$p['status']??'active';demand(in_array($status,['active','frozen','archived'],true),'Hesap durumu geçersiz');
                $meta=$p['metadata']??[];demand(is_array($meta),'Profil bilgisi geçersiz');$pass=$p['password']??'';demand(($old&&$pass==='')||strlen($pass)>=12,'Parola en az 12 karakter olmalı');
                $hash=$pass!==''?password_hash($pass,PASSWORD_DEFAULT):$old['password'];
                if($old)$db->q('UPDATE users SET login=?,name=?,password=?,status=?,metadata=? WHERE id=?',[$login,$p['name']??'',$hash,$status,js($meta),$id]);
                else $db->insert('users',['id'=>$id,'login'=>$login,'name'=>$p['name']??'','password'=>$hash,'role'=>'student','status'=>$status,'metadata'=>js($meta),'test'=>$this->app->config['env']==='test'?1:0,'created_at'=>now()]);break;
            case 'case':
                $old=$db->one('SELECT * FROM cases WHERE id=?'.($db->sqlite()?'':' FOR UPDATE'),[$id]);$status=$p['status']??'draft';demand(in_array($status,['draft','active','closed','archived'],true),'Vaka durumu geçersiz');demand(trim($p['title']??'')!==''&&trim($p['body']??'')!=='','Başlık ve metin gerekli');
                $rubric=($p['rubric_id']??'')?:null;if($rubric)demand((bool)$db->one("SELECT id FROM definitions WHERE id=? AND family='rubric' AND archived=0",[$rubric]),'Rubrik yok');
                if(!$old)$db->insert('cases',['id'=>$id,'title'=>$p['title'],'position'=>(int)($p['position']??1),'status'=>$status,'practice'=>!empty($p['practice'])?1:0,'test'=>$this->app->config['env']==='test'?1:0,'stop_access'=>!empty($p['stop_access'])?1:0,'rubric_id'=>$rubric]);
                else $db->q('UPDATE cases SET title=?,position=?,status=?,practice=?,stop_access=?,rubric_id=? WHERE id=?',[$p['title'],(int)($p['position']??1),$status,!empty($p['practice'])?1:0,!empty($p['stop_access'])?1:0,$rubric,$id]);
                $v=(int)($db->one('SELECT MAX(version) n FROM case_versions WHERE case_id=?',[$id])['n']??0)+1;$vid=uid();$db->insert('case_versions',['id'=>$vid,'case_id'=>$id,'version'=>$v,'body'=>$p['body'],'instructions'=>$p['instructions']??'','notes'=>$p['notes']??'','created_at'=>now()]);$db->q('UPDATE cases SET current_version=? WHERE id=?',[$vid,$id]);break;
            case 'assign':
                demand((bool)$db->one("SELECT id FROM users WHERE id=? AND role='student'",[$p['user_id']]),'Öğrenci yok');demand((bool)$db->one('SELECT id FROM cases WHERE id=?',[$p['case_id']]),'Vaka yok');
                $old=$db->one('SELECT * FROM assignments WHERE user_id=? AND case_id=?',[$p['user_id'],$p['case_id']]);
                if($old){$id=$old['id'];$db->q('UPDATE assignments SET active=? WHERE id=?',[(int)(bool)$p['active'],$id]);}
                else $db->insert('assignments',['id'=>$id,'user_id'=>$p['user_id'],'case_id'=>$p['case_id'],'active'=>(int)(bool)$p['active']]);break;
            case 'definition':
                $family=$p['family']??'';demand(in_array($family,['rubric','label'],true),'Tanım tipi geçersiz');$body=$p['body']??[];if($family==='rubric')Rules::rubric($body);
                $parent=$p['parent_id']??null;$old=$parent?$db->one('SELECT * FROM definitions WHERE id=?'.($db->sqlite()?'':' FOR UPDATE'),[$parent]):null;demand(!$parent||($old&&$old['family']===$family),'Önceki tanım yok');if($parent)demand(!$db->one('SELECT id FROM definitions WHERE parent_id=?',[$parent]),'Yalnız son sürümden devam edin',409);demand(trim($p['name']??'')!=='','Tanım adı gerekli');
                $db->insert('definitions',['id'=>$id,'family'=>$family,'name'=>$p['name'],'version'=>$old?(int)$old['version']+1:1,'parent_id'=>$parent,'body'=>js($body),'archived'=>!empty($p['archived'])?1:0,'created_at'=>now()]);break;
            case 'labels':
                $m=$db->one('SELECT * FROM messages WHERE id=?',[$p['message_id']]);demand((bool)$m,'Mesaj yok');$review=uid();$db->insert('label_reviews',['id'=>$review,'message_id'=>$m['id'],'actor'=>$u['id'],'reason'=>$reason,'created_at'=>now()]);
                foreach(array_unique($p['labels']??[]) as $label){demand((bool)$db->one("SELECT id FROM definitions WHERE id=? AND family='label' AND archived=0",[$label]),'Etiket geçersiz');$db->insert('message_labels',['id'=>uid(),'message_id'=>$m['id'],'definition_id'=>$label,'source'=>'researcher','review_id'=>$review,'actor'=>$u['id'],'reason'=>$reason,'created_at'=>now()]);}break;
            case 'note': $db->lock($p['app_id']);demand(trim($p['body']??'')!=='','Not metni gerekli');$db->insert('research_notes',['id'=>$id,'app_id'=>$p['app_id'],'actor'=>$u['id'],'body'=>$p['body'],'reason'=>$reason,'created_at'=>now()]);break;
            case 'exclude': $db->lock($id);$db->q('UPDATE applications SET excluded=?,exclusion_reason=? WHERE id=?',[(int)(bool)$p['excluded'],$reason,$id]);break;
            case 'resolve_request':
                $r=$db->one('SELECT * FROM ai_requests WHERE id=?',[$id]);demand((bool)$r,'İstek yok');$a=$db->lock($r['app_id']);demand($r['status']==='running','İstek zaten kapalı');demand(time()-strtotime($r['started_at'])>$this->app->config['ai']['timeout']+60,'Sağlayıcı timeout + 60 s dolmadan kapatılamaz',409);$db->q("UPDATE ai_requests SET status='failed',attempt=?,error='researcher_closed',completed_at=? WHERE id=?",[uid(),now(),$id]);$db->q("UPDATE ai_attempts SET status='failed',error='researcher_closed',completed_at=? WHERE id=?",[now(),$r['attempt']]);if($a['active_request']===$id)$db->q('UPDATE applications SET active_request=NULL WHERE id=?',[$a['id']]);break;
            case 'resolve_analysis':
                $s=$db->one('SELECT * FROM analysis_steps WHERE id=?'.($db->sqlite()?'':' FOR UPDATE'),[$id]);demand($s&&$s['status']==='running','Çalışan analiz adımı yok');demand(time()-strtotime($s['created_at'])>$this->app->config['ai']['timeout']+60,'Timeout + 60 s dolmadan kapatılamaz',409);$db->q("UPDATE analysis_steps SET status='failed',error='researcher_closed' WHERE id=?",[$id]);break;
            case 'close_study':
                $count=(int)$db->one("SELECT COUNT(*) n FROM cases WHERE practice=0 AND status<>'archived'")['n'];demand($count>=5,'En az beş araştırma vakası gerekli');demand((bool)($p['confirm']??false),'Global kapanış onayı gerekli');demand(!$db->one('SELECT closed_at FROM study WHERE id=1')['closed_at'],'Çalışma zaten kapalı');
                $db->q('UPDATE study SET closed_at=?,reason=?,actor=? WHERE id=1',[now(),$reason,$u['id']]);break;
            default:throw new Fault('Bilinmeyen yönetim işlemi',404);
            }
            $db->audit($u['id'],$op,$id,$reason,array_diff_key($p,array_flip(['password','body','metadata'])));return ['id'=>$id];
        });
    }
}
