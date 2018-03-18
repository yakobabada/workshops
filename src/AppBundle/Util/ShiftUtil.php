<?php

namespace AppBundle\Util;

use AppBundle\Entity\RotaSlotStaff;
use AppBundle\Model\IntervalModel;
use AppBundle\Model\ShiftModel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

class ShiftUtil
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function get(int $rotaId, int $dayNumber)
    {
        $rotaSlotStaffList = $this->entityManager
            ->getRepository(RotaSlotStaff::class)
            ->findActiveStaffRotaByRotaAndNumberDay($rotaId, $dayNumber);

        $totalShiftInterval = $this->getTotalShiftInterval($rotaSlotStaffList);
        $groupWorkIntervals = $this->calculateGroupWorkIntervals($rotaSlotStaffList);
        $aloneWorkIntervals = $this->calculateAloneWorkIntervals($totalShiftInterval, $groupWorkIntervals);

        return (new ShiftModel())
            ->setDayNumber($dayNumber)
            ->setDayInterval($totalShiftInterval)
            ->setGroupWorkIntervals($groupWorkIntervals)
            ->setAloneWorkIntervals($aloneWorkIntervals);
    }


    /**
     * @param array $rotaSlotStaffList
     *
     * @return array
     */
    private function calculateGroupWorkIntervals(array $rotaSlotStaffList)
    {
        $rotaSlotStaffCollection = new ArrayCollection($rotaSlotStaffList);

        $groupWorkIntervals = [];

        for ($i=0;$i<$rotaSlotStaffCollection->count();$i++) {
            $currentMember = $rotaSlotStaffCollection->current();

            $shiftInterval = new IntervalModel($currentMember->getStartTime(), $currentMember->getEndTime());

            $nextMember = $rotaSlotStaffCollection->next();

            if (!$nextMember) {
                continue;
            }

            $nextShiftInterval = new IntervalModel($nextMember->getStartTime(), $nextMember->getEndTime());

            if ($this->areMembersWorkSameTime($shiftInterval, $nextShiftInterval)) {
                $groupWorkIntervals = $this->addToGroupWorkIntervals($groupWorkIntervals, $this->getGroupWorkInterval($shiftInterval, $nextShiftInterval));
            }
        }

        return $groupWorkIntervals;
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
     * @param array $groupWorkIntervals
     * @param IntervalModel $interval
     *
     * @return array
     */
    public function addToGroupWorkIntervals(array $groupWorkIntervals, IntervalModel $interval) : array
    {
        foreach ($groupWorkIntervals as $groupWorkInterval) {
            if ($groupWorkInterval->IsTouching($interval)) {
                $groupWorkInterval->concatenate($interval);
                return $groupWorkIntervals;
            }
        }

        $groupWorkIntervals[] = $interval;

        return $groupWorkIntervals;
    }

    /**
     * @param array $rotaSlotStaffCollection
     *
     * @return IntervalModel
     */
    public function getTotalShiftInterval($rotaSlotStaffCollection) : IntervalModel
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

    /**
     * @param $totalShiftInterval
     * @param $groupWorkIntervals
     *
     * @return array
     */
    private function calculateAloneWorkIntervals($totalShiftInterval, $groupWorkIntervals) : array
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