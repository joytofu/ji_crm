<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/24/16
 * Time: 3:27 PM
 */

namespace CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="knowledge_base")
 * @ORM\Entity()
 */
class KnowledgeBase
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $question;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $answer;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $lines;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    protected $editor;

    /**
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * @ORM\Column(type="text",nullable=true)
     */
    protected $remark;

    public function __construct()
    {
        $this->updatedAt = new \DateTime('now');
    }

    public function getId(){
        return $this->id;
    }

    public function getQuestion(){
        return $this->question;
    }

    public function setQuestion($question){
        if($question){
            $this->updatedAt = new \DateTime('now');
            $this->question = $question;
            return $this;
        }

    }

    public function getLines(){
        return $this->lines;
    }

    public function setLines($lines){
        $this->lines = $lines;
        return $this;
    }

    public function getAnswer(){
        return $this->answer;
    }

    public function setAnswer($answer){
        if($answer){
            $this->updatedAt = new \DateTime('now');
        }
        $this->answer = $answer;
        return $this;
    }

    public function getEditor(){
        return $this->editor;
    }

    public function setEditor($uid){
        $this->editor = $uid;
        return $this;
    }

    public function getUpdatedAt(){
        return $this->updatedAt;
    }

    public function getRemark(){
        return $this->remark;
    }

    public function setRemark($remark){
        $this->remark = $remark;
        return $this;
    }


}