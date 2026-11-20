<?php
declare(strict_types=1);

final class CollectionTimeline {
    private array $changes=[];

    public function __construct(Db $db,private array $application) {
        $rows=$db->all("SELECT id,action,detail,created_at FROM audit
            WHERE (action='collection' AND target=?)
               OR (action IN ('assign','assign_bulk','late_access') AND target=?)
               OR (action='new_attempt' AND target IN (SELECT id FROM applications WHERE case_id=? AND user_id=?))
            ORDER BY created_at,id",
            [$application['case_id'],$application['assignment_id'],$application['case_id'],$application['user_id']]);
        $closed=false;
        $late=false;
        $assigned=true;
        $allowed=true;
        $window=null;
        foreach($rows as $row){
            $detail=obj($row['detail']);
            $at=Navigation::time($row['action']==='collection'?($detail['effective_at']??$row['created_at']):$row['created_at']);
            if($at===null)continue;
            switch($row['action']){
                case 'collection':$closed=!empty($detail['closed']);$late=false;break;
                case 'late_access':$late=!empty($detail['open']);break;
                case 'new_attempt':if(!empty($detail['late_access']))$late=true;break;
                default:$assigned=!empty($detail['active']);$late=false;
            }
            $next=$assigned&&(!$closed||$late);
            if($next===$allowed)continue;
            $allowed=$next;
            if($allowed)$window=$row['id'];
            $this->changes[]=['at'=>$at,'allowed'=>$allowed,'window'=>$window];
        }
        // A migrated closure can predate the per-case audit history.
        if($allowed&&Scope::collectionClosed($application)){
            $at=Navigation::time($application['collection_closed_at']);
            if($at!==null&&(!$this->changes||$at>end($this->changes)['at']))
                $this->changes[]=['at'=>$at,'allowed'=>false,'window'=>null];
        }
    }

    public function classify(?float $clientTime,array $payload,float $received,?float $submitted=null): array {
        $currentClosed=Scope::collectionClosed($this->application);
        $from=Navigation::time($payload['capture_from']??null);
        $until=Navigation::time($payload['capture_until']??null);
        $anchored=$from!==null&&$until!==null&&$from<=$until&&$until-$from<=5&&$from<=$received;
        if($anchored)$until=min($until,$received);
        elseif($currentClosed&&$submitted===null)return ['allowed'=>false,'window'=>null];
        else $from=$until=$clientTime!==null&&$submitted!==null?min($clientTime,$submitted):$clientTime;

        if($from===null)return ['allowed'=>!$this->changes&&!$currentClosed,'window'=>null];
        $allowed=true;
        $window=null;
        foreach($this->changes as $change){
            if($change['at']>$until)break;
            if($change['at']>$from)return ['allowed'=>false,'window'=>null];
            $allowed=$change['allowed'];$window=$change['window'];
        }
        return ['allowed'=>$allowed,'window'=>$window];
    }
}
