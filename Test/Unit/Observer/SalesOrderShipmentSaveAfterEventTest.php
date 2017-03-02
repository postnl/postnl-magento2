<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
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
        $this->markTestSkipped('Should be fixed');

        $shipAt = '2016-11-19';

        $shipmentMock = $this->getFakeMock(\TIG\PostNL\Model\Shipment::class)
            ->setMethods(['save', 'setData', 'getParcelCount', 'getEntityId'])
            ->getMock();

        $setDataExpects = $shipmentMock->expects($this->once());
        $setDataExpects->method('setData');
        $setDataExpects->with([
            'ship_at' => $shipAt,
            'shipment_id' => $id,
            'main_barcode' => $barcode,
        ]);

        $getParcelCountMock = $shipmentMock->expects($this->once());
        $getParcelCountMock->method('getParcelCount');
        $getParcelCountMock->willReturn($parcelCount);

        $saveExpects = $shipmentMock->expects($this->once());
        $saveExpects->method('save');

        $shipmentFactoryMock = $this->getFakeMock(\TIG\PostNL\Model\ShipmentFactory::class)
            ->setMethods(['create'])->getMock();
        $shipmentFactoryMock->method('create')->willReturn($shipmentMock);

        $shipmentMockBuilder = $this->getMockBuilder(Shipment::class, ['getId'])->disableOriginalConstructor();
        $shipmentMock = $shipmentMockBuilder->getMock();

        $shipmentMock->method('getId')->willReturn($id);

        $sentDateHandlerMock = $this->getFakeMock(\TIG\PostNL\Observer\Handlers\SentDateHandler::class)->getMock();
        $getExpects = $sentDateHandlerMock->expects($this->once());
        $getExpects->method('get');
        $getExpects->with($shipmentMock);
        $getExpects->willReturn($shipAt);

        $barcodeHandlerMock = $this->getFakeMock(\TIG\PostNL\Observer\Handlers\BarcodeHandler::class)->getMock();
        $barcodeHandlerMock->method('generate')->willReturn($barcode);

        if ($parcelCount > 1) {
            $saveShipmentExpects = $barcodeHandlerMock->expects($this->once());
            $saveShipmentExpects->method('saveShipment');
            $saveShipmentExpects->with(null, $parcelCount);
        }

        /** @var Observer $observer */
        $observer = $this->objectManager->getObject(Observer::class);
        $observer->setData('data_object', $shipmentMock);

        $instance = $this->getInstance([
            'shipmentFactory' => $shipmentFactoryMock,
            'sendDateHandler' => $sentDateHandlerMock,
            'barcodeHandler' => $barcodeHandlerMock,
        ]);
        $instance->execute($observer);
    }
}
