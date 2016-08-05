<?php

namespace CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MappingLog
 *
 * @ORM\Table(name="mapping_log")
 * @ORM\Entity
 */
class MappingLog
{
    /**
     * @var integer
     *
     * @ORM\Column(name="MappingLogID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $mappinglogid;

    /**
     * @var integer
     *
     * @ORM\Column(name="UID", type="integer", nullable=false)
     */
    private $uid;

    /**
     * @var integer
     *
     * @ORM\Column(name="MID", type="integer", nullable=false)
     */
    private $mid;

    /**
     * @var integer
     *
     * @ORM\Column(name="Time", type="integer", nullable=false)
     */
    private $time;



    /**
     * Get mappinglogid
     *
     * @return integer
     */
    public function getMappinglogid()
    {
        return $this->mappinglogid;
    }

    /**
     * Set uid
     *
     * @param integer $uid
     *
     * @return MappingLog
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return integer
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set mid
     *
     * @param integer $mid
     *
     * @return MappingLog
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
     * Set time
     *
     * @param integer $time
     *
     * @return MappingLog
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return integer
     */
    public function getTime()
    {
        return $this->time;
    }
}
