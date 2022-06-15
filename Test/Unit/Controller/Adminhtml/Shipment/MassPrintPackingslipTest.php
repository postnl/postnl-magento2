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
namespace TIG\PostNL\Unit\Controller\Adminhtml\Shipment;

use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Controller\Adminhtml\Shipment\MassPrintPackingslip;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Test\TestCase;

class MassPrintPackingslipTest extends TestCase
{
    protected $instanceClass = MassPrintPackingslip::class;

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

    /**
     * @param $id
     * @param $country
     *
     * @return Shipment|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getShipmentMock($id, $country)
    {
        $addressMock = $this->getFakeMock(Address::class)->setMethods(['getCountryId'])->getMock();
        $addressMock->expects($this->once())->method('getCountryId')->willReturn($country);

        $shipmentMock = $this->getFakeMock(Shipment::class)
            ->setMethods(['getId', 'getShippingAddress', 'getTracks'])
            ->getMock();
        $shipmentMock->expects($this->exactly(2))->method('getId')->willReturn($id);
        $shipmentMock->expects($this->atLeastOnce())->method('getShippingAddress')->willReturn($addressMock);
        $shipmentMock->expects($this->once())->method('getTracks')->willReturn(['some track code']);

        return $shipmentMock;
    }

    public function testLoadLabel()
    {
        $shipmentMock = $this->getShipmentMock(1, 'NL');

        $barcodeHandlerMock = $this->getFakeMock(BarcodeHandler::class)->setMethods(['prepareShipment'])->getMock();
        $barcodeHandlerMock->expects($this->exactly(1))->method('prepareShipment')->with(1, 'NL');

        $instance = $this->getInstance(['barcodeHandler' => $barcodeHandlerMock]);
        $this->invokeArgs('loadLabel', [$shipmentMock], $instance);
    }
}
