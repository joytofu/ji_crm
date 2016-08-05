<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 5/20/16
 * Time: 11:30 AM
 */

namespace CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use UserBundle\Entity\User;

/**
 * @ORM\Table(name="distribution")
 * @ORM\Entity(repositoryClass="CRMBundle\Entity\DistributionRepository")
 */
class Distribution
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    protected $uid;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    protected $mid;


    
    public function __construct()
    {
        $this->clients = array();
    }

    public function getId(){
        return $this->id;
    }

    public function getUid(){
        return $this->uid;
    }

    public function setUid($uid){
        $this->uid = $uid;
    }

    public function getMid(){
        return $this->mid;
    }

    public function setMid($mid){
        $this->mid = $mid;
    }


    
}