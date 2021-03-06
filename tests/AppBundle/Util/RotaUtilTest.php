<?php

namespace Tests\AppBundle\Util;

use AppBundle\Entity\RotaSlotStaff;
use AppBundle\Repository\RotaSlotStaffRepository;
use AppBundle\Util\RotaUtil;
use AppBundle\Util\ShiftUtil;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class RotaUtilTest extends TestCase
{
    /**
     * @var RotaUtil
     */
    private $rotaUtil;

    private $entityManagerMock;

    private $fakeRotaSlotStaffRepository;

    public function setUp()
    {
        parent::setUp();

        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->rotaUtil = new RotaUtil($this->entityManagerMock, new ShiftUtil($this->entityManagerMock));

        $this->fakeRotaSlotStaffRepository = $this->createMock(RotaSlotStaffRepository::class);

        $this->entityManagerMock
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->fakeRotaSlotStaffRepository));
    }

    public function testGetDays()
    {
        $this->fakeRotaSlotStaffRepository
            ->expects($this->any())
            ->method('findDayNumbersByRota')
            ->willReturn([
                ['dayNumber' => 0],
                ['dayNumber' => 1],
                ['dayNumber' => 2],
            ]);

        $this->assertCount(3, $this->rotaUtil->getDays(12));
        $this->assertContains(1, $this->rotaUtil->getDays(12));
    }

    public function testGetStaffShiftsInDayNumber()
    {
        $rotaSlotStaff = (new RotaSlotStaff())
            ->setDayNumber(1)
            ->setStaffId(12);

        $this->fakeRotaSlotStaffRepository
            ->expects($this->any())
            ->method('findStaffShiftsByRota')
            ->willReturn([$rotaSlotStaff]);

        $this->assertCount(1, $this->rotaUtil->getStaffShiftsInDayNumber(12));
        $this->assertArrayHasKey(12, $this->rotaUtil->getStaffShiftsInDayNumber(12));
    }

    public function testGetActiveStaff()
    {
        $this->fakeRotaSlotStaffRepository
            ->expects($this->any())
            ->method('findActiveStaffByRota')
            ->willReturn([
                ['staffId' => 10],
                ['staffId' => 1],
                ['staffId' => 2],
            ]);

        $this->assertCount(3, $this->rotaUtil->getActiveStaff(12));
        $this->assertContains(1, $this->rotaUtil->getActiveStaff(12));
    }
}