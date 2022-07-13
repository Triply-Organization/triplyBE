<?php

namespace App\Tests\Unit\Transformer;

use App\Entity\PriceList;
use App\Entity\Schedule;
use App\Entity\TicketType;
use App\Entity\Tour;
use App\Service\PriceListService;
use App\Transformer\ScheduleTransformer;
use PHPUnit\Framework\TestCase;

class ScheduleTransformerTest extends TestCase
{

    public function testToArrayScheduleOfCustommer()
    {
        $tourMock = $this->getMockBuilder(Tour::class)->onlyMethods(['getId'])->getMock();
        $tourMock->method('getId')->willReturn(1);
        $scheduleMock = $this->getMockBuilder(Schedule::class)->onlyMethods(['getId'])->getMock();
        $scheduleMock->method('getId')->willReturn(1);
        $scheduleMock->setStartDate(new \DateTimeImmutable());
        $scheduleMock->setTicketRemain(5);
        $scheduleTransformerMock = $this->getMockBuilder(ScheduleTransformer::class)
            ->onlyMethods(['getPriceList'])->disableOriginalConstructor()->getMock();
        $scheduleTransformerMock->expects($this->once())->method('getPriceList')->willReturn(array());
        $result = $scheduleTransformerMock->toArrayScheduleOfCustomer([$scheduleMock], $tourMock);
        $this->assertIsArray($result);
    }

    public function testGetPriceList()
    {
        $type = new TicketType();
        $type->setName('STRING');
        $priceList = new PriceList();
        $priceList->setPrice(4)->setType($type);
        $schedule = new Schedule();
        $schedule->addPriceList($priceList);
        $priceListServiceMock = $this->getMockBuilder(PriceListService::class)->disableOriginalConstructor()->getMock();
        $scheduleTransformer = new ScheduleTransformer($priceListServiceMock);
        $result = $scheduleTransformer->getPriceList($schedule);
        $this->assertIsArray($result);
    }
}