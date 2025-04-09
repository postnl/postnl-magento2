<?php

namespace TIG\PostNL\Test\Unit\Service\Shipment;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Service\Shipment\ProductOptions;
use TIG\PostNL\Test\TestCase;

class ProductOptionsTest extends TestCase
{
    public $instanceClass = ProductOptions::class;

    public function returnsTheCorrectDataProvider()
    {
        return [
            ['pge', false, [0 => ['Characteristic' => '118', 'Option' => '002']]],
            ['evening', false, [0 => ['Characteristic' => '118', 'Option' => '006']]],
        ];
    }

    /**
     * @dataProvider returnsTheCorrectDataProvider
     *
     * @param $type
     * @param $flat
     * @param $isIdCheck
     * @param $expected
     *
     * @throws \Exception
     */
    public function testReturnsTheCorrectData($type, $isIdCheck, $expected)
    {
        /** @var ShipmentInterface|\PHPUnit\Framework\MockObject\MockObject$shipmentMock */
        $shipmentMock = $this->getFakeMock(ShipmentInterface::class)->getMock();
        $shipmentMock->method('getShipmentType')->willReturn($type);
        $shipmentMock->method('isIDCheck')->willReturn($isIdCheck);
        $shipmentMock->method('getProductCode')->willReturn('3085');

        /** @var ProductOptions $instance */
        $instance = $this->getInstance();

        $result = $instance->get($shipmentMock);
        $this->assertEquals($expected, $result);
    }
}
