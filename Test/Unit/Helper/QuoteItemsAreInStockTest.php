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
namespace TIG\PostNL\Test\Unit\Helper;

use TIG\PostNL\Helper\QuoteItemsAreInStock;
use TIG\PostNL\Test\TestCase;

class QuoteItemsAreInStockTest extends TestCase
{
    public $instanceClass = QuoteItemsAreInStock::class;

    public function itemsProvider()
    {
        return [
            'has no stock' => [
                [false, false, false],
                false
            ],
            'only 1 in stock' => [
                [false, true, false],
                false
            ],
            'all in stock' => [
                [true, true, true],
                true
            ],
        ];
    }

    /**
     * @param $items
     * @param $expected
     *
     * @dataProvider itemsProvider
     */
    public function testGetValue($items, $expected)
    {
        $sessionMock = $this->getFakeMock(\Magento\Checkout\Model\Session\Proxy::class);
        $sessionMock->setMethods(['getQuote', 'getAllItems']);
        $sessionMock = $sessionMock->getMock();

        $sessionMock->method('getQuote')->willReturnSelf();
        $sessionMock->method('getAllitems')->willReturn($this->getAllItems($items));

        $instance = $this->getInstance([
            'checkoutSession' => $sessionMock,
        ]);

        $this->assertEquals($expected, $instance->getValue());
    }

    /**
     * @param $items
     * @param $expected
     *
     * @dataProvider itemsProvider
     */
    public function testItemsAreInStock($items, $expected)
    {
        $input = $this->getAllItems($items);

        $result = $this->invokeArgs('itemsAreInStock', [$input]);

        $this->assertEquals($expected, $result);
    }

    /**
     * @param $items
     *
     * @return array
     */
    private function getAllItems($items)
    {
        $input = [];
        foreach ($items as $item) {
            $mock = $this->getFakeMock(\Magento\Quote\Model\Quote\Item::class);
            $mock->setMethods(['getProduct', 'isInStock']);
            $mock = $mock->getMock();

            $mock->method('getProduct')->willReturnSelf();
            $mock->method('isInStock')->willReturn($item);

            $input[] = $mock;
        }

        return $input;
    }
}
