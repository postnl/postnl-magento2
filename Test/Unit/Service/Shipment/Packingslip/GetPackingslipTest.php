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
            ->getMock();
        $shipment = $this->getMock(\TIG\PostNL\Model\Shipment::class);
        $salesShipment = $this->getMock(\Magento\Sales\Model\Order\Shipment::class);
        $shipment->method('getShipment')->willReturn($salesShipment);
        $shipmentRepoMock->expects($this->once())->method('getByShipmentId')->willReturn($shipment);

        $pdfShipmentMock = $this->getFakeMock(PdfShipment::class)->disableOriginalConstructor()->onlyMethods(['create'])->getMock();
        $pdfShipmentMock->expects($this->once())->method('create')->with($salesShipment)->willReturn('packingslip pdf');

        $barcodeMergeMock = $this->getFakeMock(Barcode::class)->disableOriginalConstructor()->onlyMethods(['add'])->getMock();
        $barcodeMergeMock->expects($this->once())->method('add')->with('packingslip pdf', $salesShipment)->willReturn('packingslip pdf with barcode');

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
    public static function mergeWithLabelsProvider()
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
    #[\PHPUnit\Framework\Attributes\DataProvider('mergeWithLabelsProvider')]
    public function testMergeWithLabels($showLabelOption, $expected)
    {
        $labelPackinslipOptionsMock = $this->getFakeMock(LabelAndPackingslipOptions::class)
            ->onlyMethods(['getShowLabel'])
            ->getMock();
        $labelPackinslipOptionsMock->expects($this->once())->method('getShowLabel')->willReturn($showLabelOption);

        $mergeWithLabelsMock = $this->getFakeMock(MergeWithLabels::class)
            ->onlyMethods(['merge'])
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
