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
namespace TIG\PostNL\Test\Unit\Config\CheckoutConfiguration;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use TIG\PostNL\Config\CheckoutConfiguration\IsShippingOptionsActive;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Test\TestCase;

class IsShippingOptionsActiveTest extends TestCase
{
    public $instanceClass = IsShippingOptionsActive::class;

    public function getValueProvider()
    {
        return [
            'active, in stock and all_products' => [
                'shippingOptionsActive' => true,
                'stockOptions' => 'in_stock',
                'productsInStock' => true,
                'expected' => true,
            ],
            'active, not in stock and all_products' => [
                'shippingOptionsActive' => true,
                'stockOptions' => 'in_stock',
                'productsInStock' => true,
                'expected' => true,
            ],
            'active, in stock and stock_products' => [
                'shippingOptionsActive' => true,
                'stockOptions' => 'backordered',
                'productsInStock' => true,
                'expected' => true,
            ],
            'active, not in stock and stock_products' => [
                'shippingOptionsActive' => true,
                'stockOptions' => 'backordered',
                'productsInStock' => false,
                'expected' => false,
            ],
            'inactive, in stock and all_products' => [
                'shippingOptionsActive' => false,
                'stockOptions' => 'in_stock',
                'productsInStock' => true,
                'expected' => false,
            ],
            'inactive, not in stock and all_products' => [
                'shippingOptionsActive' => false,
                'stockOptions' => 'in_stock',
                'productsInStock' => false,
                'expected' => false,
            ],
            'inactive, in stock and stock_products' => [
                'shippingOptionsActive' => false,
                'stockOptions' => 'backordered',
                'productsInStock' => true,
                'expected' => false,
            ],
            'inactive, not in stock and stock_products' => [
                'shippingOptionsActive' => false,
                'stockOptions' => 'backordered',
                'productsInStock' => false,
                'expected' => false,
            ],
        ];
    }

    /**
     * @param $shippingOptionsActive
     * @param $stockOptions
     * @param $productsInStock
     * @param $expected
     *
     * @dataProvider getValueProvider
     */
    public function testGetValue($shippingOptionsActive, $stockOptions, $productsInStock, $expected)
    {
        $shippingOptions = $this->getFakeMock(ShippingOptions::class)->getMock();
        $quoteItemsAreInStock = $this
            ->getFakeMock(\TIG\PostNL\Service\Quote\CheckIfQuoteItemsAreInStock::class)
            ->getMock();

        $getValueExpects = $quoteItemsAreInStock->method('getValue');
        $getValueExpects->willReturn($productsInStock);

        /** @var IsShippingOptionsActive $instance */
        $instance = $this->getInstance([
            'shippingOptions' => $shippingOptions,
            'quoteItemsAreInStock' => $quoteItemsAreInStock,
        ]);

        $this->getShippingOptionsActive($shippingOptions, $shippingOptionsActive);
        $this->getShippingStockoptions($shippingOptions, $stockOptions);

        $this->assertEquals($expected, $instance->getValue());
    }

    /**
     * @param $shippingOptions
     * @param $value
     */
    private function getShippingOptionsActive(MockObject $shippingOptions, $value)
    {
        $expects = $shippingOptions->method('isShippingoptionsActive');
        $expects->willReturn($value);
    }

    /**
     * @param $shippingOptions
     * @param $value
     */
    private function getShippingStockoptions(MockObject $shippingOptions, $value)
    {
        $expects = $shippingOptions->expects($this->once());
        $expects->method('getShippingStockoptions');
        $expects->willReturn($value);
    }
}
