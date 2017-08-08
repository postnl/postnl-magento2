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
namespace TIG\PostNL\Test\Unit\Service\Order;

use TIG\PostNL\Config\Provider\ProductOptions;
use TIG\PostNL\Service\Order\ProductCode;
use TIG\PostNL\Test\TestCase;

/**
 * Retrieve the product code for a order/shipment.
 */
class ProductCodeTest extends TestCase
{
    const PRODUCT_OPTION_DEFAULT = 'default_product_option';
    const PRODUCT_OPTION_EVENING = 'evening_product_option';
    const PRODUCT_OPTION_EXTRAATHOME = 'extraathome_product_option';
    const PRODUCT_OPTION_PAKJEGEMAK = 'pakjegemak_product_option';
    const PRODUCT_OPTION_PAKJEGEMAK_EARLY = 'pakjegemak_early_product_option';
    const PRODUCT_OPTION_SUNDAY = 'sunday_product_option';

    /**
     * @var ProductOptions|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productOptionsMock;

    /**
     * @var ProductCode
     */
    private $instance;

    public $instanceClass = ProductCode::class;

    public function setUp()
    {
        parent::setUp();

        $this->productOptionsMock = $this->getFakeMock(ProductOptions::class)->getMock();
        $productOptionsFinder = $this->getObject(\TIG\PostNL\Config\Source\Options\ProductOptions::class);

        $this->instance = $this->getInstance([
            'productOptionsConfiguration' => $this->productOptionsMock,
            'productOptionsFinder' => $productOptionsFinder,
        ]);

//        getEpsProductOptions

        $this->addProductOptionsMockFunction('getDefaultProductOption', static::PRODUCT_OPTION_DEFAULT);
        $this->addProductOptionsMockFunction('getDefaultEveningProductOption', static::PRODUCT_OPTION_EVENING);
        $this->addProductOptionsMockFunction('getDefaultExtraAtHomeProductOption', static::PRODUCT_OPTION_EXTRAATHOME);
        $this->addProductOptionsMockFunction('getDefaultPakjeGemakProductOption', static::PRODUCT_OPTION_PAKJEGEMAK);
        $this->addProductOptionsMockFunction('getDefaultSundayProductOption', static::PRODUCT_OPTION_SUNDAY);
        $this->addProductOptionsMockFunction(
            'getDefaultPakjeGemakEarlyProductOption',
            static::PRODUCT_OPTION_PAKJEGEMAK_EARLY
        );
    }

    public function getShippingOptionProvider()
    {
        return [
            'default options' => ['', '', 'NL', static::PRODUCT_OPTION_DEFAULT],
            'default options BE' => ['', '', 'BE', '4950'],
            'default options DE' => ['', '', 'DE', '4950'],
            'default options ES' => ['', '', 'ES', '4950'],
            'no option' => ['delivery', '', 'NL', static::PRODUCT_OPTION_DEFAULT],
            'default' => ['delivery', 'default', 'NL', static::PRODUCT_OPTION_DEFAULT],
            'evening' => ['delivery', 'evening', 'NL', static::PRODUCT_OPTION_EVENING],
            'extra at home' => ['delivery', 'extra@home', 'NL', static::PRODUCT_OPTION_EXTRAATHOME],
            'sunday' => ['delivery', 'sunday', 'NL', static::PRODUCT_OPTION_SUNDAY],
            'default pg' => ['pickup', 'default', 'NL', static::PRODUCT_OPTION_PAKJEGEMAK],
            'pakjegemak' => ['pickup', '', 'NL', static::PRODUCT_OPTION_PAKJEGEMAK],
            'pakjegemak early morning' => ['pickup', 'PGE', 'NL', static::PRODUCT_OPTION_PAKJEGEMAK_EARLY],
        ];
    }

    /**
     * @param $type
     * @param $option
     * @param $country
     * @param $expected
     *
     * @dataProvider getShippingOptionProvider
     */
    public function testGetShippingOption($type, $option, $country, $expected)
    {
        $this->assertEquals($expected, $this->instance->get($type, $option, $country));
    }

    private function addProductOptionsMockFunction($function, $returnValue)
    {
        $expects = $this->productOptionsMock->method($function);
        $expects->willReturn($returnValue);
    }
}
