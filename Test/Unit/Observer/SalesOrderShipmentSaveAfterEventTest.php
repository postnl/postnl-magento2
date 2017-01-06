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

    /**
     * @return array
     */
    public function executeProvider()
    {
        return [
            [rand(1000, 2000), '3STOTA123457890', 1],
            [rand(1000, 2000), 'CD0987654321NL', 2]
        ];
    }

    /**
     * @param $id
     * @param $barcode
     * @param $parcelCount
     *
     * @dataProvider executeProvider
     */
    public function testExecute($id, $barcode, $parcelCount)
    {
        $shipmentFactoryMock = $this->getFakeMock('\TIG\PostNL\Model\ShipmentFactory');
        $shipmentFactoryMock->setMethods(
            ['create', 'setShipmentId', 'setMainBarcode', 'getParcelCount', 'save', 'getEntityId']
        );
        $shipmentFactoryMock = $shipmentFactoryMock->getMock();

        $createExpects = $shipmentFactoryMock->expects($this->once());
        $createExpects->method('create');
        $createExpects->willReturnSelf();

        $setDataExpects = $shipmentFactoryMock->expects($this->once());
        $setDataExpects->method('setShipmentId');

        $setDataExpects = $shipmentFactoryMock->expects($this->once());
        $setDataExpects->method('setMainBarcode');
        $setDataExpects->with($barcode);

        $setDataExpects = $shipmentFactoryMock->expects($this->once());
        $setDataExpects->method('getParcelCount');
        $setDataExpects->willReturn($parcelCount);

        $saveExpects = $shipmentFactoryMock->expects($this->once());
        $saveExpects->method('save');

        $setDataExpects = $shipmentFactoryMock->expects($parcelCount > 1 ? $this->once() : $this->never());
        $setDataExpects->method('getEntityId');

        $shipmentMockBuilder = $this->getMockBuilder(Shipment::class, ['getId']);
        $shipmentMockBuilder->disableOriginalConstructor();
        $shipmentMock = $shipmentMockBuilder->getMock();

        $getIdMock = $shipmentMock->expects($this->once());
        $getIdMock->method('getId');
        $getIdMock->willReturn($id);

        $shipmentBarcodeFactoryMock = $this->getShipmentBarcodeFactoryMock($id, $barcode, $parcelCount);

        $shipmentBarcodeCollectionFactoryMock = $this->getFakeMock(
            '\TIG\PostNL\Model\ResourceModel\ShipmentBarcode\CollectionFactory'
        );
        $shipmentBarcodeCollectionFactoryMock->setMethods(
            ['create', 'load', 'addItem', 'save']
        );
        $shipmentBarcodeCollectionFactoryMock = $shipmentBarcodeCollectionFactoryMock->getMock();

        $createExpects = $shipmentBarcodeCollectionFactoryMock->expects($this->exactly($parcelCount < 2 ? 0 : 1));
        $createExpects->method('create');
        $createExpects->willReturnSelf();

        $createExpects = $shipmentBarcodeCollectionFactoryMock->expects($this->exactly($parcelCount < 2 ? 0 : 1));
        $createExpects->method('load');

        $createExpects = $shipmentBarcodeCollectionFactoryMock->expects(
            $this->exactly($parcelCount < 2 ? 0 : $parcelCount)
        );
        $createExpects->method('addItem');

        $saveExpects = $shipmentBarcodeCollectionFactoryMock->expects($this->exactly($parcelCount < 2 ? 0 : 1));
        $saveExpects->method('save');

        $barcodeMock = $this->getFakeMock('TIG\PostNL\Webservices\Endpoints\Barcode');
        $barcodeMock->setMethods(['call']);
        $barcodeMock = $barcodeMock->getMock();

        $callExpects = $barcodeMock->expects($this->atLeastOnce());
        $callExpects->method('call');
        $callExpects->willReturn((Object)['Barcode' => $barcode]);

        /** @var Observer $observer */
        $observer = $this->objectManager->getObject(Observer::class);
        $observer->setData('data_object', $shipmentMock);

        $instance = $this->getInstance([
            'shipmentFactory' => $shipmentFactoryMock,
            'shipmentBarcodeFactory' => $shipmentBarcodeFactoryMock,
            'shipmentBarcodeCollectionFactory' => $shipmentBarcodeCollectionFactoryMock,
            'barcode' => $barcodeMock
        ]);
        $instance->execute($observer);
    }

    /**
     * @return array
     */
    public function generateBarcodeProvider()
    {
        return [
            [
                (Object)['Barcode' => '3STOTA123457890'],
                '3STOTA123457890'
            ],
            [
                'Response by unittest',
                __('Invalid GenerateBarcode response: %1', var_export('Response by unittest', true))
            ],
        ];
    }

    /**
     * @param $callReturnValue
     * @param $expected
     *
     * @dataProvider generateBarcodeProvider
     */
    public function testGenerateBarcode($callReturnValue, $expected)
    {
        $barcodeMock = $this->getFakeMock('TIG\PostNL\Webservices\Endpoints\Barcode');
        $barcodeMock->setMethods(['call']);
        $barcodeMock = $barcodeMock->getMock();

        $callExpects = $barcodeMock->expects($this->once());
        $callExpects->method('call');
        $callExpects->willReturn($callReturnValue);

        $instance = $this->getInstance(['barcode' => $barcodeMock]);
        $result = $this->invoke('generateBarcode', $instance);

        $this->assertEquals($expected, $result);
    }

    /**
     * @param $id
     * @param $barcode
     * @param $parcelCount
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getShipmentBarcodeFactoryMock($id, $barcode, $parcelCount)
    {
        if ($parcelCount < 2) {
            $parcelCount = 0;
        }

        $shipmentBarcodeFactoryMock = $this->getFakeMock('\TIG\PostNL\Model\ShipmentBarcodeFactory');
        $shipmentBarcodeFactoryMock->setMethods(
            ['create', 'setShipmentId', 'setType', 'setNumber', 'setValue', 'save']
        );
        $shipmentBarcodeFactoryMock = $shipmentBarcodeFactoryMock->getMock();

        $createExpects = $shipmentBarcodeFactoryMock->expects($this->exactly($parcelCount));
        $createExpects->method('create');
        $createExpects->willReturnSelf();

        $setShipmentIdExpects = $shipmentBarcodeFactoryMock->expects($this->exactly($parcelCount));
        $setShipmentIdExpects->method('setShipmentId');

        $setShipmentIdExpects = $shipmentBarcodeFactoryMock->expects($this->exactly($parcelCount));
        $setShipmentIdExpects->method('setType');
        $setShipmentIdExpects->with(ShipmentBarcode::BARCODE_TYPE_SHIPMENT);

        $setShipmentIdExpects = $shipmentBarcodeFactoryMock->expects($this->exactly($parcelCount));
        $setShipmentIdExpects->method('setNumber');

        $setShipmentIdExpects = $shipmentBarcodeFactoryMock->expects($this->exactly($parcelCount));
        $setShipmentIdExpects->method('setValue');
        $setShipmentIdExpects->with($barcode);

        return $shipmentBarcodeFactoryMock;
    }
}
