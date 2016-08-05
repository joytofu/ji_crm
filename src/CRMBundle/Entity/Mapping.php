<?php

namespace CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mapping
 *
 * @ORM\Table(name="mapping", indexes={@ORM\Index(name="LastFollowTime", columns={"LastFollowTime"}), @ORM\Index(name="MID", columns={"MID"})})
 * @ORM\Entity
 */
class Mapping
{
    /**
     * @var integer
     *
     * @ORM\Column(name="ID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer",name="UID")
     */
    private $uid;

    /**
     * @var integer
     *
     * @ORM\Column(name="MID", type="integer", nullable=false)
     */
    private $mid = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="LastFollowTime", type="integer", nullable=false)
     */
    private $lastfollowtime = '0';

    /**
     * @var integer
     *
     * @ORM\Column(name="DistributedBy", type="integer", nullable=true)
     */
    private $distributedby;



    public function getId(){
        return $this->id;
    }
    
    public function getUid()
    {
        return $this->uid;
    }

    public function setUid($uid){
        $this->uid = $uid;
    }

    /**
     * Set mid
     *
     * @param integer $mid
     *
     * @return Mapping
     */
    public function setMid($mid)
    {
        $this->mid = $mid;

        return $this;
    }

    /**
     * Get mid
     *
     * @return integer
     */
    public function getMid()
    {
        return $this->mid;
    }

    /**
     * Set lastfollowtime
     *
     * @param integer $lastfollowtime
     *
     * @return Mapping
     */
    public function setLastfollowtime($lastfollowtime)
    {
        $this->lastfollowtime = $lastfollowtime;

        return $this;
    }

    /**
     * Get lastfollowtime
     *
     * @return integer
     */
    public function getLastfollowtime()
    {
        return $this->lastfollowtime;
    }

    /**
     * Set distributedby
     *
     * @param integer $distributedby
     *
     * @return Mapping
     */
    public function setDistributedby($distributedby)
    {
        $this->distributedby = $distributedby;

        return $this;
    }

    /**
     * Get distributedby
     *
     * @return integer
     */
    public function getDistributedby()
    {
        return $this->distributedby;
    }
}
