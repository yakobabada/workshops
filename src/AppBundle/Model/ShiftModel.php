<?php

namespace AppBundle\Model;

class ShiftModel
{
    /**
     * @var IntervalModel
     */
    private $dayInterval;

    /**
     * @var int
     */
    private $dayNumber;

    /**
     * @var array
     */
    private $workingGroupIntervals;

    /**
     * @var array
     */
    private $workingAloneIntervals;

    /**
     * @var int
     */
    private $totalWorkingHours;

    /**
     * @return IntervalModel
     */
    public function getDayInterval()
    {
        return $this->dayInterval;
    }

    /**
     * @param $dayInterval
     *
     * @return ShiftModel
     */
    public function setDayInterval($dayInterval)
    {
        $this->dayInterval = $dayInterval;

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
     * @return ShiftModel
     */
    public function setDayNumber($dayNumber)
    {
        $this->dayNumber = $dayNumber;

        return $this;
    }

    /**
     * @return array
     */
    public function getWorkingGroupIntervals()
    {
        return $this->workingGroupIntervals;
    }

    /**
     * @param $workingGroupIntervals
     *
     * @return ShiftModel
     */
    public function setWorkingGroupIntervals($workingGroupIntervals)
    {
        $this->workingGroupIntervals = $workingGroupIntervals;

        return $this;
    }

    /**
     * @return array
     */
    public function getWorkingAloneIntervals()
    {
        return $this->workingAloneIntervals;
    }

    /**
     * @return int
     */
    public function getTotalWorkingAloneInMinutes()
    {
        $workingAlone = 0;

        foreach ($this->workingAloneIntervals as $workingAloneInterval) {
            $workingAlone += abs(
                $workingAloneInterval->getStartTime()->getTimestamp() -
                $workingAloneInterval->getEndTime()->getTimestamp()
            ) / 60;
        }

        return $workingAlone;
    }

    /**
     * @param $workingAloneIntervals
     *
     * @return ShiftModel
     */
    public function setWorkingAloneIntervals($workingAloneIntervals)
    {
        $this->workingAloneIntervals = $workingAloneIntervals;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotalWorkingHours()
    {
        return $this->totalWorkingHours;
    }

    /**
     * @param int $totalWorkingHours
     *
     * @return ShiftModel
     */
    public function setTotalWorkingHours($totalWorkingHours)
    {
        $this->totalWorkingHours = $totalWorkingHours;

        return $this;
    }
}