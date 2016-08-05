<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 5/20/16
 * Time: 5:11 PM
 */

namespace CRMBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping as ORM;
use UserBundle\Entity\User;
use UserBundle\Entity\UserInfo;

/**
 * @ORM\Table(name="tracking_record")
 * @ORM\Entity(repositoryClass="CRMBundle\Entity\TrackingRecordRepository")
 */
class TrackingRecord
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
    protected $mid;

    /**
     * @ORM\Column(type="integer",nullable=false)
     */
    protected $uid;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $contactMethod;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $type;

    /**
     * @ORM\Column(type="text")
     */
    protected $content;

    /**
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\UserInfo",inversedBy="trackingRecords",cascade={"persist"})
     */
    protected $handler;

    /**
     * @ORM\Column(type="datetime",nullable=true)
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    protected $updatedAt;
    
    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updateAt = new \DateTime();
    }

    public function getId(){
        return $this->id;
    }
    
    public function getMid(){
        return $this->mid;
    }
    
    public function setMid($mid){
        $this->mid = $mid;
        return $this;
    }
    
    public function getUid(){
        return $this->uid;
    }

    public function setUid($uid){
        $this->uid = $uid;
        return $this;
    }

    public function getContactMethod(){
        return $this->contactMethod;
    }

    public function setContactMethod($method){
        $this->contactMethod = $method;
    }

    public function getType(){
        return $this->type;
    }

    public function setType($type){
        $this->type = $type;
    }
    
    public function getContent(){
        return $this->content;
    }
    
    public function setContent($content){
        $this->content = $content;
        if($content){
            $this->updateAt = time();
        }
    }
    
    public function getCreatedAt(){
        return $this->createdAt;
    }
    
    public function getUpdatedAt(){
        return $this->updatedAt;
    }

    public function getHandler(){
        return $this->handler;
    }

    public function setHandler(UserInfo $info){
        $this->handler = $info;
        return $this;
    }
    
}