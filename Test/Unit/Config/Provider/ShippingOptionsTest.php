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

use TIG\PostNL\Config\Provider\ShippingOptions;
use Magento\Store\Model\ScopeInterface;

class ShippingOptionsTest extends AbstractConfigurationTest
{
    protected $instanceClass = ShippingOptions::class;

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::enabledAndDisabled
     * @param $value
     */
    public function testIsShippingoptionsActive($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ShippingOptions::XPATH_SHIPPING_OPTION_ACITVE, $value);
        $this->assertEquals($value, $instance->isShippingoptionsActive());
    }

    public function stockOptions()
    {
        return [
            ['backordered'],
            ['in_stock']
        ];
    }

    /**
     * @dataProvider stockOptions
     * @param $value
     */
    public function testGetShippingStockoptions($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ShippingOptions::XPATH_SHIPPING_OPTION_STOCK, $value);
        $this->assertEquals($value, $instance->getShippingStockoptions());
    }

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::enabledAndDisabled
     * @param $value
     */
    public function testIsDeliverydaysActive($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ShippingOptions::XPATH_SHIPPING_OPTION_DELIVERYDAYS_ACTIVE, $value);
        $this->assertEquals($value, $instance->isDeliverydaysActive());
    }

    public function deliverydaysProvider()
    {
        return [
            'deliverydays not active' => [false, '5'],
            'deliverydays active' => [true, rand(1, 14)]
        ];
    }

    /**
     * @dataProvider deliverydaysProvider
     * @param $deliveryDaysActive
     * @param $expected
     */
    public function testGetMaxAmountOfDeliverydays($deliveryDaysActive, $expected)
    {
        $instance = $this->getInstance();

        $xpaths = [
            [
                ShippingOptions::XPATH_SHIPPING_OPTION_DELIVERYDAYS_ACTIVE,
                ScopeInterface::SCOPE_STORE,
                null
            ],
            [
                ShippingOptions::XPATH_SHIPPING_OPTION_MAX_DELIVERYDAYS,
                ScopeInterface::SCOPE_STORE,
                null
            ]
        ];

        $returns = [
            $deliveryDaysActive, $expected
        ];

        $this->setXpathConsecutive($xpaths, $returns);

        $this->assertEquals($expected, $instance->getMaxAmountOfDeliverydays());
    }

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::enabledAndDisabled
     * @param $value
     */
    public function testIsPakjegemakActive($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ShippingOptions::XPATH_SHIPPING_OPTION_PAKJEGEMAK_ACTIVE, $value);
        $this->assertEquals($value, $instance->isPakjegemakActive());
    }

    public function pakjegemakExpressProvider()
    {
        return [
            'with pakjegemak not active'             => [false, false],
            'with pakjegemak active but not express' => [true, false],
            'with pakjegemak active and express'     => [true, true]
        ];
    }

    /**
     * @dataProvider pakjegemakExpressProvider
     * @param $pakjegemakActive
     * @param $expected
     */
    public function testIsPakjegemakExpressActive($pakjegemakActive, $expected)
    {
        $instance = $this->getInstance();

        $xpaths = [
            [
                ShippingOptions::XPATH_SHIPPING_OPTION_PAKJEGEMAK_ACTIVE,
                ScopeInterface::SCOPE_STORE,
                null
            ],
            [
                ShippingOptions::XPATH_SHIPPING_OPTION_PAKJEGEMAK_EXPRESS_ACTIVE,
                ScopeInterface::SCOPE_STORE,
                null
            ]
        ];

        $returns = [
            $pakjegemakActive, $expected
        ];

        $this->setXpathConsecutive($xpaths, $returns);
        $this->assertEquals($expected, $instance->isPakjegemakExpressActive());
    }

    public function feeProvider()
    {
        $values = [
            '1',
            '3',
            '4',
            '5'
        ];

        return [
            'when parent not active' => [false, '0'],
            'when parent is active'   => [true, $values[rand(0, 3)]]
        ];
    }

    /**
     * @dataProvider feeProvider
     * @param $pakjegemakExpressActive
     * @param $expected
     */
    public function testGetPakjegemakExpressFee($pakjegemakExpressActive, $expected)
    {
        $instance = $this->getInstance();

        $xpaths = [
            [
                ShippingOptions::XPATH_SHIPPING_OPTION_PAKJEGEMAK_ACTIVE,
                ScopeInterface::SCOPE_STORE,
                null
            ],
            [
                ShippingOptions::XPATH_SHIPPING_OPTION_PAKJEGEMAK_ACTIVE,
                ScopeInterface::SCOPE_STORE,
                null
            ],
            [
                ShippingOptions::XPATH_SHIPPING_OPTION_PAKJEGEMAK_EXPRESS_ACTIVE,
                ScopeInterface::SCOPE_STORE,
                null
            ]
        ];

        $returns = [
            true, $pakjegemakExpressActive, $expected
        ];

        $this->setXpathConsecutive($xpaths, $returns);
        $this->assertEquals($expected, $instance->getPakjegemakExpressFee());
    }

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::enabledAndDisabled
     * @param $value
     */
    public function testIsEveningDeliveryActive($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ShippingOptions::XPATH_SHIPPING_OPTION_EVENING_ACTIVE, $value);
        $this->assertEquals($value, $instance->isEveningDeliveryActive());
    }

    /**
     * @dataProvider feeProvider
     * @param $isEveningActive
     * @param $expected
     */
    public function testGetEveningDeliveryFee($isEveningActive, $expected)
    {
        $instance = $this->getInstance();

        $xpaths = [
            [
                ShippingOptions::XPATH_SHIPPING_OPTION_EVENING_ACTIVE,
                ScopeInterface::SCOPE_STORE,
                null
            ],
            [
                ShippingOptions::XPATH_SHIPPING_OPTION_EVENING_FEE,
                ScopeInterface::SCOPE_STORE,
                null
            ]
        ];

        $returns = [
            $isEveningActive, $expected
        ];

        $this->setXpathConsecutive($xpaths, $returns);
        $this->assertEquals($expected, $instance->getEveningDeliveryFee());
    }

    /**
     * @param $value
     *
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::enabledAndDisabled
     */
    public function testIsExtraAtHomeActive($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ShippingOptions::XPATH_SHIPPING_OPTION_EXTRAATHOME_ACTIVE, $value);

        $result = $instance->isExtraAtHomeActive();
        $this->assertEquals($value, $result);
    }

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::enabledAndDisabled
     * @param $value
     */
    public function testIsSundayDeliveryActive($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ShippingOptions::XPATH_SHIPPING_OPTION_SUNDAY_ACTIVE, $value);
        $this->assertEquals($value, $instance->isSundayDeliveryActive());
    }

    /**
     * @dataProvider feeProvider
     * @param $isSundayActive
     * @param $expected
     */
    public function testGetSundayDeliveryFee($isSundayActive, $expected)
    {
        $instance = $this->getInstance();

        $xpaths = [
            [
                ShippingOptions::XPATH_SHIPPING_OPTION_SUNDAY_ACTIVE,
                ScopeInterface::SCOPE_STORE,
                null
            ],
            [
                ShippingOptions::XPATH_SHIPPING_OPTION_SUNDAY_FEE,
                ScopeInterface::SCOPE_STORE,
                null
            ]
        ];

        $returns = [
            $isSundayActive, $expected
        ];

        $this->setXpathConsecutive($xpaths, $returns);
        $this->assertEquals($expected, $instance->getSundayDeliveryFee());
    }

    public function testGetDeliveryDelay()
    {
        $value = rand(1, 4);

        $instance = $this->getInstance();
        $this->setXpath(ShippingOptions::XPATH_SHIPPING_OPTION_DELIVERY_DELAY, $value);
        $this->assertEquals($value, $instance->getDeliveryDelay());
    }
}
