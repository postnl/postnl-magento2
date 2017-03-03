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
namespace TIG\PostNL\Unit\Service\Shipping;

use Magento\Catalog\Model\Product;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Item;
use TIG\PostNL\Service\Shipping\GetFreeBoxes;
use TIG\PostNL\Test\TestCase;

class GetFreeBoxesTest extends TestCase
{
    protected $instanceClass = GetFreeBoxes::class;

    /**
     * @return array
     */
    public function getProvider()
    {
        return [
            'no_items' => [
                null,
                null,
                0
            ],
            'no_free_boxes' => [
                1,
                1,
                0
            ],
            'single_free_box' => [
                3,
                4,
                1
            ],
            'multiple_free_boxes' => [
                2,
                8,
                6
            ]
        ];
    }

    /**
     * @param $freeShipping
     * @param $qty
     * @param $expects
     *
     * @dataProvider getProvider
     */
    public function testGet($freeShipping, $qty, $expects)
    {
        $productMock = $this->getFakeMock(Product::class)->getMock();

        $quoteItemMock = $this->getFakeMock(Item::class)->setMethods(['getProduct', 'getQty', 'getFreeShipping']);
        $quoteItemMock = $quoteItemMock->getMock();

        $getProductExpects = $quoteItemMock->expects(($freeShipping && $qty) ? $this->once() : $this->never());
        $getProductExpects->method('getProduct')->willReturn($productMock);
        $freeShippingExpects = $quoteItemMock->expects(($freeShipping && $qty) ? $this->atLeastOnce() : $this->never());
        $freeShippingExpects->method('getFreeShipping')->willReturn($freeShipping);
        $quoteItemMock->expects($this->any())->method('getQty')->willReturn($qty);

        $rateRequestItems = [];
        if (null !== $freeShipping && null !== $qty) {
            $rateRequestItems = [$quoteItemMock];
        }

        $rateRequestMock = $this->getMockBuilder(RateRequest::class)->setMethods(['getAllItems'])->getMock();
        $rateRequestMock->expects($this->once())->method('getAllItems')->willReturn($rateRequestItems);

        $instance = $this->getInstance();
        $result = $instance->get($rateRequestMock);

        $this->assertEquals($expects, $result);
    }

    /**
     * @return array
     */
    public function getFreeBoxesCountFromChildrenProvider()
    {
        return [
            'no_items' => [
                null,
                null,
                0
            ],
            'no_child_boxes' => [
                1,
                1,
                0
            ],
            'single_child_box' => [
                2,
                3,
                1
            ],
            'multiple_child_boxes' => [
                4,
                6,
                2
            ]
        ];
    }

    /**
     * @param $freeShipping
     * @param $qty
     * @param $expects
     *
     * @dataProvider getFreeBoxesCountFromChildrenProvider
     */
    public function testGetFreeBoxesCountFromChildren($freeShipping, $qty, $expects)
    {
        $productMock = $this->getFakeMock(Product::class)->getMock();

        $abstractItemMock = $this->getFakeMock(Item\AbstractItem::class)->setMethods([
            'getProduct',
            'getFreeShipping',
            'getQty',
            'getQuote',
            'getAddress',
            'getOptionByCode'
        ])->getMock();
        $getProductExpects = $abstractItemMock->expects(($freeShipping && $qty) ? $this->once() : $this->never());
        $getProductExpects->method('getProduct')->willReturn($productMock);
        $getFreeShipping = $abstractItemMock->expects(($freeShipping && $qty) ? $this->atLeastOnce() : $this->never());
        $getFreeShipping->method('getFreeShipping')->willReturn($freeShipping);
        $abstractItemMock->expects($this->any())->method('getQty')->willReturn($qty);

        /** @var Item $quoteItem */
        $quoteItem = $this->getObject(Item::class);

        if (null !== $freeShipping && null !== $qty) {
            $quoteItem->addChild($abstractItemMock);
            $quoteItem->setQty($qty);
        }

        $instance = $this->getInstance();
        $result = $this->invokeArgs('getFreeBoxesCountFromChildren', [$quoteItem], $instance);

        $this->assertEquals($expects, $result);
    }
}
