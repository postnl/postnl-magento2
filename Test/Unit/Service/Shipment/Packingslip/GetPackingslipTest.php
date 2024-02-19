<?php

namespace TIG\PostNL\Test\Unit\Service\Shipment\Packingslip;

use TIG\PostNL\Service\Shipment\Packingslip\Factory as PdfShipment;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Config\Provider\LabelAndPackingslipOptions;
use TIG\PostNL\Config\Source\LabelAndPackingslip\ShowShippingLabel;
use TIG\PostNL\Service\Shipment\Packingslip\GetPackingslip;
use TIG\PostNL\Service\Shipment\Packingslip\MergeWithLabels;
use TIG\PostNL\Service\Shipment\Packingslip\Items\Barcode;
use TIG\PostNL\Test\TestCase;

class GetPackingslipTest extends TestCase
{
    public $instanceClass = GetPackingslip::class;

    public function testGetWillReturnEmptyString()
    {
        $instance = $this->getInstance();
        $result = $instance->get(0);
        $this->assertEquals('', $result);
    }

    public function testGetWillReturnPdf()
    {
        $shipmentRepoMock = $this->getFakeMock(ShipmentRepositoryInterface::class)
            ->setMethods(['getByShipmentId', 'getShipment'])
            ->getMockForAbstractClass();
        $shipmentRepoMock->expects($this->once())->method('getByShipmentId')->willReturnSelf();
        $shipmentRepoMock->expects($this->once())->method('getShipment')->willReturnSelf();

        $pdfShipmentMock = $this->getFakeMock(PdfShipment::class)->disableOriginalConstructor()->setMethods(['create'])->getMock();
        $pdfShipmentMock->expects($this->once())->method('create')->with($shipmentRepoMock)->willReturn('packingslip pdf');

        $barcodeMergeMock = $this->getFakeMock(Barcode::class)->disableOriginalConstructor()->setMethods(['add'])->getMock();
        $barcodeMergeMock->expects($this->once())->method('add')->with('packingslip pdf', $shipmentRepoMock)->willReturn('packingslip pdf with barcode');

        $instance = $this->getInstance([
            'shipmentRepository' => $shipmentRepoMock,
            'pdfShipment' => $pdfShipmentMock,
            'barcode' => $barcodeMergeMock
        ]);
        $result = $instance->get(1);
        $this->assertEquals('packingslip pdf with barcode', $result);
    }

    /**
     * @return array
     */
    public function mergeWithLabelsProvider()
    {
        return [
            'merge together' => [
                ShowShippingLabel::SHOW_SHIPPING_LABEL_TOGETHER,
                'merge together'
            ],
            'merge separate' => [
                ShowShippingLabel::SHOW_SHIPPING_LABEL_SEPARATE,
                'merge separate'
            ],
            'not merged' => [
                ShowShippingLabel::SHOW_SHIPPING_LABEL_NONE,
                'not merged'
            ],
        ];
    }

    /**
     * @param $showLabelOption
     * @param $expected
     *
     * @dataProvider mergeWithLabelsProvider
     */
    public function testMergeWithLabels($showLabelOption, $expected)
    {
        $labelPackinslipOptionsMock = $this->getFakeMock(LabelAndPackingslipOptions::class)
            ->setMethods(['getShowLabel'])
            ->getMock();
        $labelPackinslipOptionsMock->expects($this->once())->method('getShowLabel')->willReturn($showLabelOption);

        $mergeWithLabelsMock = $this->getFakeMock(MergeWithLabels::class)
            ->setMethods(['merge'])
            ->getMock();
        $mergeWithLabelsMock->method('merge')->willReturn($expected);

        $instance = $this->getInstance([
            'labelAndPackingslipOptions' => $labelPackinslipOptionsMock,
            'mergeWithLabels' => $mergeWithLabelsMock
        ]);

        $result = $this->invokeArgs('mergeWithLabels', [0, 'not merged', 0], $instance);
        $this->assertEquals($expected, $result);
    }
}
