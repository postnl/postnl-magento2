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

use TIG\PostNL\Controller\Adminhtml\Shipment\MassPrintShippingLabel;
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
     * @param $shipmentIds
     * @param $getLabelReturn
     *
     * @dataProvider getLabelProvider
     */
    public function testGetLabel($shipmentIds, $getLabelReturn)
    {
        $getLabelsMock = $this->getFakeMock(GetLabels::class);
        $getLabelsMock->setMethods(['get']);
        $getLabelsMock = $getLabelsMock->getMock();

        $map = [];
        $expectedResult = [];
        for ($i = 0; $i < count($shipmentIds); $i++) {
            $expectedResult[$shipmentIds[$i]] = $getLabelReturn[$i];

            $returnValue = [$shipmentIds[$i] => $getLabelReturn[$i]];
            $map[] = [$shipmentIds[$i], $returnValue];
        }

        $getExpects = $getLabelsMock->expects($this->exactly(count($shipmentIds)));
        $getExpects->method('get');
        $getExpects->willReturnMap($map);

        $instance = $this->getInstance(['getLabels' => $getLabelsMock]);

        foreach ($shipmentIds as $shipmentId) {
            $this->invokeArgs('setLabel', [$shipmentId], $instance);
        }

        $labelsProperty = $this->getProperty('labels', $instance);
        $this->assertEquals($expectedResult, $labelsProperty);
    }
}
