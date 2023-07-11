<?php

namespace TIG\PostNL\Unit\Model;

use TIG\PostNL\Model\ShipmentBarcodeRepository;
use TIG\PostNL\Test\TestCase;

class ShipmentBarcodeRepositoryTest extends TestCase
{
    /**
     * @param array $args
     *
     * @return object
     */
    public function getInstance(array $args = [])
    {
        return $this->objectManager->getObject(ShipmentBarcodeRepository::class, $args);
    }

    public function testSave()
    {
        $shipmentBarcodeFactoryMock = $this->getFakeMock(\TIG\PostNL\Model\ShipmentBarcode::class);
        $shipmentBarcodeFactoryMock->setMethods(['getIdentities', 'save']);
        $shipmentBarcodeFactoryMock = $shipmentBarcodeFactoryMock->getMock();

        $saveExpects = $shipmentBarcodeFactoryMock->expects($this->once());
        $saveExpects->method('save');

        $instance = $this->getInstance();
        $result = $instance->save($shipmentBarcodeFactoryMock);

        $this->assertEquals($shipmentBarcodeFactoryMock, $result);
    }

    public function testgetById()
    {
        $id = rand(1000, 2000);

        $shipmentBarcodeFactoryMock = $this->getFakeMock('\TIG\PostNL\Model\ShipmentBarcodeFactory');
        $shipmentBarcodeFactoryMock->setMethods(['create', 'load', 'getId']);
        $shipmentBarcodeFactoryMock = $shipmentBarcodeFactoryMock->getMock();

        $createExpects = $shipmentBarcodeFactoryMock->expects($this->once());
        $createExpects->method('create');
        $createExpects->willReturnSelf();

        $loadExpects = $shipmentBarcodeFactoryMock->expects($this->once());
        $loadExpects->method('load');
        $loadExpects->willReturnSelf();

        $getIdExpects = $shipmentBarcodeFactoryMock->expects($this->once());
        $getIdExpects->method('getId');
        $getIdExpects->willReturn($id);

        $instance = $this->getInstance(['objectFactory' => $shipmentBarcodeFactoryMock]);
        $result = $instance->getById($id);

        $this->assertInstanceOf('\TIG\PostNL\Model\ShipmentBarcodeFactory', $result);
        $this->assertEquals($shipmentBarcodeFactoryMock, $result);
    }

    public function testDelete()
    {
        $shipmentBarcodeFactoryMock = $this->getFakeMock(\TIG\PostNL\Model\ShipmentBarcode::class);
        $shipmentBarcodeFactoryMock->setMethods(['getIdentities', 'delete']);
        $shipmentBarcodeFactoryMock = $shipmentBarcodeFactoryMock->getMock();

        $saveExpects = $shipmentBarcodeFactoryMock->expects($this->once());
        $saveExpects->method('delete');

        $instance = $this->getInstance();
        $result = $instance->delete($shipmentBarcodeFactoryMock);

        $this->assertTrue($result);
    }
}
