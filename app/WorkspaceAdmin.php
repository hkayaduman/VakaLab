<?php
declare(strict_types=1);

final class WorkspaceAdmin {
    public function __construct(private Db $db) {}

    public function mutate(array $actor,string $action,array $input): array {
        demand(Auth::isSuperAdmin($actor),'Süper Admin yetkisi gerekli',403);
        return $this->db->tx(fn()=>match($action){
            'workspace_create'=>$this->create($actor,$input),
            'workspace_memberships'=>$this->memberships($actor,$input),
            'move_case'=>$this->moveCase($actor,$input),
        });
    }

    private function create(array $actor,array $input): array {
        $name=is_string($input['name']??null)?trim($input['name']):'';
        demand($name!==''&&mb_strlen($name)<=200,'Çalışma alanı adı gerekli (en fazla 200 karakter)');
        demand(!$this->db->one('SELECT id FROM workspaces WHERE name=?',[$name]),'Bu çalışma alanı adı zaten kullanılıyor',409);
        $id=uid();
        $this->db->insert('workspaces',['id'=>$id,'name'=>$name]);
        $this->db->audit($actor['id'],'workspace_create',$id,'',['name'=>$name]);
        return ['id'=>$id,'name'=>$name];
    }

    private function memberships(array $actor,array $input): array {
        $id=$input['user_id']??null;
        $ids=$input['workspace_ids']??null;
        demand(is_string($id)&&is_array($ids)&&array_is_list($ids),'Hesap ve çalışma alanları gerekli');
        $this->lockUser($id);
        foreach($ids as $workspace)Workspace::requireAccess($this->db,$actor,is_string($workspace)?$workspace:'');
        $previous=array_column($this->db->all('SELECT workspace_id FROM workspace_memberships WHERE user_id=?',[$id]),'workspace_id');
        foreach(array_diff($previous,$ids) as $workspace)$this->db->q('DELETE FROM workspace_memberships WHERE user_id=? AND workspace_id=?',[$id,$workspace]);
        foreach(array_diff($ids,$previous) as $workspace)Workspace::grant($this->db,$workspace,$id);
        $this->db->audit($actor['id'],'workspace_memberships',$id,'',['workspace_ids'=>array_values(array_unique($ids))]);
        return ['id'=>$id];
    }

    private function lockUser(string $id): void {
        $user=$this->db->one("SELECT id FROM users WHERE id=? AND role IN ('student','researcher')".$this->lock(),[$id]);
        demand((bool)$user,'Öğrenci veya araştırmacı hesabı gerekli',404);
    }

    private function lock(): string {return $this->db->sqlite()?'':' FOR UPDATE';}

