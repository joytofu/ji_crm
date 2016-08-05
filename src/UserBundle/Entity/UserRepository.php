<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/9/16
 * Time: 3:29 PM
 */

namespace UserBundle\Entity;


use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findByRoles($role){
        /*$qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.roles LIKE :role')
            ->setParameter('role', $role);

        return $qb->getQuery()->getArrayResult();*/
        $dql = "SELECT u FROM UserBundle:User u WHERE u.roles LIKE :role";
        return $this->_em->createQuery($dql)->setParameter('role',$role)->getArrayResult();
    }

}