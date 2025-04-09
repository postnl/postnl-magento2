<?php

namespace TIG\PostNL\Test\Unit\Service\Shipment\Label;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Service\Shipment\Label\Prepare;
use TIG\PostNL\Service\Shipment\Label\Type\TypeInterface;
use TIG\PostNL\Service\Shipment\Label\Type\TypeInterfaceFactory;
use TIG\PostNL\Service\Shipment\Type;
use TIG\PostNL\Test\TestCase;

class PrepareTest extends TestCase
{
    public $instanceClass = Prepare::class;

    public function testRequiresTheRightInterface()
    {
        $this->expectException(\TIG\PostNL\Exception::class);
        $this->expectExceptionMessage("test is not an instance of TIG\PostNL\Service\Shipment\Label\Type\TypeInterface");

        $typeConverter = $this->getObject(Type::class);

        $validType = $this->getMock(TypeInterface::class);
        $validFactory = $this->getFakeMock(TypeInterfaceFactory::class)->setMethods(['create'])->getMock();
        $validFactory->expects($this->atLeastOnce())->method('create')->willReturn($validType);

        $invalidFactory = $this->getFakeMock(TypeInterfaceFactory::class)->setMethods(['create'])->getMock();
        $invalidFactory->expects($this->atLeastOnce())->method('create')->willReturn('randomstring');

        $instance = $this->getInstance([
            'typeConverter' => $typeConverter,
            'types' => [
                'domestic' => $validFactory,
                'test' => $invalidFactory,
            ]
        ]);

        $instance->label($this->getMock(\TIG\PostNL\Api\Data\ShipmentLabelInterface::class));
    }

    /**
     * @param $type
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getLabelMock($type)
    {
        $shipmentMock    = $this->getMock(ShipmentInterface::class);
        $getShipmentType = $shipmentMock->method('getShipmentType');
        $getShipmentType->willReturn($type);

        $labelMock   = $this->getMock(ShipmentLabelInterface::class);
        $labelMock->method('getLabelFileFormat')->willReturn('PDF');
        $getShipment = $labelMock->method('getShipment');
        $getShipment->willReturn($shipmentMock);

        return $labelMock;
    }
}
