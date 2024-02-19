<?php

namespace TIG\PostNL\Test\Unit\Observer;

use Magento\Framework\Event\Observer;
use TIG\PostNL\Api\OrderRepositoryInterface;
use TIG\PostNL\Observer\TIGPostNLShipmentSaveAfter\UpdateOrderGrid;
use TIG\PostNL\Test\TestCase;

class UpdateOrderGridTest extends TestCase
{
    protected $orderId;

    protected $instanceClass = UpdateOrderGrid::class;

    public function testExecute()
    {
        $this->orderId = rand(1000, 9999);

        $gridMock = $this->getGridInterface();
        $orderRepository = $this->getOrderRepository();
        $shipment = $this->getShipment();

        /** @var UpdateOrderGrid $instance */
        $instance = $this->getInstance([
            'orderGrid' => $gridMock,
            'orderRepositoryInterface' => $orderRepository,
        ]);


        /** @var Observer $observer */
        $observer = $this->getObject(Observer::class);
        $observer->setData('data_object', $shipment);

        $instance->execute($observer);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getOrderRepository()
    {
        $orderRepository = $this->getMock(OrderRepositoryInterface::class);
        $order = $this->getOrder();

        $getByFieldWithValueExpects = $orderRepository->expects($this->once());
        $getByFieldWithValueExpects->method('getByFieldWithValue');
        $getByFieldWithValueExpects->with('order_id', $this->orderId);
        $getByFieldWithValueExpects->willReturn($order);

        $saveExpects = $orderRepository->expects($this->once());
        $saveExpects->method('save');
        $saveExpects->with($order);

        return $orderRepository;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getGridInterface()
    {
        $gridMock = $this->getMock(\Magento\Sales\Model\ResourceModel\GridInterface::class);

        $refreshExpects = $gridMock->expects($this->once());
        $refreshExpects->method('refresh');
        $refreshExpects->with($this->orderId);

        return $gridMock;
    }

    private function getOrder()
    {
        $orderMock = $this->getFakeMock(\TIG\PostNL\Model\Order::class)->getMock();

        $getDataExpects = $orderMock->expects($this->once());
        $getDataExpects->method('getData');

        $setDataExpects = $orderMock->expects($this->once());
        $setDataExpects->method('setData');

        $data = [
            'ship_at' => '2016-11-19',
            'product_code' => null,
            'confirmed' => 0,
        ];

        $setDataExpects->with($data);

        return $orderMock;
    }

    /**
     * @return object
     */
    private function getShipment()
    {
        $shipment = $this->getObject(\TIG\PostNL\Model\Shipment::class);
        $shipment->setData('order_id', $this->orderId);
        $shipment->setData('ship_at', '2016-11-19');

        return $shipment;
    }
}
