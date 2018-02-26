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
namespace TIG\PostNL\Test\Unit\Service\Shipment\Packingslip;

use Magento\Sales\Model\Order\Pdf\Shipment as PdfShipment;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Config\Provider\LabelAndPackingslipOptions;
use TIG\PostNL\Config\Source\LabelAndPackingslip\ShowShippingLabel;
use TIG\PostNL\Service\Shipment\Packingslip\GetPackingslip;
use TIG\PostNL\Service\Shipment\Packingslip\MergeWithLabels;
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

        $pdfShipmentMock = $this->getFakeMock(PdfShipment::class)->setMethods(['getPdf', 'render'])->getMock();
        $pdfShipmentMock->expects($this->once())->method('getPdf')->with([$shipmentRepoMock])->willReturnSelf();
        $pdfShipmentMock->expects($this->once())->method('render')->willReturn('packingslip pdf');

        $instance = $this->getInstance([
            'shipmentRepository' => $shipmentRepoMock,
            'pdfShipment' => $pdfShipmentMock
        ]);
        $result = $instance->get(1);
        $this->assertEquals('packingslip pdf', $result);
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
            ->setMethods(['mergeTogether', 'mergeSeparate'])
            ->getMock();
        $mergeWithLabelsMock->method('mergeTogether')->willReturn('merge together');
        $mergeWithLabelsMock->method('mergeSeparate')->willReturn('merge separate');

        $instance = $this->getInstance([
            'labelAndPackingslipOptions' => $labelPackinslipOptionsMock,
            'mergeWithLabels' => $mergeWithLabelsMock
        ]);

        $result = $this->invokeArgs('mergeWithLabels', [0, 'not merged'], $instance);
        $this->assertEquals($expected, $result);
    }
}
