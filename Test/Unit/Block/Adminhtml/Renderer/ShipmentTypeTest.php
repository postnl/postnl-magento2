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

namespace TIG\PostNL\Test\Unit\Block\Adminhtml\Renderer;

use TIG\PostNL\Block\Adminhtml\Renderer\ShipmentType;
use TIG\PostNL\Config\Source\Options\ProductOptions;
use TIG\PostNL\Test\TestCase;

class ShipmentTypeTest extends TestCase
{
    public $instanceClass = ShipmentType::class;

    public function returnsTheCorrectResultProvider()
    {
        return [
            'Daytime' => ['3085', 'Daytime', 'Domestic', '', 'Standard shipment'],
            'Evening' => ['3090', 'Evening', 'Domestic', 'Evening', 'Signature on delivery + Delivery to stated address only'],
            'Evening BE' => ['4941', 'Evening', 'EPS', 'Evening', 'EU Pack Standard evening'],
            'Extra@Home' => ['3790', 'ExtraAtHome', 'Extra@Home', '', 'Extra@Home Drempelservice 1 person delivery NL'],
            'Sunday' => ['3385', 'Sunday', 'Domestic', 'Sunday', 'Deliver to stated address only'],
            'Pickup Delivery' => ['3533', 'PG', 'Post office', '', 'Post Office + Signature on Delivery'],
            'Pickup Delivery Early' => ['3543', 'PGE', 'Post office', 'Early morning pickup', 'Post Office + Signature on Delivery + Notification'],
            'EPS' => ['4950', 'EPS', 'EPS', '', 'EU Pack Special'],
            'Letterbox Package' => ['2928', 'Letter Box' , 'Letter Box', '', 'Letter Box Parcel Extra'],
        ];
    }

    /**
     * @param $productCode
     * @param $shipmentType
     * @param $expectedLabel
     * @param $expectedType
     * @param $expectedComment
     *
     * @dataProvider returnsTheCorrectResultProvider
     */
    public function testReturnsTheCorrectResult($productCode, $shipmentType, $expectedLabel, $expectedType, $expectedComment)
    {
        $optionsMock = $this->getFakeMock(ProductOptions::class)->getMock();
        $getLabel = $optionsMock->method('getLabel');
        $getLabel->willReturn([
            'label' => $expectedLabel,
            'type' => $expectedType,
            'comment' => $expectedComment
        ]);

        /** @var ShipmentType $instance */
        $instance = $this->getInstance([
            'productOptions' => $optionsMock
        ]);

        $result = $instance->render($productCode, $shipmentType);

        $this->assertStringContainsString($expectedLabel, $result);

        if ($expectedType) {
            $this->assertStringContainsString($expectedType, $result);
        }
    }
}
