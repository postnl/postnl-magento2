<?php

namespace TIG\PostNL\Test\Unit\Model;

use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\Context;
use TIG\PostNL\Config\Source\Options\ProductOptions;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Test\TestCase;

class ShipmentTest extends TestCase
{
    public $instanceClass = Shipment::class;

    public function isExtraCoverProvider()
    {
        return [
            [3085, false],
            [3544, true],
        ];
    }

    /**
     * @param $productCode
     * @param $expected
     *
     * @dataProvider isExtraCoverProvider
     */
    public function testIsExtraCover($productCode, $expected)
    {
        $functionResponse = [
            'isExtraCover' => $expected,
        ];

        $productCodeMock = $this->getFakeMock(ProductOptions::class, true);
        $this->mockFunction($productCodeMock, 'getOptionsByCode', $functionResponse, [$productCode]);

        /** @var Shipment $instance */
        $instance = $this->getInstance(['productOptions' => $productCodeMock]);
        $instance->setProductCode($productCode);

        $result = $instance->isExtraCover();

        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function setConfirmedAtProvider()
    {
        return [
            'null value' => [
                null,
                0
            ],
            'string date value' => [
                '01-01-1970',
                1
            ]
        ];
    }

    /**
     * @param $value
     * @param $dispatchCalls
     *
     * @dataProvider setConfirmedAtProvider
     */
    public function testSetConfirmedAt($value, $dispatchCalls)
    {
        $eventManagerMock = $this->getFakeMock(ManagerInterface::class)->setMethods(['dispatch'])->getMock();

        $contextMock = $this->getFakeMock(Context::class)->setMethods(['getEventDispatcher'])->getMock();
        $contextMock->expects($this->once())->method('getEventDispatcher')->willReturn($eventManagerMock);

        $instance = $this->getInstance(['context' => $contextMock]);

        $eventManagerMock->expects($this->exactly($dispatchCalls))
            ->method('dispatch')
            ->with('tig_postnl_set_confirmed_at_before', ['shipment' => $instance]);

        $result = $instance->setConfirmedAt($value);
        $this->assertInstanceOf(Shipment::class, $result);
    }

    public function canChangeParcelCountProvider()
    {
        return [
            'not confirmed, not domestic' => [false, false, true],
            'confirmed, not domestic' => [true, false, false],
            'not confirmed, domestic' => [false, true, true],
            'confirmed, domestic' => [true, true, false],
        ];
    }

    /**
     * @dataProvider canChangeParcelCountProvider
     */
    public function testCanChangeParcelCount($isConfirmed, $isDomesticShipment, $expected)
    {
        $address = $this->getObject(\Magento\Sales\Model\Order\Address::class);
        $address->setCountryId($isDomesticShipment ? 'NL' : 'US');

        /** @var Shipment $shipment */
        $shipment = $this->getInstance();
        $shipment->setConfirmedAt($isConfirmed ? '2016-11-19 21:13:13' : null);

        $this->setProperty('shippingAddress', $address, $shipment);

        $this->assertSame($expected, $shipment->canChangeParcelCount());
    }
}
