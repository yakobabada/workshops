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

    /**
     * @param int $rotaId
     * @param int $dayNumber
     *
     * @return ShiftModel
     */
    public function get(int $rotaId, int $dayNumber) : ShiftModel
    {
        $rotaSlotStaffList = $this->entityManager
            ->getRepository(RotaSlotStaff::class)
            ->findActiveStaffRotaByRotaAndNumberDay($rotaId, $dayNumber);

        $totalShiftInterval = $this->getTotalShiftInterval($rotaSlotStaffList);
        $workingGroupIntervals = $this->calculateWorkingGroupIntervals($rotaSlotStaffList);
        $aloneWorkIntervals = $this->calculateWorkingAloneIntervals($totalShiftInterval, $workingGroupIntervals);

        return (new ShiftModel())
            ->setDayNumber($dayNumber)
            ->setDayInterval($totalShiftInterval)
            ->setWorkingGroupIntervals($workingGroupIntervals)
            ->setWorkingAloneIntervals($aloneWorkIntervals);
    }


    /**
     * @param array $rotaSlotStaffList
     *
     * @return array
     */
    private function calculateWorkingGroupIntervals(array $rotaSlotStaffList)
    {
        $rotaSlotStaffCollection = new ArrayCollection($rotaSlotStaffList);

        $workingGroupIntervals = [];

        for ($i=0;$i<$rotaSlotStaffCollection->count();$i++) {
            $currentMember = $rotaSlotStaffCollection->current();

            $shiftInterval = new IntervalModel($currentMember->getStartTime(), $currentMember->getEndTime());

            $nextMember = $rotaSlotStaffCollection->next();

            if (!$nextMember) {
                continue;
            }

            $nextShiftInterval = new IntervalModel($nextMember->getStartTime(), $nextMember->getEndTime());

            if ($this->areMembersWorkSameTime($shiftInterval, $nextShiftInterval)) {
                $workingGroupIntervals = $this->addToWorkingGroupIntervals(
                    $workingGroupIntervals,
                    $this->getWorkingGroupInterval($shiftInterval, $nextShiftInterval)
                );
            }
        }

        return $workingGroupIntervals;
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
    private function getWorkingGroupInterval(IntervalModel $shiftInterval, IntervalModel $nextShiftInterval)
    {
        return $shiftInterval->getOverLapInterval($nextShiftInterval);
    }

    /**
     * @param array $workingGroupIntervals
     * @param IntervalModel $interval
     *
     * @return array
     */
    private function addToWorkingGroupIntervals(array $workingGroupIntervals, IntervalModel $interval) : array
    {
        foreach ($workingGroupIntervals as $workingGroupInterval) {
            if ($workingGroupInterval->IsTouching($interval)) {
                $workingGroupInterval->concatenate($interval);
                return $workingGroupIntervals;
            }
        }

        $workingGroupIntervals[] = $interval;

        return $workingGroupIntervals;
    }

    /**
     * @param array $rotaSlotStaffCollection
     *
     * @return IntervalModel
     */
    private function getTotalShiftInterval($rotaSlotStaffCollection) : IntervalModel
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
     * Assume entire day is alone work interval then remove all work intervals from it, the remain is
     * alone work.
     *
     * @param IntervalModel $totalShiftInterval
     * @param array $workingGroupIntervals
     *
     * @return array
     */
    private function calculateWorkingAloneIntervals(IntervalModel $totalShiftInterval, array $workingGroupIntervals) : array
    {
        $totalWorkingAloneIntervals = [$totalShiftInterval];

        foreach ($workingGroupIntervals as $workingGroupInterval) {
            $totalWorkingAloneIntervals = $this->inspectWorkingAloneIntervalsAgainstWorkingGroupInterval(
                $totalWorkingAloneIntervals,
                $workingGroupInterval
            );
        }

        return $totalWorkingAloneIntervals;
    }

    /**
     * @param array $totalWorkingAloneIntervals
     * @param $workingGroupInterval
     *
     * @return array
     */
    private function inspectWorkingAloneIntervalsAgainstWorkingGroupInterval(
        array $totalWorkingAloneIntervals,
        IntervalModel $workingGroupInterval
    ) {
        for($i=0;$i<count($totalWorkingAloneIntervals);$i++) {
            if ($totalWorkingAloneIntervals[$i]->IsOverlap($workingGroupInterval)) {
                $workingAloneIntervals = $this->getWorkingAloneIntervals(
                    $totalWorkingAloneIntervals[$i],
                    $workingGroupInterval
                );

                unset($totalWorkingAloneIntervals[$i]);
                $totalWorkingAloneIntervals = array_merge($totalWorkingAloneIntervals, $workingAloneIntervals);
            }
        }

        return array_values($totalWorkingAloneIntervals);
    }

    /**
     * @param $totalWorkingAloneInterval
     * @param $workingGroupInterval
     * @return mixed
     */
    private function getWorkingAloneIntervals($totalWorkingAloneInterval, $workingGroupInterval)
    {
        return $totalWorkingAloneInterval->getNoneOverlapIntervals($workingGroupInterval);
    }
}