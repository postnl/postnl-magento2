<?php

namespace TIG\PostNL\Unit\Model;

use TIG\PostNL\Model\ShipmentLabelRepository;
use TIG\PostNL\Test\TestCase;

class ShipmentLabelRepositoryTest extends TestCase
{
    /**
     * @param array $args
     *
     * @return object
     */
    public function getInstance(array $args = [])
    {
        return $this->objectManager->getObject(ShipmentLabelRepository::class, $args);
    }

    public function testSave()
    {
        $shipmentLabelFactoryMock = $this->getFakeMock(\TIG\PostNL\Model\ShipmentLabel::class);
        $shipmentLabelFactoryMock->setMethods(['getIdentities', 'save']);
        $shipmentLabelFactoryMock = $shipmentLabelFactoryMock->getMock();

        $saveExpects = $shipmentLabelFactoryMock->expects($this->once());
        $saveExpects->method('save');

        $instance = $this->getInstance();
        $result = $instance->save($shipmentLabelFactoryMock);

        $this->assertEquals($shipmentLabelFactoryMock, $result);
    }

    public function testgetById()
    {
        $id = rand(1000, 2000);

        $shipmentLabelFactoryMock = $this->getFakeMock('\TIG\PostNL\Model\ShipmentLabelFactory');
        $shipmentLabelFactoryMock->setMethods(['create', 'load', 'getId']);
        $shipmentLabelFactoryMock = $shipmentLabelFactoryMock->getMock();

        $createExpects = $shipmentLabelFactoryMock->expects($this->once());
        $createExpects->method('create');
        $createExpects->willReturnSelf();

        $loadExpects = $shipmentLabelFactoryMock->expects($this->once());
        $loadExpects->method('load');
        $loadExpects->willReturnSelf();

        $getIdExpects = $shipmentLabelFactoryMock->expects($this->once());
        $getIdExpects->method('getId');
        $getIdExpects->willReturn($id);

        $instance = $this->getInstance(['objectFactory' => $shipmentLabelFactoryMock]);
        $result = $instance->getById($id);

        $this->assertInstanceOf('\TIG\PostNL\Model\ShipmentLabelFactory', $result);
        $this->assertEquals($shipmentLabelFactoryMock, $result);
    }

    public function testDelete()
    {
        $shipmentLabelFactoryMock = $this->getFakeMock(\TIG\PostNL\Model\ShipmentLabel::class);
        $shipmentLabelFactoryMock->setMethods(['getIdentities', 'delete']);
        $shipmentLabelFactoryMock = $shipmentLabelFactoryMock->getMock();

        $saveExpects = $shipmentLabelFactoryMock->expects($this->once());
        $saveExpects->method('delete');

        $instance = $this->getInstance();
        $result = $instance->delete($shipmentLabelFactoryMock);

        $this->assertTrue($result);
    }
}
