<?php

namespace AppBundle\Util;

use AppBundle\Entity\RotaSlotStaff;
use AppBundle\Model\IntervalModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

class ShiftUtil
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    private $workAloneInMinute = 0;

    /**
     * @var []IntervalModel
     */
    private $groupWorkIntervals = [];

    /**
     * @var IntervalModel
     */
    private $totalShiftInterval;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function calculateShiftIntervals(int $rotaId, int $dayNumber)
    {
        $rotaSlotStaffList = $this->entityManager
            ->getRepository(RotaSlotStaff::class)
            ->findActiveStaffRotaByRotaAndNumberDay($rotaId, $dayNumber);

        $rotaSlotStaffCollection = new ArrayCollection($rotaSlotStaffList);

         $this->totalShiftInterval = $this->getTotalShiftInterval($rotaSlotStaffCollection);

        $this->calculateGroupWorkIntervals($rotaSlotStaffCollection);

        $this->AloneWorkIntervals = $this->calculateAloneWorkIntervals($this->totalShiftInterval, $this->groupWorkIntervals);

        dump($this->AloneWorkIntervals);
    }



    /**
     * @return mixed
     */
    public function getGroupWorkIntervals()
    {
        return $this->groupWorkIntervals;
    }

    private function calculateGroupWorkIntervals(ArrayCollection $rotaSlotStaffCollection)
    {
        for ($i=0;$i<$rotaSlotStaffCollection->count();$i++) {
            $currentMember = $rotaSlotStaffCollection->current();

            $shiftInterval = new IntervalModel($currentMember->getStartTime(), $currentMember->getEndTime());

            $nextMember = $rotaSlotStaffCollection->next();

            if (!$nextMember) {
                continue;
            }

            $nextShiftInterval = new IntervalModel($nextMember->getStartTime(), $nextMember->getEndTime());

            if ($this->areMembersWorkSameTime($shiftInterval, $nextShiftInterval)) {
                $this->addToGroupWorkIntervals($this->getGroupWorkInterval($shiftInterval, $nextShiftInterval));
            }
        }
    }

    /**
     * @param IntervalModel $shiftInterval
     * @param IntervalModel $nextShiftInterval
     *
     * @return bool
     */
    private function areMembersWorkSameTime(IntervalModel $shiftInterval, IntervalModel $nextShiftInterval)
    {
        return $shiftInterval->IsOverlap($nextShiftInterval);
    }

    /**
     * @param IntervalModel $shiftInterval
     * @param IntervalModel $nextShiftInterval
     *
     * @return IntervalModel|null
     */
    private function getGroupWorkInterval(IntervalModel $shiftInterval, IntervalModel $nextShiftInterval)
    {
        return $shiftInterval->getOverLapInterval($nextShiftInterval);
    }

    /**
     * @return int
     */
    public function getWorkAloneInMinute()
    {
        return $this->workAloneInMinute;
    }

    /**
     * @param int $workAloneInMinute
     */
    public function setWorkAloneInMinute($workAloneInMinute)
    {
        $this->workAloneInMinute = $workAloneInMinute;
    }

    /**
     * @param IntervalModel $interval
     */
    public function addToGroupWorkIntervals(IntervalModel $interval)
    {
        foreach ($this->groupWorkIntervals as $groupWorkInterval) {
            if ($groupWorkInterval->IsTouching($interval)) {
                $groupWorkInterval->concatenate($interval);
                return;
            }
        }

        $this->groupWorkIntervals[] = $interval;
    }

    /**
     * @param ArrayCollection $rotaSlotStaffCollection
     *
     * @return IntervalModel
     */
    public function getTotalShiftInterval(ArrayCollection $rotaSlotStaffCollection) : IntervalModel
    {
        $startTime = null;
        $endTime = null;

        foreach ($rotaSlotStaffCollection as $rotaSlotStaff) {
            if (null === $startTime || $rotaSlotStaff->getStartTime() < $startTime) {
                $startTime = $rotaSlotStaff->getStartTime();
            }

            if (null === $endTime || $rotaSlotStaff->getEndTime() > $endTime) {
                $endTime = $rotaSlotStaff->getEndTime();
            }
        }

        return new IntervalModel($startTime, $endTime);
    }

    private function calculateAloneWorkIntervals($totalShiftInterval, $groupWorkIntervals)
    {
        $totalAloneIntervals = [$totalShiftInterval];

        foreach ($groupWorkIntervals as $groupWorkInterval) {
            for($i=0;$i<count($totalAloneIntervals);$i++) {
                if ($totalAloneIntervals[$i]->IsOverlap($groupWorkInterval)) {
                    $noneOverlapInterval = $totalAloneIntervals[$i]->getNoneOverlapIntervals($groupWorkInterval);
                    unset($totalAloneIntervals[$i]);
                    $totalAloneIntervals = array_merge($totalAloneIntervals, $noneOverlapInterval);
                }
            }

            $totalAloneIntervals = array_values($totalAloneIntervals);
        }

        return $totalAloneIntervals;
    }
}