<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/15/16
 * Time: 5:14 PM
 */

namespace UserBundle\Entity;


use Doctrine\ORM\EntityRepository;

class UserInfoRepository extends EntityRepository
{
    public function getManagerByUid($uid){
        $em = $this->getEntityManager();
        $distribution = $em->getRepository('CRMBundle:Distribution')->findOneBy(['uid'=>$uid]);
        $managerName = $em->getRepository($this->getEntityName())->find($distribution->getMid())->getName();
        return $managerName;
    }

    public function getDistinctManagers(){
        $dql = "SELECT u.id,u.name FROM {$this->getEntityName()} u WHERE u.id IN (SELECT DISTINCT d.mid FROM CRMBundle:Distribution d) AND u.id NOT IN (0)";
        $managers = $this->getEntityManager()->createQuery($dql)->getArrayResult();
        return $managers;
    }
    
    public function getAllManagers(){
        $dql = "SELECT ui FROM UserBundle:UserInfo ui WHERE ui.id > 0";
        $managers = $this->getEntityManager()->createQuery($dql)->getArrayResult();
        return $managers;
        
    }
}