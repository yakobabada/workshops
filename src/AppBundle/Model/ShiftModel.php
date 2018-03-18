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
     * @var []IntervalModel
     */
    private $groupWorkIntervals;

    /**
     * @var []IntervalModel
     */
    private $aloneWorkIntervals;

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
     * @return mixed
     */
    public function getGroupWorkIntervals()
    {
        return $this->groupWorkIntervals;
    }

    /**
     * @param $groupWorkIntervals
     * @return ShiftModel
     */
    public function setGroupWorkIntervals($groupWorkIntervals)
    {
        $this->groupWorkIntervals = $groupWorkIntervals;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAloneWorkIntervals()
    {
        return $this->aloneWorkIntervals;
    }

    /**
     * @param $aloneWorkIntervals
     *
     * @return ShiftModel
     */
    public function setAloneWorkIntervals($aloneWorkIntervals)
    {
        $this->aloneWorkIntervals = $aloneWorkIntervals;

        return $this;
    }


}