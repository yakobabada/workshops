<?php

namespace Tests\AppBundle\Util;

use AppBundle\Entity\RotaSlotStaff;
use AppBundle\Model\IntervalModel;
use AppBundle\Model\ShiftModel;
use AppBundle\Repository\RotaSlotStaffRepository;
use AppBundle\Util\ShiftUtil;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

class ShiftUtilTest extends TestCase
{
    /**
     * @var ShiftUtil
     */
    private $shiftUtil;

    private $entityManagerMock;

    private $fakeRotaSlotStaffRepository;

    public function setUp()
    {
        parent::setUp();

        $this->entityManagerMock = $this->getMockBuilder(EntityManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->shiftUtil = new ShiftUtil($this->entityManagerMock);

        $this->fakeRotaSlotStaffRepository = $this->createMock(RotaSlotStaffRepository::class);

        $this->entityManagerMock
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->fakeRotaSlotStaffRepository));
    }

    public function testGet()
    {
        $rotaSlotStaff = (new RotaSlotStaff())
            ->setDayNumber(1)
            ->setStaffId(12)
            ->setWorkHours(8)
            ->setStartTime((new \DateTime())->setTime(8, 0))
            ->setEndTime((new \DateTime())->setTime(11, 0));

        $rotaSlotStaff2 = (new RotaSlotStaff())
            ->setDayNumber(1)
            ->setStaffId(12)
            ->setWorkHours(8)
            ->setStartTime((new \DateTime())->setTime(8, 0))
            ->setEndTime((new \DateTime())->setTime(14, 0));

        $this->fakeRotaSlotStaffRepository
            ->expects($this->any())
            ->method('findActiveStaffRotaByRotaAndNumberDay')
            ->willReturn([$rotaSlotStaff, $rotaSlotStaff2]);

        $this->shiftUtil->get(12, 1);
        $this->assertInstanceOf(ShiftModel::class, $this->shiftUtil->get(12, 1));

        $this->assertEquals(
            new IntervalModel(
                (new \DateTime())->setTime(8, 0),
                (new \DateTime())->setTime(14, 0)
            ),
            $this->shiftUtil->get(12, 1)->getDayInterval()
        );

        $this->assertEquals(16, $this->shiftUtil->get(12, 1)->getTotalWorkingHours());

        $this->assertContainsOnlyInstancesOf(
            IntervalModel::class,
            $this->shiftUtil->get(12, 1)->getWorkingAloneIntervals());

        $this->assertCount(1, $this->shiftUtil->get(12, 1)->getWorkingAloneIntervals());

        $this->assertEquals([
            new IntervalModel(
                (new \DateTime())->setTime(11, 0),
                (new \DateTime())->setTime(14, 0))
            ],
            $this->shiftUtil->get(12, 1)->getWorkingAloneIntervals()
        );

        $this->assertEquals([
            new IntervalModel(
                (new \DateTime())->setTime(8, 0),
                (new \DateTime())->setTime(11, 0))
        ],
            $this->shiftUtil->get(12, 1)->getWorkingGroupIntervals()
        );

        $this->assertEquals(
            180,
            $this->shiftUtil->get(12, 1)->getTotalWorkingAloneInMinutes()
        );
    }
}