<?php

namespace TIG\PostNL\Test\Unit\Observer\TIGPostNLSetConfirmedAtBefore;

use Magento\Framework\Event\Observer;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Model\Shipment as PostNLShipment;
use TIG\PostNL\Observer\TIGPostNLSetConfirmedAtBefore\SendTrackAndTraceEmail;
use TIG\PostNL\Test\TestCase;

class SendTrackAndTraceEmailTest extends TestCase
{
    protected $instanceClass = SendTrackAndTraceEmail::class;

    public function testExecute()
    {
        $shipmentMock = $this->getFakeMock(Shipment::class, true);

        $postnlShipmentMock = $this->getFakeMock(PostNLShipment::class)->setMethods(['getShipment'])->getMock();
        $postnlShipmentMock->expects($this->once())->method('getShipment')->willReturn($shipmentMock);

        $trackMock = $this->getFakeMock(Track::class)->setMethods(['send'])->getMock();
        $trackMock->expects($this->once())->method('send')->with($shipmentMock);

        /** @var Observer $observer */
        $observer = $this->getObject(Observer::class);
        $observer->setData('shipment', $postnlShipmentMock);

        $instance = $this->getInstance(['track' => $trackMock]);
        $instance->execute($observer);
    }
}
