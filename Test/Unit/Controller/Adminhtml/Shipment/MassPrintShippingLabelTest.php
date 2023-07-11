<?php

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
