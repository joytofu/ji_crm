<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/15/16
 * Time: 5:07 PM
 */

namespace CRMBundle\Entity;


use Doctrine\ORM\EntityRepository;

class ClientCategoryRepository extends EntityRepository
{
    public function getCategoryByUid($uid){
        if($mapping = $this->getEntityManager()->getRepository('CRMBundle:ClientCategoryMapping')->findOneBy(['uid'=>$uid])){
            $category_id = $mapping->getCategoryId();
            if($category = $this->getEntityManager()->getRepository('CRMBundle:ClientCategory')->find($category_id)){
                $category = $category->getName();
                return $category;
            }else{
                return null;
            }
        }else{
            return null;
        }
    }
}