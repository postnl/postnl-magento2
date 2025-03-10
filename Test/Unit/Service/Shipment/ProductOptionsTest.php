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
            ['pge', false, false, ['Characteristic' => '118', 'Option' => '002']],
            ['evening', false, false, ['Characteristic' => '118', 'Option' => '006']],
            ['pge', true, false, ['Characteristic' => '118', 'Option' => '002']],
            ['evening', true, false, ['Characteristic' => '118', 'Option' => '006']],
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
    public function testReturnsTheCorrectData($type, $flat, $isIdCheck, $expected)
    {
        /** @var ShipmentInterface|\PHPUnit\Framework\MockObject\MockObject$shipmentMock */
        $shipmentMock = $this->getFakeMock(ShipmentInterface::class)->getMock();
        $shipmentMock->method('getShipmentType')->willReturn($type);
        $shipmentMock->method('isIDCheck')->willReturn($isIdCheck);

        /** @var ProductOptions $instance */
        $instance = $this->getInstance();

        $result = $instance->get($shipmentMock, $flat);
        if (!$flat) {
            $result = $result['ProductOption'];
        }

        foreach ($expected as $type => $value) {
            $this->assertEquals($result[$type], $value);
            unset($result[$type]);
        }

        if (count($result)) {
            $this->fail('$result contains values but should be empty');
        }
    }
}
