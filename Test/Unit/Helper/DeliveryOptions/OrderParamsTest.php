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

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Helper\DeliveryOptions\OrderParams;
use TIG\PostNL\Service\Order\FeeCalculator;
use TIG\PostNL\Service\Order\ProductCodeAndType;
use TIG\PostNL\Service\Shipment\ProductOptions;

class OrderParamsTest extends TestCase
{
    protected $instanceClass = OrderParams::class;

    private $validDaytimeParams = [
        'type' => 'delivery',
        'option' => 'daytime',
        'country' => 'NL',
        'quote_id' => '1',
        'date' => '2018-06-11',
        'from' => '12:00:00',
        'to' => '19:00:00'
    ];

    public function testGet()
    {
        $productInfo = [
            'code' => '3085',
            'type' => 'Daytime',
        ];

        $productCodeAndTypeMock = $this->getFakeMock(ProductCodeAndType::class)->getMock();
        $productCodeAndTypeMock->expects($this->once())->method('get')->willReturn($productInfo);

        $feeCalculatorMock = $this->getFakeMock(FeeCalculator::class)->getMock();
        $feeCalculatorMock->expects($this->once())->method('get')->willReturn(0.0);

        $productOptionsMock = $this->getFakeMock(ProductOptions::class)->getMock();
        $productOptionsMock->expects($this->once())->method('get')->willReturn(null);

        $instance = $this->getInstance([
            'feeCalculator' => $feeCalculatorMock,
            'productCodeAndType' => $productCodeAndTypeMock,
            'productOptions' => $productOptionsMock
        ]);

        $result = $instance->get($this->validDaytimeParams);
        $this->assertArrayHasKey('product_code', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertArrayHasKey('fee', $result);
    }
}
