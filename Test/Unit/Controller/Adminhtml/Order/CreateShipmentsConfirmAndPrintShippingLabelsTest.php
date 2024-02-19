<?php

namespace TIG\PostNL\Unit\Controller\Adminhtml\Order;

use Magento\Framework\Message\ManagerInterface;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Controller\Adminhtml\Order\CreateShipmentsConfirmAndPrintShippingLabels;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Shipment\CreateShipment;
use TIG\PostNL\Test\TestCase;

class CreateShipmentsConfirmAndPrintShippingLabelsTest extends TestCase
{
    protected $instanceClass = CreateShipmentsConfirmAndPrintShippingLabels::class;

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

    public function handleErrorsProvider()
    {
        return [
            'no errors' => [
                [],
                [],
                0
            ],
            'single instance error' => [
                ['error'],
                [],
                1
            ],
            'single shipment error' => [
                [],
                ['error'],
                1
            ],
            'instance and shipment error' => [
                ['some error'],
                ['another error'],
                2
            ],
            'multiple instance errors' => [
                ['some error', 'another error'],
                [],
                2
            ],
            'multiple shipment errors' => [
                [],
                ['some error', 'another error'],
                2
            ],
            'multiple instance and shipment error' => [
                ['some error', 'another error'],
                ['more errors', 'error', 'failed'],
                5
            ],
        ];
    }

    /**
     * @param $instanceErrors
     * @param $shipmentErrors
     * @param $errorsAdded
     *
     * @dataProvider handleErrorsProvider
     */
    public function testHandleErrors($instanceErrors, $shipmentErrors, $errorsAdded)
    {
        $createShipmentMock = $this->getMockBuilder(CreateShipment::class)
            ->disableOriginalConstructor()
            ->setMethods(['getErrors'])
            ->getMock();
        $createShipmentMock->expects($this->once())->method('getErrors')->willReturn($shipmentErrors);

        $messageManagerMock = $this->getMockBuilder(ManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $messageManagerMock->expects($this->exactly($errorsAdded))->method('addErrorMessage');

        $instance = $this->getInstance(['createShipment' => $createShipmentMock]);
        $this->setProperty('messageManager', $messageManagerMock, $instance);
        $this->setProperty('errors', $instanceErrors, $instance);

        $result = $this->invoke('handleErrors', $instance);
        $this->assertInstanceOf(CreateShipmentsConfirmAndPrintShippingLabels::class, $result);
    }
}
