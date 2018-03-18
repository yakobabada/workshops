<?php

namespace AppBundle\Repository;

use AppBundle\Entity\RotaSlotStaff;
use Doctrine\ORM\EntityRepository;

class RotaSlotStaffRepository extends EntityRepository
{
    /**
     * @param int $rotaId
     *
     * @return array
     */
    public function findDayNumbersByRota(int $rotaId) : array
    {
        $queryBuilder =  $this->getEntityManager()->createQueryBuilder();

        return $queryBuilder
            ->select('DISTINCT rota.dayNumber')
            ->from(RotaSlotStaff::class, 'rota')
            ->where('rota.rotaId = :rotaId')
            ->setParameter('rotaId', $rotaId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $rotaId
     *
     * @return array
     */
    public function findTotalWorkHoursPerDayByRota(int $rotaId) : array
    {
        $queryBuilder =  $this->getEntityManager()->createQueryBuilder();

        return $queryBuilder
            ->select('SUM(rota.workHours) as totalWorkHours, rota.dayNumber')
            ->from(RotaSlotStaff::class, 'rota')
            ->where('rota.rotaId = :rotaId')
            ->groupBy('rota.dayNumber')
            ->setParameter('rotaId', $rotaId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $rotaId
     *
     * @return array
     */
    public function findStaffShiftsByRota(int $rotaId) : array
    {
        $queryBuilder =  $this->getEntityManager()->createQueryBuilder();

        return $queryBuilder
            ->select('rota')
            ->from(RotaSlotStaff::class, 'rota')
            ->where('rota.rotaId = :rotaId')
            ->andWhere('rota.staffId is not null')
            ->andWhere('rota.slotType = :slotType')
            ->setParameter('rotaId', $rotaId)
            ->setParameter('slotType', 'shift')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $rotaId
     *
     * @return array
     */
    public function findActiveStaffByRota(int $rotaId) : array
    {
        $queryBuilder =  $this->getEntityManager()->createQueryBuilder();

        return $queryBuilder
            ->select('DISTINCT rota.staffId')
            ->from(RotaSlotStaff::class, 'rota')
            ->where('rota.rotaId = :rotaId')
            ->andWhere('rota.staffId is not null')
            ->andWhere('rota.slotType = :slotType')
            ->setParameter('rotaId', $rotaId)
            ->setParameter('slotType', 'shift')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param int $rotaId
     *
     * @return array
     */
    public function findActiveStaffRotaByRotaAndNumberDay(int $rotaId, int $dayNumber) : array
    {
        $queryBuilder =  $this->getEntityManager()->createQueryBuilder();

        return $queryBuilder
            ->select('rota')
            ->from(RotaSlotStaff::class, 'rota')
            ->where('rota.rotaId = :rotaId')
            ->andWhere('rota.staffId is not null')
            ->andWhere('rota.slotType = :slotType')
            ->setParameter('rotaId', $rotaId)
            ->setParameter('slotType', 'shift')
            ->andWhere('rota.dayNumber = :dayNumber')
            ->setParameter('dayNumber', $dayNumber)
            ->orderBy('rota.startTime')
            ->getQuery()
            ->getResult();
    }
}