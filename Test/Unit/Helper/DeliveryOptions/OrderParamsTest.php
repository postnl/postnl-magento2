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
namespace TIG\PostNL\Test\Unit\Helper\DeliveryOptions;

use TIG\PostNL\Helper\DeliveryOptions\OrderParams;
use TIG\PostNL\Service\Order\FeeCalculator;
use TIG\PostNL\Service\Order\ProductInfo;
use TIG\PostNL\Service\Shipment\ProductOptions;
use TIG\PostNL\Test\TestCase;

class OrderParamsTest extends TestCase
{
    protected $instanceClass = OrderParams::class;

    public function getValidParams()
    {
        return [
            'Netherlands' => [[
                'type'     => 'delivery',
                'option'   => 'daytime',
                'country'  => 'NL',
                'quote_id' => '1',
                'date'     => '2018-06-11',
                'from'     => '12:00:00',
                'to'       => '19:00:00',
                'address'  => ['postcode' => '1037BA', 'country' => 'NL']],
                'daytime'
            ],
            'LaPalma' => [[
              'type'     => 'GP',
              'country'  => 'ES',
              'quote_id' => '1',
              'date'     => '2020-08-18',
              'from'     => '',
              'to'       => '',
              'address'  => ['postcode' => '38712', 'country' => 'ES']],
              'GP'
            ]
        ];
    }

    /**
     * @param $option
     * @param $expectedType
     *
     * @dataProvider getValidParams
     * @throws \Exception
     */
    public function testGet($option, $expectedType)
    {
        $productMock = [
            'code' => '3085',
            'type' => 'Daytime',
        ];

        $productInfoMock = $this->getFakeMock(ProductInfo::class)->getMock();
        $productInfoMock->expects($this->once())->method('get')->willReturn($productMock);

        $feeCalculatorMock = $this->getFakeMock(FeeCalculator::class)->getMock();
        $feeCalculatorMock->expects($this->once())->method('get')->willReturn(0.0);

        $productOptionsMock = $this->getFakeMock(ProductOptions::class)->getMock();
        $productOptionsMock->expects($this->once())->method('getByType')->willReturn(null);

        $instance = $this->getInstance([
            'feeCalculator' => $feeCalculatorMock,
            'productInfo' => $productInfoMock,
            'productOptions' => $productOptionsMock
        ]);

        $result = $instance->get($option);

        $this->assertArrayHasKey('product_code', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('fee', $result);
        $this->assertEquals($expectedType, $result['type']);
    }
}
