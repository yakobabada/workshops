<?php

namespace AppBundle\Model;

class IntervalModel
{
    /**
     * @var \DateTime
     */
    private $startTime;

    /**
     * @var \DateTime
     */
    private $endTime;

    /**
     * @param \DateTime $startTime
     * @param \DateTime $endTime
     */
    public function __construct(\DateTime $startTime, \DateTime $endTime)
    {
        $this->startTime = $startTime;
        $this->endTime = $endTime;
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
     * @return IntervalModel
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
        return $this->endTime;
    }

    /**
     * @param $endTime
     *
     * @return IntervalModel
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * @param IntervalModel $interval
     *
     * @return bool
     */
    public function IsTouching(IntervalModel $interval)
    {
        if (max($this->getStartTime(), $interval->getStartTime()) <= min($this->getEndTime(), $interval->getEndTime())) {
            return true;
        }

        return false;
    }

    /**
     * @param IntervalModel $interval
     *
     * @return bool
     */
    public function IsOverlap(IntervalModel $interval)
    {
        if (max($this->getStartTime(), $interval->getStartTime()) < min($this->getEndTime(), $interval->getEndTime())) {
            return true;
        }

        return false;
    }

    /**
     * @param IntervalModel $interval
     */
    public function concatenate(IntervalModel $interval)
    {
        if ($this->IsTouching($interval)) {
            $this->setStartTime(min($this->getStartTime(), $interval->getStartTime()));
            $this->setEndTime(max($this->getEndTime(), $interval->getEndTime()));
        }
    }

    /**
     * @param IntervalModel $interval
     *
     * @return IntervalModel|null
     */
    public function getOverLapInterval(IntervalModel $interval)
    {
        if ($this->IsOverlap($interval)) {
            return new IntervalModel(
                max($this->getStartTime(), $interval->getStartTime()),
                min($this->getEndTime(), $interval->getEndTime())
            );
        }

        return null;
    }

    public function getNoneOverlapIntervals(IntervalModel $interval)
    {
        $intervals = [];

        if ($this->IsOverlap($interval)) {
            if ($interval->getStartTime() > $this->getStartTime()) {
                $intervals[] = new IntervalModel($this->getStartTime(), $interval->getStartTime());
            }

            if ($interval->getEndTime() < $this->getEndTime()) {
                $intervals[] = new IntervalModel($interval->getEndTime(),  $this->getEndTime());
            }

            return $intervals;
        }

        return null;
    }

    /**
     * @return \DateInterval
     */
    public function getDiffBetweenStartAndEndTime() : \DateInterval
    {
        return $this->startTime->diff($this->endTime);
    }
}

