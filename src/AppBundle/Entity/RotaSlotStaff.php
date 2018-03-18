<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RotaSlotStaffRepository")
 */
class RotaSlotStaff
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="rotaid")
     */
    private $rotaId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", name="posted_at", name="daynumber")
     */
    private $dayNumber;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true, name="staffid")
     */
    private $staffId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20, name="slottype")
     */
    private $slotType;

    /**
     * @var \Time
     *
     * @ORM\Column(type="time", nullable=true, name="starttime")
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="time", nullable=true, name="endtime")
     */
    private $endTime;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", scale=2, nullable=true, name="workhours")
     */
    private $workHours;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true, name="premiumminutes")
     */
    private $premiumMinutes;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true, name="roletypeid")
     */
    private $roleTypeId;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true, name="freeminutes")
     */
    private $freeMinutes;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true, name="seniorcashierminutes")
     */
    private $seniorCashierMinutes;

    /**
     * @var int
     *
     * @ORM\Column(type="string", length=200, nullable=true, name="splitshifttimes")
     */
    private $splitShiftTimes;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getRotaId()
    {
        return $this->rotaId;
    }

    /**
     * @param $rotaId
     *
     * @return $this
     */
    public function setRotaId($rotaId)
    {
        $this->rotaId = $rotaId;

        return $this;
    }

    /**
     * @return int
     */
    public function getDayNumber()
    {
        return $this->dayNumber;
    }

    /**
     * @param $dayNumber
     *
     * @return $this
     */
    public function setDayNumber($dayNumber)
    {
        $this->dayNumber = $dayNumber;

        return $this;
    }

    /**
     * @return int
     */
    public function getStaffId()
    {
        return $this->staffId;
    }

    /**
     * @param $staffId
     *
     * @return $this
     */
    public function setStaffId($staffId)
    {
        $this->staffId = $staffId;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlotType()
    {
        return $this->slotType;
    }

    /**
     * @param $slotType
     *
     * @return $this
     */
    public function setSlotType($slotType)
    {
        $this->slotType = $slotType;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param $startTime
     *
     * @return $this
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
        if ($this->getStartTime() > $this->endTime) {
            return $this->endTime->add((new \DateInterval('P1D')));
        }

        return $this->endTime;
    }

    /**
     * @param $endTime
     *
     * @return $this
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * @return float
     */
    public function getWorkHours()
    {
        return $this->workHours;
    }

    /**
     * @param $workHours
     *
     * @return $this
     */
    public function setWorkHours($workHours)
    {
        $this->workHours = $workHours;

        return $this;
    }

    /**
     * @return int
     */
    public function getPremiumMinutes()
    {
        return $this->premiumMinutes;
    }

    /**
     * @param $premiumMinutes
     *
     * @return $this
     */
    public function setPremiumMinutes($premiumMinutes)
    {
        $this->premiumMinutes = $premiumMinutes;

        return $this;
    }

    /**
     * @return int
     */
    public function getRoleTypeId()
    {
        return $this->roleTypeId;
    }

    /**
     * @param $roleTypeId
     *
     * @return $this
     */
    public function setRoleTypeId($roleTypeId)
    {
        $this->roleTypeId = $roleTypeId;

        return $this;
    }

    /**
     * @return int
     */
    public function getFreeMinutes()
    {
        return $this->freeMinutes;
    }

    /**
     * @param $freeMinutes
     *
     * @return $this
     */
    public function setFreeMinutes($freeMinutes)
    {
        $this->freeMinutes = $freeMinutes;

        return $this;
    }

    /**
     * @return int
     */
    public function getSeniorCashierMinutes()
    {
        return $this->seniorCashierMinutes;
    }

    /**
     * @param $seniorCashierMinutes
     *
     * @return $this
     */
    public function setSeniorCashierMinutes($seniorCashierMinutes)
    {
        $this->seniorCashierMinutes = $seniorCashierMinutes;

        return $this;
    }

    /**
     * @return int
     */
    public function getSplitShiftTimes()
    {
        return $this->splitShiftTimes;
    }

    /**
     * @param $splitShiftTimes
     *
     * @return $this
     */
    public function setSplitShiftTimes($splitShiftTimes)
    {
        $this->splitShiftTimes = $splitShiftTimes;

        return $this;
    }
}