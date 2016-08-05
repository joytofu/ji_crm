<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/1/16
 * Time: 10:49 AM
 */

namespace UserBundle\Entity;

use CRMBundle\Entity\KnowledgeBase;
use CRMBundle\Entity\TrackingRecord;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="UserBundle\Entity\UserInfoRepository")
 * @ORM\Table(name="user_info")
 */
class UserInfo
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @Assert\Length(max="11",maxMessage="手机号码过长，请重新输入！")
     * @Assert\Length(min="11",minMessage="手机号码过短，请重新输入！")
     * @Assert\Regex(pattern="/^(13[0-9]|15[012356789]|18[0236789]|14[57])[0-9]{8}$/", message="手机号码不正确，请重新输入!")
     */
    protected $cellPhone;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $department;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $position;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    protected $hireDate;

    /**
     * @ORM\OneToMany(targetEntity="CRMBundle\Entity\TrackingRecord",mappedBy="handler")
     */
    protected $trackingRecords;

    /**
     * @ORM\OneToOne(targetEntity="UserBundle\Entity\User",inversedBy="userInfo",cascade={"persist"})
     */
    protected $user;
    
    
    public function __construct()
    {
        $this->trackingRecords = new ArrayCollection();
    }

    public function getId(){
        return $this->id;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function getCellphone(){
        return $this->cellPhone;
    }

    public function setCellphone($cellphone){
        $this->cellPhone = $cellphone;
    }

    public function getDepartment(){
        return $this->department;
    }

    public function setDepartment($department){
        $this->department = $department;
    }

    public function getPosition(){
        return $this->position;
    }

    public function setPosition($position){
        $this->position = $position;
    }

    public function getHireDate(){
        return $this->hireDate;
    }

    public function setHireDate($hireDate){
        $this->hireDate = $hireDate;
    }
    
    public function getTrackingRecords(){
        return $this->trackingRecords;
    }
    
    public function addTrackingRecord(TrackingRecord $record){
        $this->trackingRecords[] = $record;
        return $this;
    }
    
    public function removeTrackingRecord(TrackingRecord $record){
        $this->trackingRecords->removeElement($record);
        return $this;
    }

    public function getUser(){
        return $this->user;
    }

    public function setUser(User $user){
        $this->user = $user;
    }
    
}