<?php

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
     * @param $value
     *
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::enabledAndDisabled
     */
    public function testIsIDCheckActive($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ShippingOptions::XPATH_SHIPPING_OPTION_IDCHECK_ACTIVE, $value);

        $result = $instance->isIDCheckActive();
        $this->assertEquals($value, $result);
    }

    public function testGetDeliveryDelay()
    {
        $value = rand(1, 4);

        $instance = $this->getInstance();
        $this->setXpath(ShippingOptions::XPATH_SHIPPING_OPTION_DELIVERY_DELAY, $value);
        $this->assertEquals($value, $instance->getDeliveryDelay());
    }

    /**
     * @return array
     */
    public function guaranteedProvider()
    {
        return [
            'shipping options active' => [true, true],
            'shipping options not active' => [false, false],
        ];
    }

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::enabledAndDisabled
     * @param $shippingActive
     */
    public function testIsGuaranteedDeliveryActive($value)
    {
        $instance = $this->getInstance();

        $this->setXpath(ShippingOptions::XPATH_GUARANTEED_DELIVERY_ACTIVE, $value);
        $this->assertEquals($value, $instance->isGuaranteedDeliveryActive());
    }
}
