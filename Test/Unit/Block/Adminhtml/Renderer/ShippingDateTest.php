<?php

namespace TIG\PostNL\Unit\Block\Adminhtml\Renderer;

use TIG\PostNL\Block\Adminhtml\Renderer\ShippingDate;
use TIG\PostNL\Test\TestCase;
use Magento\Framework\Phrase;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class ShippingDateTest extends TestCase
{
    protected $instanceClass = ShippingDate::class;

    public function formatShippingDateProvider()
    {
        return [
            ['19 november 2016', 'Today'],
            ['20 november 2016', 'Tomorrow'],
            ['21 november 2016', 'In 2 days'],
            ['22 november 2016', 'In 3 days'],
            ['29 november 2016', 'In 10 days'],
            ['10 november 2016', '10 Nov. 2016'],
            ['18 november 2016', '18 Nov. 2016'],
        ];
    }
    /**
     * @param $shippingDate
     * @param $expected
     *
     * @dataProvider formatShippingDateProvider
     */
    public function testFormatShippingDate($shippingDate, $expected)
    {
        $todayDate = new \DateTime('19 november 2016');
        $todayDateMock = $this->getMock(TimezoneInterface::class);
        $this->mockFunction($todayDateMock, 'date', $todayDate, ['today', null, false, false]);

        $shipAtDate = new \DateTime($shippingDate);
        $shipAtDateMock = $this->getMock(TimezoneInterface::class);
        $this->mockFunction($shipAtDateMock, 'date', $shipAtDate);

        $instance = $this->getInstance([
            'todayDate' => $todayDateMock,
            'shipAtDate' => $shipAtDateMock,
        ]);

        $result = $this->invokeArgs('formatShippingDate', ['2016-11-19'], $instance);
        if ($result instanceof Phrase) {
            $result = $result->render();
        }
        $this->assertEquals($expected, $result);
    }
    public function getShipAtProvider()
    {
        return [
            [true, '2016-11-19'],
            [false, '2016-11-19'],
            [true, null],
            [false, null],
        ];
    }

    /**
     * @param $useObject
     * @param $shipAt
     *
     * @dataProvider getShipAtProvider
     */
    public function testGetShipAt($useObject, $shipAt)
    {
        $input = $shipAt;
        if ($useObject) {
            $shipment = $this->getFakeMock(\TIG\PostNL\Model\Shipment::class)->setMethods(['getShipAt'])->getMock();

            $shipAtExpects = $shipment->expects($this->once());
            $shipAtExpects->method('getShipAt');
            $shipAtExpects->willReturn($shipAt);

            $input = $shipment;
        }

        $result = $this->invokeArgs('getShipAt', [$input]);
        $this->assertEquals($shipAt, $result);
    }
}
