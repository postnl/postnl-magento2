<?php

namespace TIG\PostNL\Test\Unit\Service\Shipment;

use TIG\PostNL\Service\Shipment\Type;
use TIG\PostNL\Test\TestCase;

class TypeTest extends TestCase
{
    public $instanceClass = Type::class;

    public function returnsTheRightTypeProvider()
    {
        return [
            ['PG', 'NL', 'PG'],
            ['PGE', 'NL', 'PGE'],
            ['Evening', 'NL', 'Evening'],
            ['Daytime', 'NL', 'Daytime'],
            [null, 'NL', 'Daytime'],
            [null, 'DE', 'EPS'],
            [null, 'BE', 'EPS'],
            [null, 'US', 'GLOBALPACK'],
        ];
    }

    /**
     * @dataProvider returnsTheRightTypeProvider
     *
     * @param $type
     * @param $countryId
     * @param $expected
     *
     * @throws \Exception
     */
    public function testReturnsTheRightType($type, $countryId, $expected)
    {
        $shipmentMock = $this->getFakeMock(\Magento\Sales\Model\Order\Shipment::class, true);
        /** @var \Magento\Sales\Model\Order\Address $address */
        $address = $this->getObject(\Magento\Sales\Model\Order\Address::class);
        $address->setCountryId($countryId);

        $shippingAdddressExpects = $shipmentMock->expects($this->any());
        $shippingAdddressExpects->method('getShippingAddress');
        $shippingAdddressExpects->willReturn($address);

        $shipmentInterfaceMock = $this->getMock(\TIG\PostNL\Api\Data\ShipmentInterface::class);

        $getTypeExpects = $shipmentInterfaceMock->expects($this->once());
        $getTypeExpects->method('getShipmentType');
        $getTypeExpects->willReturn($type);

        $getShipmentMethod = $shipmentInterfaceMock->method('getShipment');
        $getShipmentMethod->willReturn($shipmentMock);

        /** @var Type $instance */
        $instance = $this->getInstance();

        $this->assertEquals($expected, $instance->get($shipmentInterfaceMock));
    }
}
