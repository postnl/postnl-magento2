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
        $model = $this->getMock(\TIG\PostNL\Model\ShipmentBarcode::class);
        $resource = $this->getMock(\TIG\PostNL\Model\ResourceModel\ShipmentBarcode::class);
        $resource->expects($this->once())->method('save')->with($model);

        $instance = $this->getInstance(['entityResource' => $resource]);
        $result = $instance->save($model);

        $this->assertSame($model, $result);
    }

    public function testGetById()
    {
        $id = 42;
        $model = $this->getMock(\TIG\PostNL\Model\ShipmentBarcode::class);
        $model->method('getId')->willReturn($id);
        $factory = $this->getMock(\TIG\PostNL\Model\ShipmentBarcodeFactory::class);
        $factory->expects($this->once())->method('create')->willReturn($model);
        $resource = $this->getMock(\TIG\PostNL\Model\ResourceModel\ShipmentBarcode::class);
        $resource->expects($this->once())->method('load')->with($model, $id);

        $instance = $this->getInstance(['objectFactory' => $factory, 'entityResource' => $resource]);

        $this->assertSame($model, $instance->getById($id));
    }

    public function testDelete()
    {
        $model = $this->getMock(\TIG\PostNL\Model\ShipmentBarcode::class);
        $resource = $this->getMock(\TIG\PostNL\Model\ResourceModel\ShipmentBarcode::class);
        $resource->expects($this->once())->method('delete')->with($model);

        $instance = $this->getInstance(['entityResource' => $resource]);
        $result = $instance->delete($model);

        $this->assertTrue($result);
    }
}
