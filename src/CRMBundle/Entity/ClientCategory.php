<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 5/15/16
 * Time: 4:24 PM
 */

namespace CRMBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="client_category")
 * @ORM\Entity(repositoryClass="CRMBundle\Entity\ClientCategoryRepository")
 */
class ClientCategory
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $categoryName;


    /**
     * @ORM\OneToMany(targetEntity="CRMBundle\Entity\Distribution",mappedBy="category")
     */
    protected $distributions;
    

    public function __construct()
    {
        $this->distributions = new ArrayCollection();
        $this->mappings = new ArrayCollection();
    }

    public function getId(){
        return $this->id;
    }

    public function getName(){
        return $this->categoryName;
    }

    public function setName($name){
        $this->categoryName = $name;
    }
    
    
    public function getDistributions(){
        return $this->distributions;
    }
    
    public function addDisdtribution(Distribution $distribution){
        $this->distributions[] = $distribution;
        return $this;
    }
    
    public function removeDistribution(Distribution $distribution){
        $this->distributions->removeElement($distribution);
        return $this;
    }
    
    

}