    private function moveCase(array $actor,array $input): array {
        $id=$input['case_id']??null;
        $target=$input['workspace_id']??null;
        demand(is_string($id)&&is_string($target),'Vaka ve hedef çalışma alanı gerekli');
        demand((bool)$this->db->one('SELECT id FROM workspaces WHERE id=?'.$this->lock(),[$target]),'Hedef çalışma alanı bulunamadı',404);
        $case=$this->db->one('SELECT * FROM cases WHERE id=?'.$this->lock(),[$id]);
        demand((bool)$case,'Vaka bulunamadı',404);
        if($case['workspace_id']===$target)return ['id'=>$id,'workspace_id'=>$target];
        $assignments=$this->db->all('SELECT id,user_id,active FROM assignments WHERE case_id=? ORDER BY id'.$this->lock(),[$id]);
        $apps=$this->db->all('SELECT id,user_id FROM applications WHERE case_id=? ORDER BY id'.$this->lock(),[$id]);
        $activeAssignments=array_filter($assignments,fn($row)=>(bool)$row['active']);
        $users=array_merge(array_column($activeAssignments,'user_id'),array_column($apps,'user_id'),[$case['owner_id']]);
        $auditTargets=array_merge([$id],array_column($assignments,'id'),array_column($apps,'id'));
        $references=['rubric_id'=>[$case['rubric_id']],'definition_id'=>[]];
        $documents=[];
        foreach($apps as $app){
            $aid=$app['id'];
            $runs=$this->db->all('SELECT id,rubric_id FROM analysis_runs WHERE app_id=?',[$aid]);
            $labels=$this->db->all('SELECT l.id,l.definition_id,l.actor FROM message_labels l JOIN messages m ON m.id=l.message_id WHERE m.app_id=?',[$aid]);
            $auditTargets=array_merge($auditTargets,array_column($runs,'id'),array_column($labels,'id'));
            $references['rubric_id']=array_merge($references['rubric_id'],array_column($runs,'rubric_id'));
            $references['definition_id']=array_merge($references['definition_id'],array_column($labels,'definition_id'));
            $users=array_merge($users,array_column($labels,'actor'));
            foreach(['reports','research_notes'] as $table){
                foreach($this->db->all('SELECT id,actor,body FROM '.$table.' WHERE app_id=?',[$aid]) as $row){
                    $users[]=$row['actor'];
                    $auditTargets[]=$row['id'];
                    if($table==='reports')$documents[]=['reports','body',$row['id'],obj($row['body'])];
                }
            }
            foreach($this->db->all('SELECT r.actor FROM label_reviews r JOIN messages m ON m.id=r.message_id WHERE m.app_id=?',[$aid]) as $row)$users[]=$row['actor'];
            foreach($runs as $run){
                foreach($this->db->all('SELECT id,status,input,output FROM analysis_steps WHERE run_id=?',[$run['id']]) as $step){
                    demand($step['status']!=='running','Vaka taşınmadan önce çalışan değerlendirme tamamlanmalı',409);
                    $auditTargets[]=$step['id'];
                    foreach(['input','output'] as $column)$documents[]=['analysis_steps',$column,$step['id'],obj($step[$column])];
                    foreach($this->db->all('SELECT id,actor,body FROM decisions WHERE step_id=?',[$step['id']]) as $decision){
                        $users[]=$decision['actor'];
                        $auditTargets[]=$decision['id'];
                        $documents[]=['decisions','body',$decision['id'],obj($decision['body'])];
                    }
                }
            }
            foreach($this->db->all("SELECT id,actor,detail FROM audit WHERE target=? AND action='message_labels_ai'",[$aid]) as $batch){
                $users[]=$batch['actor'];
                $documents[]=['audit','detail',$batch['id'],obj($batch['detail'])];
            }
        }
        foreach(array_chunk(array_unique($auditTargets),200) as $keys){
            $placeholders=implode(',',array_fill(0,count($keys),'?'));
            $users=array_merge($users,array_column($this->db->all("SELECT DISTINCT actor FROM audit WHERE target IN ($placeholders)",$keys),'actor'));
        }
        $map=[];
        foreach(array_unique(array_filter(array_merge(...array_values($references)))) as $definition)
            $this->copyDefinition($definition,$target,$map);
        foreach($documents as [$table,$column,$key,$document]){
            $updated=$this->definitionReferences($document,$target,$map);
            if($updated!==$document)$this->db->q("UPDATE $table SET $column=? WHERE id=?",[js($updated),$key]);
        }
        foreach(array_unique($references['rubric_id']) as $old)if($old&&$map[$old]!==$old)
            $this->db->q('UPDATE analysis_runs SET rubric_id=? WHERE rubric_id=? AND app_id IN (SELECT id FROM applications WHERE case_id=?)',[$map[$old],$old,$id]);
        foreach(array_unique($references['definition_id']) as $old)if($map[$old]!==$old)
            $this->db->q('UPDATE message_labels SET definition_id=? WHERE definition_id=? AND message_id IN (SELECT m.id FROM messages m JOIN applications a ON a.id=m.app_id WHERE a.case_id=?)',[$map[$old],$old,$id]);
        $users=array_unique(array_filter($users));
        sort($users);
        foreach($users as $user){
            if($this->db->one("SELECT id FROM users WHERE id=? AND role IN ('student','researcher')".$this->lock(),[$user]))
                Workspace::grant($this->db,$target,$user);
        }
        $this->db->q('UPDATE cases SET workspace_id=?,rubric_id=? WHERE id=?',[$target,$map[$case['rubric_id']]??null,$id]);
        $this->db->audit($actor['id'],'move_case',$id,'',['source'=>$case['workspace_id'],'workspace_id'=>$target]);
        return ['id'=>$id,'workspace_id'=>$target];
    }

    private function copyDefinition(string $id,string $target,array &$map): string {
        if(isset($map[$id]))return $map[$id];
        $definition=$this->db->one('SELECT * FROM definitions WHERE id=?',[$id]);
        if(!$definition||$definition['workspace_id']===$target)return $map[$id]=$id;
        $origin=$definition['origin_id']??$id;
        $existing=$this->db->one('SELECT id FROM definitions WHERE workspace_id=? AND (id=? OR origin_id=?) ORDER BY id LIMIT 1',[$target,$origin,$origin]);
        if($existing)return $map[$id]=$existing['id'];
        if($definition['parent_id'])$definition['parent_id']=$this->copyDefinition($definition['parent_id'],$target,$map);
        $definition['id']=uid();
        $definition['origin_id']=$origin;
        $definition['workspace_id']=$target;
        $this->db->insert('definitions',$definition);
        return $map[$id]=$definition['id'];
    }

    private function definitionReferences(array $data,string $target,array &$map): array {
        foreach(['rubric_id','rubric_version'] as $key)
            if(is_string($data[$key]??null))$data[$key]=$this->copyDefinition($data[$key],$target,$map);
        if(is_array($data['label_definitions']??null)){
            foreach($data['label_definitions'] as &$definition)
                if(is_string($definition['id']??null))$definition['id']=$this->copyDefinition($definition['id'],$target,$map);
            unset($definition);
        }
        if(is_array($data['labels']??null)){
            foreach($data['labels'] as &$labels)if(is_array($labels)){
                foreach($labels as &$label)if(is_string($label))$label=$this->copyDefinition($label,$target,$map);
                unset($label);
            }
            unset($labels);
        }
        if(is_array($data['parts']??null)){
            foreach($data['parts'] as &$part)
                if(is_array($part['approved']??null))$part['approved']=$this->definitionReferences($part['approved'],$target,$map);
            unset($part);
        }
        if(is_array($data['internal']['suggestions']??null)){
            foreach($data['internal']['suggestions'] as &$suggestion){
                if(!is_string($suggestion['output']??null))continue;
                $output=obj($suggestion['output']);
                $updated=$this->definitionReferences($output,$target,$map);
                if($updated!==$output)$suggestion['output']=js($updated);
            }
            unset($suggestion);
        }
        return $data;
    }
}
