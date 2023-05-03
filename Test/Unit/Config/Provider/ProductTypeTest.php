<?php

namespace TIG\PostNL\Test\Unit\Config\Provider;

use Magento\Catalog\Model\Product;
use TIG\PostNL\Config\Provider\ProductType;
use TIG\PostNL\Test\TestCase;

class ProductTypeTest extends TestCase
{
    public $instanceClass = ProductType::class;

    public function testHasTheRegularProductType()
    {
        $options = $this->getInstance()->getAllOptions();

        $options = array_filter($options, function ($item) {
            return $item['value'] === ProductType::PRODUCT_TYPE_REGULAR;
        });

        $this->assertCount(1, $options);
    }

    public function testTheOptionListContainsExtraAtHome()
    {
        $shippingOptionsMock = $this->getFakeMock(\TIG\PostNL\Config\Provider\ShippingOptions::class, true);
        $isExtraAtHomeActive = $shippingOptionsMock->method('isExtraAtHomeActive');
        $isExtraAtHomeActive->willReturn(true);

        /** @var ProductType $instance */
        $instance = $this->getInstance(['shippingOptions' => $shippingOptionsMock]);

        $options = array_filter($instance->getAllOptions(), function ($item) {
            return $item['value'] == ProductType::PRODUCT_TYPE_EXTRA_AT_HOME;
        });

        $this->assertCount(1, $options);
    }

    public function testExtraAtHomeIsNotAvailableWhenNotEnabled()
    {
        $shippingOptionsMock = $this->getFakeMock(\TIG\PostNL\Config\Provider\ShippingOptions::class, true);
        $isExtraAtHomeActive = $shippingOptionsMock->method('isExtraAtHomeActive');
        $isExtraAtHomeActive->willReturn(false);

        /** @var ProductType $instance */
        $instance = $this->getInstance(['shippingOptions' => $shippingOptionsMock]);

        $options = array_filter($instance->getAllOptions(), function ($item) {
            return $item['value'] == ProductType::PRODUCT_TYPE_EXTRA_AT_HOME;
        });

        $this->assertCount(0, $options);
    }

    public function testHasTheCorrectTypes()
    {
        $productMock = $this->getFakeMock(Product::class)->getMock();

        $options = $this->getInstance()->getAllTypes($productMock);

        $this->assertTrue(in_array(ProductType::PRODUCT_TYPE_REGULAR, $options));
        $this->assertTrue(in_array(ProductType::PRODUCT_TYPE_EXTRA_AT_HOME, $options));
    }
}
