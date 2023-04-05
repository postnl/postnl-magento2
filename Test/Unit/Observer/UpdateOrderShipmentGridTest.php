<?php

namespace TIG\PostNL\Test\Unit\Observer;

use Magento\Framework\Event\Observer;
use TIG\PostNL\Observer\TIGPostNLShipmentSaveAfter\UpdateOrderShipmentGrid;
use TIG\PostNL\Test\TestCase;

class UpdateOrderShipmentGridTest extends TestCase
{
    protected $instanceClass = UpdateOrderShipmentGrid::class;

    public function testExecute()
    {
        $shipment_id = rand(1000, 9999);

        $gridMock = $this->getMock(\Magento\Sales\Model\ResourceModel\GridInterface::class);

        $refreshExpects = $gridMock->expects($this->once());
        $refreshExpects->method('refresh');
        $refreshExpects->with($shipment_id);

        /** @var UpdateOrderShipmentGrid $instance */
        $instance = $this->getInstance([
            'shipmentGrid' => $gridMock,
        ]);

        $shipment = $this->getObject(\TIG\PostNL\Model\Shipment::class);
        $shipment->setData('shipment_id', $shipment_id);

        /** @var Observer $observer */
        $observer = $this->getObject(Observer::class);
        $observer->setData('data_object', $shipment);

        $instance->execute($observer);
    }
}
