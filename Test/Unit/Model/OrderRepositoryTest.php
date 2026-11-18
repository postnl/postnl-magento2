<?php

namespace TIG\PostNL\Unit\Model;

use TIG\PostNL\Service\Filter\SearchResults;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Model\Order;
use TIG\PostNL\Model\ResourceModel\Order\CollectionFactory;
use TIG\PostNL\Model\OrderFactory;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class OrderRepositoryTest extends TestCase
{
    protected $instanceClass = OrderRepository::class;

    public function testSave()
    {
        $model = $this->getMock(\TIG\PostNL\Model\Order::class);
        $resource = $this->getMock(\TIG\PostNL\Model\ResourceModel\Order::class);
        $resource->expects($this->once())->method('save')->with($model);

        $instance = $this->getInstance(['entityResource' => $resource]);
        $result = $instance->save($model);

        $this->assertSame($model, $result);
    }

    public function testCreate()
    {
        $orderFactoryMock = $this->getOrderFactoryMock();
        $data             = [
            'order_id' => 10,
            'quote_id' => 12
        ];

        $createExpects = $orderFactoryMock->expects($this->once());
        $createExpects->method('create');
        $createExpects->with($data);
        $createExpects->willReturnSelf();

        $instance = $this->getInstance(['orderFactory' => $orderFactoryMock]);
        $result   = $instance->create($data);

        $this->assertEquals($orderFactoryMock, $result);
    }

    public function testGetById()
    {
        $id = 42;
        $model = $this->getMock(\TIG\PostNL\Model\Order::class);
        $model->method('getId')->willReturn($id);
        $factory = $this->getMock(\TIG\PostNL\Model\OrderFactory::class);
        $factory->expects($this->once())->method('create')->willReturn($model);
        $resource = $this->getMock(\TIG\PostNL\Model\ResourceModel\Order::class);
        $resource->expects($this->once())->method('load')->with($model, $id);

        $instance = $this->getInstance(['orderFactory' => $factory, 'entityResource' => $resource]);

        $this->assertSame($model, $instance->getById($id));
    }

    private function getOrderFactoryMock()
    {
        $orderFactoryMock = $this->getFakeMock(OrderFactory::class);
        $orderFactoryMock->onlyMethods(['create']);
        $orderFactoryMock = $orderFactoryMock->getMock();

        return $orderFactoryMock;
    }
}
