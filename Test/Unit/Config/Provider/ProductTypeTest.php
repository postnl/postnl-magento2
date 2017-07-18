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

namespace TIG\PostNL\Test\Unit\Config\Provider;

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
        $options = $this->getInstance()->getAllTypes();

        $this->assertTrue(in_array(ProductType::PRODUCT_TYPE_REGULAR, $options));
        $this->assertTrue(in_array(ProductType::PRODUCT_TYPE_EXTRA_AT_HOME, $options));
    }
}
