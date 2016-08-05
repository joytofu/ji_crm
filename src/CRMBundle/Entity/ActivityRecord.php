<?php
/**
 * Created by PhpStorm.
 * User: kcswag
 * Date: 6/25/16
 * Time: 6:50 PM
 */

namespace CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="activity_record")
 * @ORM\Entity()
 */
class ActivityRecord
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="date")
     */
    protected $date;

    /**
     * @ORM\Column(type="text")
     */
    protected $detail;

    /**
     * @ORM\Column(type="string")
     */
    protected $status;

    /**
     * @ORM\Column(type="float")
     */
    protected $amount;

    /**
     * @ORM\Column(type="integer")
     */
    protected $investorCount;

    /**
     * @ORM\Column(type="integer")
     */
    protected $registrationCount;

    /**
     * @ORM\Column(type="integer")
     */
    protected $verificationCount;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     *
     * @return ActivityRecord
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set detail
     *
     * @param string $detail
     *
     * @return ActivityRecord
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;

        return $this;
    }

    /**
     * Get detail
     *
     * @return string
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return ActivityRecord
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set amount
     *
     * @param float $amount
     *
     * @return ActivityRecord
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set investorCount
     *
     * @param integer $investorCount
     *
     * @return ActivityRecord
     */
    public function setInvestorCount($investorCount)
    {
        $this->investorCount = $investorCount;

        return $this;
    }

    /**
     * Get investorCount
     *
     * @return integer
     */
    public function getInvestorCount()
    {
        return $this->investorCount;
    }

    /**
     * Set registrationCount
     *
     * @param integer $registrationCount
     *
     * @return ActivityRecord
     */
    public function setRegistrationCount($registrationCount)
    {
        $this->registrationCount = $registrationCount;

        return $this;
    }

    /**
     * Get registrationCount
     *
     * @return integer
     */
    public function getRegistrationCount()
    {
        return $this->registrationCount;
    }

    /**
     * Set verificationCount
     *
     * @param integer $verificationCount
     *
     * @return ActivityRecord
     */
    public function setVerificationCount($verificationCount)
    {
        $this->verificationCount = $verificationCount;

        return $this;
    }

    /**
     * Get verificationCount
     *
     * @return integer
     */
    public function getVerificationCount()
    {
        return $this->verificationCount;
    }
}
