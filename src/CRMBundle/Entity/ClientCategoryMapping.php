<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/14/16
 * Time: 5:01 PM
 */

namespace CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="client_category_mapping")
 * @ORM\Entity(repositoryClass="CRMBundle\Entity\ClientCategoryMappingRepository")
 */
class ClientCategoryMapping
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $uid;

    /**
     * @ORM\Column(type="integer",nullable=true)
     */
    protected $category_id;

    public function getId(){
        return $this->id;
    }

    public function getUid(){
        return $this->uid;
    }

    public function setUid($uid){
        $this->uid = $uid;
        return $this;
    }

    public function getCategoryId(){
        return $this->category_id;
    }

    public function setCategoryId($category_id){
        $this->category_id = $category_id;
        return $this;
    }

}