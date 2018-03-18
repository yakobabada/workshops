<?php

namespace AppBundle\Util;

use AppBundle\Entity\RotaSlotStaff;
use Doctrine\ORM\EntityManager;

class RotaUtil
{
    /**
     * @var EntityManager
     */
    private $entityManager;
    /**
     * @var ShiftUtil
     */
    private $shiftUtil;

    /**
     * @param EntityManager $entityManager
     * @param ShiftUtil $shiftUtil
     */
    public function __construct(EntityManager $entityManager, ShiftUtil $shiftUtil)
    {
        $this->entityManager = $entityManager;
        $this->shiftUtil = $shiftUtil;
    }

    /**
     * @param $rotaId
     *
     * @return array
     */
    public function getDays(int $rotaId) : array
    {
        $dayNumberList = $this->entityManager->getRepository(RotaSlotStaff::class)->findDayNumbersByRota($rotaId);

        return array_column($dayNumberList, "dayNumber");
    }

    /**
     * @param int $rotaId
     *
     * @return array
     */
    public function getShiftList(int $rotaId) : array
    {
        $dayNumbers = $this->getDays($rotaId);
        $shiftList = [];

        foreach ($dayNumbers as $dayNumber) {
            $shiftList[$dayNumber] = $this->shiftUtil->get($rotaId, $dayNumber);
        }

        return $shiftList;
    }

    /**
     * @param $rotaId
     *
     * @return array
     */
    public function getStaffShiftsInDayNumber(int $rotaId) : array
    {
        $staffShiftsDayList = $this->entityManager->getRepository(RotaSlotStaff::class)->findStaffShiftsByRota($rotaId);

        $staffShiftsPerDay = [];

        foreach ($staffShiftsDayList as $staffShiftsDay) {
            $staffShiftsPerDay[$staffShiftsDay->getStaffId()][$staffShiftsDay->getDayNumber()] = $staffShiftsDay;
        }

        return $staffShiftsPerDay;
    }

    public function getActiveStaff(int $rotaId) : array
    {
        $dayNumberList = $this->entityManager->getRepository(RotaSlotStaff::class)->findActiveStaffByRota($rotaId);

        return array_column($dayNumberList, "staffId");
    }
}