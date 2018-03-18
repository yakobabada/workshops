<?php

namespace Tests\AppBundle\Model;

use AppBundle\Model\IntervalModel;
use AppBundle\Model\ShiftModel;
use PHPUnit\Framework\TestCase;

class ShiftModelTest extends TestCase
{
    public function testGetTotalWorkingAloneInMinutes()
    {
        $shiftModel = new ShiftModel();
        $shiftModel->setWorkingAloneIntervals([
            $noneTouchingInterval = new IntervalModel(
                (new \DateTime())->setTime(7, 00),
                (new \DateTime())->setTime(8, 0)
            )
        ]);

        $this->assertEquals(60, $shiftModel->getTotalWorkingAloneInMinutes());
    }
}