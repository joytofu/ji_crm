<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/8/16
 * Time: 9:49 AM
 */

namespace CRMBundle\Entity;


use AppBundle\Controller\CRMPaginator;
use AppBundle\Controller\GlobalClass;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;

class DistributionRepository extends EntityRepository
{
    /**
     * @param array $fields <p>An array of fields that you like to display</p>
     * @param boolean $pageOption [optional]
     * @return array
     * <code>
     * Array
     * (
     *   [0]=>Array([uid]=>1),
     *   [1]=>Array([uid]=>2)
     * )
     * </code>
     * 
     */
    public function findUidsByManager($mid=null,Request $request,$fetchAllUids = false){

        
        if($mid!=null && is_numeric($mid)){
            $dql = "SELECT d.uid FROM CRMBundle:Distribution d WHERE d.mid = :mid";
            $query = $this->getEntityManager()->createQuery($dql)->setParameter('mid',$mid);
        }else{
            $dql = "SELECT d.uid FROM CRMBundle:Distribution d";
            $query = $this->getEntityManager()->createQuery($dql);
        }
        if($fetchAllUids==false){
            $paginator = new CRMPaginator($request,$query);
            $pagination = $paginator->paginate();
            $fieldsArray = $paginator->getMainResult(true);  //fields array
            $fieldsString = GlobalClass::arrayToString($fieldsArray); //uids string
            return array('pagination'=>$pagination,'string'=>$fieldsString,'array'=>$fieldsArray);
        }else{
            $fieldsArray = $query->getArrayResult();
            $fieldsString = GlobalClass::arrayToString($fieldsArray);
            return array('string'=>$fieldsString,'array'=>$fieldsArray);
        }

        
    }

    public function findUidsByManagerAndCategory($midsArr=array(),$categoryIdsArr=array()){
        if(!empty($midsArr) && !empty($categoryIdsArr)){
            $midsTerm = " WHERE d.mid IN (:mids)";
            $cateTerm = " AND cm.category_id IN (:category_ids)";
            $parameters = array('mids'=>$midsArr,'category_ids'=>$categoryIdsArr);
        }elseif(!empty($midsArr) && empty($categoryIdsArr)){
            $midsTerm = " WHERE d.mid IN (:mids)";
            $cateTerm = null;
            $parameters = array('mids'=>$midsArr);
        }elseif(empty($midsArr) && !empty($categoryIdsArr)){
            $midsTerm = null;
            $cateTerm = " WHERE cm.category_id IN (:category_ids)";
            $parameters = array('category_ids'=>$categoryIdsArr);
        }elseif(empty($midsArr) && empty($categoryIdsArr)){
            $midsTerm = null;
            $cateTerm = null;
        }
        $dql = "SELECT cm.uid FROM CRMBundle:ClientCategoryMapping cm WHERE cm.uid IN (SELECT d.uid FROM CRMBundle:Distribution d{$midsTerm}{$cateTerm})";
        if(empty($midsArr) && empty($categoryIdsArr)){
            $query = $this->getEntityManager()->createQuery($dql);
        }else{
            $query = $this->getEntityManager()->createQuery($dql)->setParameters($parameters);
        }
        //$query = $this->getEntityManager()->createQuery($dql)->setParameters(['mids'=>$midsArr,'category_ids'=>$categoryIdsArr]);
        $output = $query->getArrayResult();
        return $output;
    }
    
    
    public function findUids($pageOption = false,$request=null){

        $query = $this->getEntityManager()->createQuery('
            SELECT d.uid FROM CRMBundle:Distribution d
        ');
        if($pageOption==true){
            $paginator = new CRMPaginator($request,$query);
            $pagination = $paginator->paginate();
            
            $uids_arr = $paginator->getMainResult(true);
            return array('pagination'=>$pagination,'uids'=>$uids_arr);
        }else{
            $uids_arr = $query->getArrayResult();
            return $uids_arr;
        }
        
    }
    
    public function countClientsOfManager($mid){
        $dql = "SELECT COUNT(d) FROM {$this->getEntityName()} d WHERE d.mid = $mid";
        $result = $this->getEntityManager()->createQuery($dql)->getSingleScalarResult();
        return $result;
    }
    
    public function findAllDistributedUids(){
        $dql = "SELECT d.uid FROM {$this->getEntityName()} d";
        $uidsArray = $this->getEntityManager()->createQuery($dql)->getArrayResult();
        $uidsString = GlobalClass::arrayToString($uidsArray);
        return $uidsString;
    }
    
    public function updateManager($mid,$uids){
        $qb = $this->getEntityManager()->createQueryBuilder();

            $qb->update($this->getEntityName(),'d')
            ->add('set',$qb->expr()->eq('d.mid',':mid'))
            ->add('where',$qb->expr()->in('d.uid',$uids));
        $qb->getQuery()->execute(['mid'=>$mid]);
        $this->getEntityManager()->flush();
    }
    
    
    

}