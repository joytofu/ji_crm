<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/25/16
 * Time: 11:02 AM
 */

namespace CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="case_base")
 * @ORM\Entity()
 */
class CaseBase
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer")
     */
    protected $mid;

    /**
     * @ORM\Column(type="string")
     */
    protected $clientName;

    /**
     * @ORM\Column(type="string")
     */
    protected $contactMethod;

    /**
     * @ORM\Column(type="string")
     * @Assert\Length(max="11",maxMessage="手机号码过长，请重新输入！")
     * @Assert\Length(min="11",minMessage="手机号码过短，请重新输入！")
     * @Assert\Regex(pattern="/^(13[0-9]|15[012356789]|18[0236789]|14[57])[0-9]{8}$/", message="手机号码不正确，请重新输入!")
     */
    protected $cellphone;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * @ORM\Column(type="text")
     */
    protected $question;

    /**
     * @ORM\Column(type="text")
     */
    protected $answer;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $ifSolved;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $assistantDepartment;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $helper;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $remark;

    /**
     * @ORM\Column(type="date",nullable=true)
     */
    protected $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
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

    public function getClientName(){
        return $this->clientName;
    }

    public function setClientName($name){
        $this->clientName = $name;
        return $this;
    }

    public function getContactMethod(){
        return $this->contactMethod;
    }

    public function setContactMethod($method){
        $this->contactMethod = $method;
        return $this;
    }

    public function getCellphone(){
        return $this->cellphone;
    }

    public function setCellphone($cellphone){
        $this->cellphone = $cellphone;
        return $this;
    }

    public function getType(){
        return $this->type;
    }

    public function setType($type){
        $this->type = $type;
        return $this;
    }

    public function getQuestion(){
        return $this->question;
    }

    public function setQuestion($question){
        $this->question = $question;
    }

    public function getAnswer(){
        return $this->answer;
    }

    public function setAnswer($answer){
        $this->answer = $answer;
        return $this;
    }

    public function getIfSolved(){
        return $this->ifSolved;
    }

    public function setIfSolved($boolean){
        $this->ifSolved = (boolean)$boolean;
        return $this;
    }

    public function getAssistantDepartment(){
        return $this->assistantDepartment;
    }

    public function setAssistantDepartment($department){
        $this->assistantDepartment = $department;
        return $this;
    }

    public function getHelper(){
        return $this->helper;
    }

    public function setHelper($helper){
        $this->helper = $helper;
        return $this;
    }

    public function getRemark(){
        return $this->remark;
    }

    public function setRemark($remark){
        $this->remark = $remark;
        return $this;
    }

    public function getCreatedAt(){
        return $this->createdAt;
    }

}