<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
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
                'stockOptions' => 'all_products',
                'productsInStock' => true,
                'expected' => true,
            ],
            'active, not in stock and all_products' => [
                'shippingOptionsActive' => true,
                'stockOptions' => 'all_products',
                'productsInStock' => true,
                'expected' => true,
            ],
            'active, in stock and stock_products' => [
                'shippingOptionsActive' => true,
                'stockOptions' => 'stock_products',
                'productsInStock' => true,
                'expected' => true,
            ],
            'active, not in stock and stock_products' => [
                'shippingOptionsActive' => true,
                'stockOptions' => 'stock_products',
                'productsInStock' => false,
                'expected' => false,
            ],
            'inactive, in stock and all_products' => [
                'shippingOptionsActive' => false,
                'stockOptions' => 'all_products',
                'productsInStock' => true,
                'expected' => false,
            ],
            'inactive, not in stock and all_products' => [
                'shippingOptionsActive' => false,
                'stockOptions' => 'all_products',
                'productsInStock' => false,
                'expected' => false,
            ],
            'inactive, in stock and stock_products' => [
                'shippingOptionsActive' => false,
                'stockOptions' => 'stock_products',
                'productsInStock' => true,
                'expected' => false,
            ],
            'inactive, not in stock and stock_products' => [
                'shippingOptionsActive' => false,
                'stockOptions' => 'stock_products',
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
            ->getFakeMock(\TIG\PostNL\Services\Quote\CheckIfQuoteItemsAreInStock::class)
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
