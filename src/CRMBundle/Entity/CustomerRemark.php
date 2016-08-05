<?php

namespace CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CustomerRemark
 *
 * @ORM\Table(name="customer_remark", indexes={@ORM\Index(name="UID", columns={"UID"})})
 * @ORM\Entity
 */
class CustomerRemark
{
    /**
     * @var integer
     *
     * @ORM\Column(name="CustomerRemarkID", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $customerremarkid;

    /**
     * @var integer
     *
     * @ORM\Column(name="MID", type="integer", nullable=false)
     */
    private $mid;

    /**
     * @var integer
     *
     * @ORM\Column(name="UID", type="integer", nullable=false)
     */
    private $uid;

    /**
     * @var string
     *
     * @ORM\Column(name="Content", type="text", length=65535, nullable=false)
     */
    private $content;

    /**
     * @var integer
     *
     * @ORM\Column(name="Time", type="integer", nullable=false)
     */
    private $time;



    /**
     * Get customerremarkid
     *
     * @return integer
     */
    public function getCustomerremarkid()
    {
        return $this->customerremarkid;
    }

    /**
     * Set mid
     *
     * @param integer $mid
     *
     * @return CustomerRemark
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
     * Set uid
     *
     * @param integer $uid
     *
     * @return CustomerRemark
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
     * Set content
     *
     * @param string $content
     *
     * @return CustomerRemark
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set time
     *
     * @param integer $time
     *
     * @return CustomerRemark
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
