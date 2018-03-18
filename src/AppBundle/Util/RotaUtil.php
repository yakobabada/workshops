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
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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
     * @param $rotaId
     *
     * @return array
     */
    public function getTotalWorkHoursInDayNumber(int $rotaId) : array
    {
        $workHoursDayList = $this->entityManager->getRepository(RotaSlotStaff::class)->findTotalWorkHoursPerDayByRota($rotaId);

        $workHoursDays = [];

        foreach ($workHoursDayList as $workHoursDay) {
            $workHoursDays[$workHoursDay['dayNumber']] = $workHoursDay['totalWorkHours'];
        }

        return $workHoursDays;
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