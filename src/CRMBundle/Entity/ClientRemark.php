<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/14/16
 * Time: 5:35 PM
 */

namespace CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="client_remark")
 * @ORM\Entity
 */
class ClientRemark
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
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $uid;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $content;

    /**
     * @ORM\Column(type="string",nullable=true)
     */
    protected $updatedAt;

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

    public function getContent(){
        return $this->content;
    }

    public function setContent($content){
        $this->content = $content;
        return $this;
    }
}