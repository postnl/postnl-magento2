<?php

namespace TIG\PostNL\Unit\Service\Shipping;

use TIG\PostNL\Test\Unit\Support\RateRequest;
use TIG\PostNL\Test\Unit\Support\QuoteItem as Item;
use TIG\PostNL\Model\ResourceModel\Tablerate as TablerateModel;
use TIG\PostNL\Model\ResourceModel\TablerateFactory;
use TIG\PostNL\Service\Carrier\Price\Tablerate;
use TIG\PostNL\Test\TestCase;

class TablerateTest extends TestCase
{
    protected $instanceClass = Tablerate::class;

    public function testGetTableratePrice()
    {
        $productMock = $this->createStub(\Magento\Catalog\Model\Product::class);

        $itemMock = $this->getFakeMock(Item::class)->onlyMethods(['getProduct'])->getMock();
        $itemMock->expects($this->atLeastOnce())->method('getProduct')->willReturn($productMock);

        $rateRequestMock = $this->getMockBuilder(RateRequest::class)->onlyMethods(['getAllItems'])->getMock();
        $rateRequestMock->expects($this->atLeastOnce())->method('getAllItems')->willReturn([$itemMock]);

        $tablerateMock = $this->getFakeMock(TablerateModel::class)->onlyMethods(['getRate'])->getMock();
        $tablerateMock->expects($this->once())->method('getRate')->willReturn(['price' => 1, 'cost' => 1]);

        $tablerateFactoryMock = $this->getFakeMock(TablerateFactory::class)->onlyMethods(['create'])->getMock();
        $tablerateFactoryMock->expects($this->once())->method('create')->willReturn($tablerateMock);

        $instance = $this->getInstance(['tablerateFactory' => $tablerateFactoryMock]);
        $result = $instance->getTableratePrice($rateRequestMock, false);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('price', $result);
        $this->assertArrayHasKey('cost', $result);
    }

    /**
     * @return array
     */
    public static function getVirtualItemRowTotalProvider()
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
    #[\PHPUnit\Framework\Attributes\DataProvider('getVirtualItemRowTotalProvider')]
    public function testGetVirtualItemRowTotal($isVirtual, $baseRowTotal, $childIsVirtual, $childBaseRowTotal, $expected)
    {
        $productChildMock = $this->getFakeMock(\Magento\Catalog\Model\Product::class)
            ->onlyMethods(['isVirtual'])
            ->getMock();
        $isVirtualExpects = $productChildMock->expects((null !== $childBaseRowTotal) ? $this->once() : $this->never());
        $isVirtualExpects->method('isVirtual')->willReturn($childIsVirtual);

        $itemChildMock = $this->getFakeMock(Item::class)
            ->onlyMethods(['getProduct', 'getBaseRowTotal'])
            ->getMock();
        $childGetProduct = $itemChildMock->expects((null !== $childBaseRowTotal) ? $this->once() : $this->never());
        $childGetProduct->method('getProduct')->willReturn($productChildMock);
        $itemChildMock->method('getBaseRowTotal')->willReturn($childBaseRowTotal);

        $productMock = $this->getFakeMock(\Magento\Catalog\Model\Product::class)
            ->onlyMethods(['isVirtual'])
            ->getMock();
        $productMock->expects($this->once())->method('isVirtual')->willReturn($isVirtual);

        $itemMock = $this->getFakeMock(Item::class)
            ->onlyMethods(['getProduct', 'getBaseRowTotal', 'getHasChildren', 'isShipSeparately', 'getChildren'])
            ->getMock();
        $itemMock->expects($this->once())->method('getProduct')->willReturn($productMock);
        $itemMock->method('getBaseRowTotal')->willReturn($baseRowTotal);
        $itemMock->expects($this->once())->method('getHasChildren')->willReturn(null !== $childBaseRowTotal);
        $itemMock->method('isShipSeparately')->willReturn(null !== $childBaseRowTotal);
        $itemMock->method('getChildren')->willReturn([$itemChildMock]);

        $instance = $this->getInstance();
        $result = $this->invokeArgs('getVirtualItemRowTotal', [$itemMock], $instance);

        $this->assertEquals($expected, $result);
    }
}
