<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 5/15/16
 * Time: 12:48 PM
 */

namespace UserBundle\Entity;

use CRMBundle\Entity\CustomerService;
use CRMBundle\Entity\Distribution;
use CRMBundle\Entity\TrackingRecord;
use Doctrine\Common\Collections\ArrayCollection;
use \FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\UserInfo",mappedBy="user",cascade={"persist"})
     */
    protected $userInfo;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User",inversedBy="children",cascade={"persist"})
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="UserBundle\Entity\User",mappedBy="parent",cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id",referencedColumnName="id")
     */
    protected $children;

    /**
     * @ORM\OneToMany(targetEntity="CRMBundle\Entity\Distribution",mappedBy="manager")
     */
    protected $distributions;


    public function __construct()
    {
        parent::__construct();
        $this->children = new ArrayCollection();
        $this->distributions = new ArrayCollection();

    }
    
    public function getUserInfo(){
        return $this->userInfo;
    }
    
    public function setUserInfo(UserInfo $info){
        $this->userInfo = $info;
    }

    public function getParent(){
        return $this->parent;
    }

    public function setParent(User $parent){
        $this->parent = $parent;
    }

    public function getChildren(){
        return $this->children;
    }

    public function addChild(User $child){
        $this->children[] = $child;
    }

    public function removeChild(User $child){
        $this->children->removeElement($child);
        return $this;
    }

    public function getDistributions(){
        return $this->distributions;
    }

    public function addDistribution(Distribution $distribution){
        $this->distributions[] = $distribution;
        return $this;
    }

    public function removeDistribution(Distribution $distribution){
        $this->distributions->removeElement($distribution);
        return $this;
    }
    
    
    
}