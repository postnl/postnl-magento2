<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Unit\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Phrase;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Observer\SalesOrderShipmentSaveAfterEvent;
use TIG\PostNL\Model\ShipmentBarcode;
use TIG\PostNL\Test\TestCase;

class SalesOrderShipmentSaveAfterEventTest extends TestCase
{
    protected $instanceClass = SalesOrderShipmentSaveAfterEvent::class;

    public function testExecute()
    {
        $id = rand(1000, 2000);
        $barcode = '3STOTA123457890';

        $shipmentFactoryMock = $this->getFakeMock('\TIG\PostNL\Model\ShipmentFactory');
        $shipmentFactoryMock->setMethods(['create', 'setData', 'save']);
        $shipmentFactoryMock = $shipmentFactoryMock->getMock();

        $createExpects = $shipmentFactoryMock->expects($this->once());
        $createExpects->method('create');
        $createExpects->willReturnSelf();

        $setDataExpects = $shipmentFactoryMock->expects($this->once());
        $setDataExpects->method('setData');
        $setDataExpects->with('shipment_id', $id);

        $saveExpects = $shipmentFactoryMock->expects($this->once());
        $saveExpects->method('save');

        $shipmentMockBuilder = $this->getMockBuilder(Shipment::class, ['getId']);
        $shipmentMockBuilder->disableOriginalConstructor();
        $shipmentMock = $shipmentMockBuilder->getMock();

        $getIdMock = $shipmentMock->expects($this->once());
        $getIdMock->method('getId');
        $getIdMock->willReturn($id);

        $shipmentBarcodeFactoryMock = $this->getShipmentBarcodeFactoryMock($id, $barcode);

        $barcodeMock = $this->getFakeMock('TIG\PostNL\Webservices\Endpoints\Barcode');
        $barcodeMock->setMethods(['call']);
        $barcodeMock = $barcodeMock->getMock();

        $callExpects = $barcodeMock->expects($this->once());
        $callExpects->method('call');
        $callExpects->willReturn((Object)['Barcode' => $barcode]);

        /** @var Observer $observer */
        $observer = $this->objectManager->getObject(Observer::class);
        $observer->setData('data_object', $shipmentMock);

        $instance = $this->getInstance([
            'shipmentFactory' => $shipmentFactoryMock,
            'shipmentBarcodeFactory' => $shipmentBarcodeFactoryMock,
            'barcode' => $barcodeMock
        ]);
        $instance->execute($observer);
    }

    /**
     * @param $id
     * @param $barcode
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getShipmentBarcodeFactoryMock($id, $barcode)
    {
        $shipmentBarcodeFactoryMock = $this->getMockBuilder('\TIG\PostNL\Model\ShipmentBarcodeFactory');
        $shipmentBarcodeFactoryMock->setMethods(
            ['create', 'setShipmentId', 'setType', 'setNumber', 'setValue', 'save']
        );
        $shipmentBarcodeFactoryMock = $shipmentBarcodeFactoryMock->getMock();

        $createExpects = $shipmentBarcodeFactoryMock->expects($this->once());
        $createExpects->method('create');
        $createExpects->willReturnSelf();

        $setShipmentIdExpects = $shipmentBarcodeFactoryMock->expects($this->once());
        $setShipmentIdExpects->method('setShipmentId');
        $setShipmentIdExpects->with($id);

        $setShipmentIdExpects = $shipmentBarcodeFactoryMock->expects($this->once());
        $setShipmentIdExpects->method('setType');
        $setShipmentIdExpects->with(ShipmentBarcode::BARCODE_TYPE_SHIPMENT);

        $setShipmentIdExpects = $shipmentBarcodeFactoryMock->expects($this->once());
        $setShipmentIdExpects->method('setNumber');
        $setShipmentIdExpects->with(0);

        $setShipmentIdExpects = $shipmentBarcodeFactoryMock->expects($this->once());
        $setShipmentIdExpects->method('setValue');
        $setShipmentIdExpects->with($barcode);

        $saveExpects = $shipmentBarcodeFactoryMock->expects($this->once());
        $saveExpects->method('save');

        return $shipmentBarcodeFactoryMock;
    }
}
