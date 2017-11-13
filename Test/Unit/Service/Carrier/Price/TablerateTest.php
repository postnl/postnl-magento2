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

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Item;
use TIG\PostNL\Model\ResourceModel\Tablerate as TablerateModel;
use TIG\PostNL\Model\ResourceModel\TablerateFactory;
use TIG\PostNL\Service\Carrier\Price\Tablerate;
use TIG\PostNL\Test\TestCase;

class TablerateTest extends TestCase
{
    protected $instanceClass = Tablerate::class;

    public function testGetTableratePrice()
    {
        $productMock = $this->getFakeMock(Item\AbstractItem::class)->getMock();

        $itemMock = $this->getFakeMock(Item::class)->setMethods(['getProduct'])->getMock();
        $itemMock->expects($this->any())->method('getProduct')->willReturn($productMock);

        $rateRequestMock = $this->getMockBuilder(RateRequest::class)->setMethods(['getAllItems'])->getMock();
        $rateRequestMock->expects($this->atLeastOnce())->method('getAllItems')->willReturn([$itemMock]);

        $tablerateMock = $this->getFakeMock(TablerateModel::class)->setMethods(['getRate'])->getMock();
        $tablerateMock->expects($this->once())->method('getRate')->willReturn(['price' => 1, 'cost' => 1]);

        $tablerateFactoryMock = $this->getFakeMock(TablerateFactory::class)->setMethods(['create'])->getMock();
        $tablerateFactoryMock->expects($this->once())->method('create')->willReturn($tablerateMock);

        $instance = $this->getInstance(['tablerateFactory' => $tablerateFactoryMock]);
        $result = $instance->getTableratePrice($rateRequestMock, false);

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('price', $result);
        $this->assertArrayHasKey('cost', $result);
    }

    /**
     * @return array
     */
    public function getVirtualItemRowTotalProvider()
    {
        return [
            'product_not_virtual' => [
                false,
                10,
                null,
                null,
                0
            ],
            'product_is_virtual' => [
                true,
                20,
                null,
                null,
                20
            ],
            'child_not_virtual' => [
                false,
                30,
                false,
                40,
                0
            ],
            'child_is_virtual' => [
                false,
                50,
                true,
                60,
                60
            ],
            'both_are_virtual' => [
                true,
                70,
                true,
                80,
                80
            ]
        ];
    }

    /**
     * @param $isVirtual
     * @param $baseRowTotal
     * @param $childIsVirtual
     * @param $childBaseRowTotal
     * @param $expected
     *
     * @dataProvider getVirtualItemRowTotalProvider
     */
    public function testGetVirtualItemRowTotal($isVirtual, $baseRowTotal, $childIsVirtual, $childBaseRowTotal, $expected)
    {
        $productChildMock = $this->getFakeMock(Item\AbstractItem::class)
            ->setMethods(['isVirtual', 'getQuote', 'getAddress', 'getOptionByCode'])
            ->getMock();
        $isVirtualExpects = $productChildMock->expects((null !== $childBaseRowTotal) ? $this->once() : $this->never());
        $isVirtualExpects->method('isVirtual')->willReturn($childIsVirtual);

        $itemChildMock = $this->getFakeMock(Item::class)
            ->setMethods(['getProduct', 'getBaseRowTotal'])
            ->getMock();
        $childGetProduct = $itemChildMock->expects((null !== $childBaseRowTotal) ? $this->once() : $this->never());
        $childGetProduct->method('getProduct')->willReturn($productChildMock);
        $itemChildMock->expects($this->any())->method('getBaseRowTotal')->willReturn($childBaseRowTotal);

        $productMock = $this->getFakeMock(Item\AbstractItem::class)
            ->setMethods(['isVirtual', 'getQuote', 'getAddress', 'getOptionByCode'])
            ->getMock();
        $productMock->expects($this->once())->method('isVirtual')->willReturn($isVirtual);

        $itemMock = $this->getFakeMock(Item::class)
            ->setMethods(['getProduct', 'getBaseRowTotal', 'getHasChildren', 'isShipSeparately', 'getChildren'])
            ->getMock();
        $itemMock->expects($this->once())->method('getProduct')->willReturn($productMock);
        $itemMock->expects($this->any())->method('getBaseRowTotal')->willReturn($baseRowTotal);
        $itemMock->expects($this->once())->method('getHasChildren')->willReturn(null !== $childBaseRowTotal);
        $itemMock->expects($this->any())->method('isShipSeparately')->willReturn(null !== $childBaseRowTotal);
        $itemMock->expects($this->any())->method('getChildren')->willReturn([$itemChildMock]);

        $instance = $this->getInstance();
        $result = $this->invokeArgs('getVirtualItemRowTotal', [$itemMock], $instance);

        $this->assertEquals($expected, $result);
    }
}
