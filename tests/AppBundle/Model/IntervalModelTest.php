<?php

namespace Tests\AppBundle\Model;

use AppBundle\Model\IntervalModel;
use PHPUnit\Framework\TestCase;

class IntervalModelTest extends TestCase
{
    public function testIsTouching()
    {
        $interval = new IntervalModel(
            (new \DateTime())->setTime(9, 0),
            (new \DateTime())->setTime(11, 0)
        );

        $touchingInterval = new IntervalModel(
            (new \DateTime())->setTime(10, 0),
            (new \DateTime())->setTime(12, 0)
        );

        $this->assertTrue($interval->isTouching($touchingInterval));

        $touchingInterval = new IntervalModel(
            (new \DateTime())->setTime(8, 0),
            (new \DateTime())->setTime(10, 0)
        );

        $this->assertTrue($interval->isTouching($touchingInterval));

        $noneTouchingInterval = new IntervalModel(
            (new \DateTime())->setTime(11, 05),
            (new \DateTime())->setTime(12, 0)
        );

        $this->assertFalse($interval->isTouching($noneTouchingInterval));

        $noneTouchingInterval = new IntervalModel(
            (new \DateTime())->setTime(7, 00),
            (new \DateTime())->setTime(8, 0)
        );

        $this->assertFalse($interval->isTouching($noneTouchingInterval));
    }

    public function testIsOverLap()
    {
        $interval = new IntervalModel(
            (new \DateTime())->setTime(9, 0),
            (new \DateTime())->setTime(11, 0)
        );

        $touchingInterval = new IntervalModel(
            (new \DateTime())->setTime(10, 0),
            (new \DateTime())->setTime(12, 0)
        );

        $this->assertTrue($interval->IsOverlap($touchingInterval));

        $touchingInterval = new IntervalModel(
            (new \DateTime())->setTime(12, 0),
            (new \DateTime())->setTime(15, 0)
        );

        $this->assertFalse($interval->IsOverlap($touchingInterval));
    }

    public function testConcatenate()
    {
        $interval = new IntervalModel(
            (new \DateTime())->setTime(9, 0),
            (new \DateTime())->setTime(11, 0)
        );

        $touchingInterval = new IntervalModel(
            (new \DateTime())->setTime(10, 0),
            (new \DateTime())->setTime(12, 0)
        );

        $interval->concatenate($touchingInterval);

        $this->assertEquals($interval->getStartTime(), (new \DateTime())->setTime(9, 0));
        $this->assertEquals($interval->getEndTime(), (new \DateTime())->setTime(12, 0));

        $interval = new IntervalModel(
            (new \DateTime())->setTime(9, 0),
            (new \DateTime())->setTime(11, 0)
        );

        $touchingInterval = new IntervalModel(
            (new \DateTime())->setTime(8, 0),
            (new \DateTime())->setTime(9, 30)
        );

        $interval->concatenate($touchingInterval);

        $this->assertEquals($interval->getStartTime(), (new \DateTime())->setTime(8, 0));
        $this->assertEquals($interval->getEndTime(), (new \DateTime())->setTime(11, 0));

        $noneTouchingInterval = new IntervalModel(
            (new \DateTime())->setTime(11, 05),
            (new \DateTime())->setTime(12, 0)
        );

        $interval = new IntervalModel(
            (new \DateTime())->setTime(9, 0),
            (new \DateTime())->setTime(11, 0)
        );

        $interval->concatenate($noneTouchingInterval);

        $this->assertEquals($interval->getStartTime(), (new \DateTime())->setTime(9, 0));
        $this->assertEquals($interval->getEndTime(), (new \DateTime())->setTime(11, 0));
    }

    public function testGetOverLapInterval()
    {
        $interval = new IntervalModel(
            (new \DateTime())->setTime(9, 0),
            (new \DateTime())->setTime(11, 0)
        );

        $overlappedInterval = new IntervalModel(
            (new \DateTime())->setTime(10, 0),
            (new \DateTime())->setTime(12, 0)
        );

        $this->assertEquals($interval->getOverLapInterval($overlappedInterval), new IntervalModel(
            (new \DateTime())->setTime(10, 0),
            (new \DateTime())->setTime(11, 0)
        ));

        $noneOverLappedInterval = new IntervalModel(
            (new \DateTime())->setTime(11, 05),
            (new \DateTime())->setTime(12, 0)
        );

        $this->assertNull($interval->getOverLapInterval($noneOverLappedInterval));

        $noneOverLappedInterval = new IntervalModel(
            (new \DateTime())->setTime(11, 00),
            (new \DateTime())->setTime(12, 0)
        );

        $this->assertNull($interval->getOverLapInterval($noneOverLappedInterval));
    }

    public function testNoneOverlapIntervals()
    {
        $interval = new IntervalModel(
            (new \DateTime())->setTime(9, 0),
            (new \DateTime())->setTime(14, 0)
        );

        $overlappedInterval = new IntervalModel(
            (new \DateTime())->setTime(10, 0),
            (new \DateTime())->setTime(12, 0)
        );

        $this->assertCount(2, $interval->getNoneOverlapIntervals($overlappedInterval));
        $this->assertContainsOnlyInstancesOf(IntervalModel::class, $interval->getNoneOverlapIntervals($overlappedInterval));

        $overlappedInterval = new IntervalModel(
            (new \DateTime())->setTime(14, 0),
            (new \DateTime())->setTime(15, 0)
        );

        $this->assertCount(0, $interval->getNoneOverlapIntervals($overlappedInterval));
    }

    public function testGetDiffBetweenStartAndEndTime()
    {
        $interval = new IntervalModel(
            (new \DateTime())->setTime(9, 0),
            (new \DateTime())->setTime(14, 0)
        );

        $this->assertInstanceOf(\DateInterval::class, $interval->getDiffBetweenStartAndEndTime());
    }
}