<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/15/16
 * Time: 4:34 PM
 */

namespace CRMBundle\Entity;


use AppBundle\Controller\CRMPaginator;
use AppBundle\Controller\GlobalClass;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

class ClientCategoryMappingRepository extends EntityRepository
{
    public function getUidsStringByCategory($category_id,Request $request){
        $dql = "SELECT cm.uid FROM {$this->getEntityName()} cm WHERE cm.category_id = :category_id";
        $query = $this->getEntityManager()->createQuery($dql)->setParameter('category_id',$category_id);
        $paginator = new CRMPaginator($request,$query);
        $pagination = $paginator->paginate();
        $uidsArray = $paginator->getMainResult(true);
        $uidsString = GlobalClass::arrayToString($uidsArray);
        return array(
            'pagination'=>$pagination,'string'=>$uidsString,'array'=>$uidsArray
        );
    }
}