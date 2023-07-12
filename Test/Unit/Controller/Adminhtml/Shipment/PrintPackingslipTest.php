<?php

namespace TIG\PostNL\Unit\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\ShipmentRepository;
use TIG\PostNL\Controller\Adminhtml\Shipment\PrintPackingslip;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Test\TestCase;

class PrintPackingslipTest extends TestCase
{
    protected $instanceClass = PrintPackingslip::class;

    public function testGetPackingslip()
    {
        $addressMock = $this->getFakeMock(Address::class)->setMethods(['getCountryId'])->getMock();
        $addressMock->expects($this->once())->method('getCountryId')->willReturn('NL');

        $shipmentMock = $this->getFakeMock(Shipment::class)->setMethods(['getId', 'getShippingAddress', 'getTracks'])->getMock();
        $shipmentMock->expects($this->exactly(2))->method('getId')->willReturn(1);
        $shipmentMock->expects($this->once())->method('getShippingAddress')->willReturn($addressMock);
        $shipmentMock->expects($this->once())->method('getTracks')->willReturn(['some track code']);

        $shipmentRepoMock = $this->getFakeMock(ShipmentRepository::class)->setMethods(['get'])->getMock();
        $shipmentRepoMock->expects($this->once())->method('get')->with(1)->willReturn($shipmentMock);

        $requestMock = $this->getFakeMock(RequestInterface::class)->getMockForAbstractClass();
        $requestMock->expects($this->once())->method('getParam')->with('shipment_id')->willReturn(1);

        $barcodeHandlerMock = $this->getFakeMock(BarcodeHandler::class)->setMethods(['prepareShipment'])->getMock();
        $barcodeHandlerMock->expects($this->once())->method('prepareShipment')->with(1, 'NL');

        $contextMock = $this->getFakeMock(Context::class)->setMethods(['getRequest'])->getMock();
        $contextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($requestMock);

        $instance = $this->getInstance([
            'context' => $contextMock,
            'shipmentRepository' => $shipmentRepoMock,
            'barcodeHandler' => $barcodeHandlerMock
        ]);
        $this->invoke('getPackingslip', $instance);
    }

    public function testGetShipment()
    {
        $requestMock = $this->getFakeMock(RequestInterface::class)->getMockForAbstractClass();
        $requestMock->expects($this->once())->method('getParam')->with('shipment_id')->willReturn(1);

        $contextMock = $this->getFakeMock(Context::class)->setMethods(['getRequest'])->getMock();
        $contextMock->expects($this->atLeastOnce())->method('getRequest')->willReturn($requestMock);

        $shipmentMock = $this->getFakeMock(Shipment::class, true);

        $shipmentRepoMock = $this->getFakeMock(ShipmentRepository::class)->setMethods(['get'])->getMock();
        $shipmentRepoMock->expects($this->once())->method('get')->with(1)->willReturn($shipmentMock);

        $instance = $this->getInstance(['context' => $contextMock, 'shipmentRepository' => $shipmentRepoMock]);
        $result = $this->invoke('getShipment', $instance);

        $this->assertInstanceOf(Shipment::class, $result);
        $this->assertEquals($shipmentMock, $result);
    }

    /**
     * @return array
     */
    public function setTracksProvider()
    {
        return [
            'no tracking' => [
                [],
                1
            ],
            'single tracking' => [
                ['3S123456'],
                0
            ],
            'multiple tracking' => [
                ['3S123456', '3S789123', '3S456789'],
                0
            ],
        ];
    }

    /**
     * @param $trackCodes
     * @param $tracksetCalled
     *
     * @dataProvider setTracksProvider
     */
    public function testSetTracks($trackCodes, $tracksetCalled)
    {
        $shipmentMock = $this->getFakeMock(Shipment::class)->setMethods(['getTracks'])->getMock();
        $shipmentMock->expects($this->once())->method('getTracks')->willReturn($trackCodes);

        $trackMock = $this->getFakeMock(Track::class)->setMethods(['set'])->getMock();
        $trackMock->expects($this->exactly($tracksetCalled))->method('set')->with($shipmentMock);

        $instance = $this->getInstance(['track' => $trackMock]);
        $this->invokeArgs('setTracks', [$shipmentMock], $instance);
    }
}
