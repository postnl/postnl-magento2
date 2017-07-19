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

use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Collection;
use TIG\PostNL\Controller\Adminhtml\Shipment\MassPrintShippingLabel;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;
use TIG\PostNL\Test\TestCase;

class MassPrintShippingLabelTest extends TestCase
{
    protected $instanceClass = MassPrintShippingLabel::class;

    /**
     * @return array
     */
    public function getLabelProvider()
    {
        return [
            'no_shipment_ids' => [[], []],
            'single_shipment_id' => [
                [123],
                ['abcdef']
            ],
            'multi_shipment_ids' => [
                [456, 789],
                ['ghijkl', 'mnopqr']
            ]
        ];
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
