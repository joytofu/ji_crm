<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/17/16
 * Time: 2:58 PM
 */

namespace CRMBundle\Entity;


use Doctrine\ORM\EntityRepository;

class TrackingRecordRepository extends EntityRepository
{
    public function findTrackingRecordByUid($uid){
        $dql = "SELECT tr FROM {$this->getEntityName()} tr WHERE uid = :uid";
        $query = $this->getEntityManager()->createQuery($dql)->setParameter('uid',$uid);
        $result = $query->getSingleResult();
        return $result;
    }

    public function countByContactMethodAndDateRange($contactMethod,$dateRange = null,$mid = null){
        switch($dateRange){
            case "intraday":
                $start = mktime(0,0,0,date('m'),date('d'),date('Y'));
                $end = mktime(23,59,59,date('m'),date('d'),date('y'));
                break;
            case "7days":
                $start = mktime(0,0,0,date('m'),date('d')-7,date('Y'));
                $end = mktime(23,59,59,date('m'),date('d')-1,date('Y'));
                break;
            case "currentMonth":
                $start = mktime(0,0,0,date('m'),1,date('Y'));
                $end = mktime(23,59,59,date('m'),date('t'),date('Y'));
                break;
            case "lastMonth":
                $start = mktime(0,0,0,date('m')-1,1,date('Y'));
                $end = mktime(23,59,59,date('m')-1,date('t'),date('Y'));
                break;
            case "last3Months":
                $start = mktime(0,0,0,date('m')-3,1,date('Y'));
                $end = mktime(23,59,59,date('m')-1,date('t'),date('Y'));
                break;
            case null:
                break;
        }
        if($mid==null){
            $midTerm = null;
        }elseif(is_numeric($mid)){
            $midTerm = "AND tr.mid = {$mid}";
        }

        if($dateRange==null){
            $rangerTerm = null;
        }else{
            $rangerTerm = "AND tr.updatedAt BETWEEN {$start} and {$end}";
        }
        $dql = "SELECT count(tr) FROM {$this->getEntityName()} tr WHERE tr.contactMethod = :contactMethod {$midTerm} {$rangerTerm}";
        $result = $this->getEntityManager()->createQuery($dql)->setParameter('contactMethod',$contactMethod)->getSingleScalarResult();
        return $result;
    }

    public function getAllCountDataByContactMethod($contactMethod,$mid=null){
        
        return array(
            'contactMethod'=>$contactMethod,
            'intraday'=>$this->countByContactMethodAndDateRange($contactMethod,'intraday',$mid),
            'sevenDays'=>$this->countByContactMethodAndDateRange($contactMethod,'7days',$mid),
            'currentMonth'=>$this->countByContactMethodAndDateRange($contactMethod,'currentMonth',$mid),
            'lastMonth'=>$this->countByContactMethodAndDateRange($contactMethod,'lastMonth',$mid),
            'last3Months'=>$this->countByContactMethodAndDateRange($contactMethod,'last3Months',$mid),
        );
    }
    
    
}