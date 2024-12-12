<?php

namespace TIG\PostNL\Test\Unit\Config\Provider;

use TIG\PostNL\Config\Provider\ProductOptions;

class ProductOptionsTest extends AbstractConfigurationTest
{
    protected $instanceClass = ProductOptions::class;

    public function defaultProductOptionsProvider()
    {
        return [
            'Standard shipment' => ['3085'],
            'Signature on delivery' => ['3189']
        ];
    }

    /**
     * @dataProvider defaultProductOptionsProvider
     * @param $value
     */
    public function testGetDefaultProductOption($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ProductOptions::XPATH_DEFAULT_PRODUCT_OPTION, $value);
        $this->assertEquals($value, $instance->getDefaultProductOption());
    }

    /**
     * @return array
     */
    public function getUseAlternativeDefaultProvider()
    {
        return [
            'use alternative default option' => [1],
            'do not alternative default option' => [0]
        ];
    }

    /**
     * @param $value
     *
     * @dataProvider getUseAlternativeDefaultProvider
     */
    public function testGetUseAlternativeDefault($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ProductOptions::XPATH_USE_ALTERNATIVE_DEFAULT_OPTION, $value);
        $this->assertEquals($value, $instance->getUseAlternativeDefault());
    }

    /**
     * @return array
     */
    public function getAlternativeDefaultMinAmountProvider()
    {
        return [
            'alternative disabled' => [0, 500, 0],
            'alternative enabled, no amount value' => [1, null, null],
            'alternative enabled, zero amount value' => [1, 0, 0],
            'alternative enabled, non-zero amount value' => [1, 300, 300]
        ];
    }

    /**
     * @param $enabled
     * @param $value
     * @param $expected
     *
     * @dataProvider getAlternativeDefaultMinAmountProvider
     */
    public function testGetAlternativeDefaultMinAmount($enabled, $value, $expected)
    {
        $instance = $this->getInstance();
        $this->setXpathConsecutive(
            [[ProductOptions::XPATH_USE_ALTERNATIVE_DEFAULT_OPTION], [ProductOptions::XPATH_ALTERNATIVE_DEFAULT_MIN_AMOUNT]],
            [$enabled, $value]
        );

        $result = $instance->getAlternativeDefaultMinAmount();
        $this->assertEquals($expected, $result);
    }

    /**
     * @return array
     */
    public function getAlternativeDefaultProductOptionProvider()
    {
        return [
            'alternative disabled' => [0, '3085', false],
            'alternative enabled, no option set' => [1, null, null],
            'alternative enabled, standard shipment' => [1, '3085', '3085'],
            'alternative enabled, signature on delivery' => [1, '3189', '3189']
        ];
    }

    /**
     * @param $enabled
     * @param $value
     * @param $expected
     *
     * @dataProvider getAlternativeDefaultProductOptionProvider
     */
    public function testGetAlternativeDefaultProductOption($enabled, $value, $expected)
    {
        $instance = $this->getInstance();
        $this->setXpathConsecutive(
            [[ProductOptions::XPATH_USE_ALTERNATIVE_DEFAULT_OPTION], [ProductOptions::XPATH_ALTERNATIVE_DEFAULT_PRODUCT_OPTION]],
            [$enabled, $value]
        );

        $result = $instance->getAlternativeDefaultProductOption();
        $this->assertEquals($expected, $result);
    }

    public function EveningOptionsProvider()
    {
        return [
            'Delivery to stated address only' => ['3385'],
            'Delivery to neighbour + Return when not home' => ['3090']
        ];
    }

    /**
     * @dataProvider EveningOptionsProvider
     * @param $value
     */
    public function testGetDefaultEveningProductOption($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ProductOptions::XPATH_DEFAULT_EVENING_PRODUCT_OPTION, $value);
        $this->assertEquals($value, $instance->getDefaultEveningProductOption());
    }

    /**
     * @return array
     */
    public function getDefaultExtraAtHomeProductOptionProvider()
    {
        return [
            'Extra@Home Top service 2 person delivery NL'     => ['3628'],
            'Extra@Home Top service Btl 2 person delivery'    => ['3629'],
            'Extra@Home Top service 1 person delivery NL'     => ['3653'],
            'Extra@Home Top service Btl 1 person delivery'    => ['3783'],
            'Extra@Home Drempelservice 1 person delivery NL'  => ['3790'],
            'Extra@Home Drempelservice 2 person delivery NL'  => ['3791'],
            'Extra@Home Drempelservice Btl 1 person delivery' => ['3792'],
            'Extra@Home Drempelservice Btl 2 person delivery' => ['3793'],
        ];
    }

    /**
     * @param $value
     *
     * @dataProvider getDefaultExtraAtHomeProductOptionProvider
     */
    public function testGetDefaultExtraAtHomeProductOption($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ProductOptions::XPATH_DEFAULT_EXTRAATHOME_PRODUCT_OPTION, $value);

        $result = $instance->getDefaultExtraAtHomeProductOption();
        $this->assertEquals($value, $result);
    }

    public function pakjegemakOptionsProvider()
    {
        return [
            'Post Office + extra cover' => ['3534'],
            'Post Office + extra cover + Notification' => ['3544'],
            'Post Office + Signature on Delivery' => ['3533'],
            'Post Office + Signature on Delivery + Notification' => ['3543']
        ];
    }

    public function pakjegemakBeOptionsProvider()
    {
        return [
            'Post Office Belgium + Extra Cover' => ['4932'],
            'Post Office Belgium' => ['4936']
        ];
    }

    /**
     * @dataProvider pakjegemakOptionsProvider
     * @param $value
     */
    public function testGetDefaultPakjeGemakProductOption($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ProductOptions::XPATH_DEFAULT_PAKJEGEMAK_PRODUCT_OPTION, $value);
        $this->assertEquals($value, $instance->getDefaultPakjeGemakProductOption());
    }

    public function testGetDefaultLetterBoxPackageProductOption()
    {
        $value                    = '2928';
        $optionsConfigurationMock = $this->getFakeMock(\TIG\PostNL\Config\Source\Options\ProductOptions::class);
        $optionsConfigurationMock->setMethods(null);
        $instance = $this->getInstance(['productOptions' => $optionsConfigurationMock->getMock()]);
        $this->assertEquals($value, $instance->getDefaultLetterboxPackageProductOption());
    }

    /**
     * @dataProvider pakjegemakOptionsProvider
     * @param $value
     */
    public function testGetDefaultPakjegemakBeProductOption($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ProductOptions::XPATH_DEFAULT_PAKJEGEMAK_BE_PRODUCT_OPTION, $value);
        $this->assertEquals($value, $instance->getDefaultPakjeGemakBeProductOption());
    }

    public function sundayOptionsProvider()
    {
        return [
            'Deliver to stated address only' => ['3385'],
            'Signature on delivery + Deliver to stated address only + Return when not home' => ['3096'],
            'Signature on delivery + Delivery to stated address only' => ['3089']
        ];
    }

    /**
     * @dataProvider sundayOptionsProvider
     * @param $value
     */
    public function testGetDefaultSundayProductOption($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ProductOptions::XPATH_DEFAULT_SUNDAY_PRODUCT_OPTION, $value);
        $this->assertEquals($value, $instance->getDefaultSundayProductOption());
    }

    /**
     * @return array
     */
    public function getDefaultBeDomesticProductOptionProvider()
    {
        return [
            'BE Standard, stated address only' => ['4960'],
            'BE Standard' => ['4961'],
            'BE Standard, stated address only, signature' => ['4962'],
            'BE Standard, signature' => ['4963'],
            'BE Standard, extra cover' => ['4965'],
        ];
    }

    /**
     * @dataProvider getDefaultBeDomesticProductOptionProvider
     *
     * @param $value
     */
    public function testGetDefaultBeDomesticProductOption($value) {
        $instance = $this->getInstance();
        $this->setXpath(ProductOptions::XPATH_DEFAULT_BE_DOMESTIC_OPTION, $value);
        $this->assertEquals($value, $instance->getDefaultBeDomesticProductOption());
    }

    /**
     * @return array
     */
    public function getDefaultPakjeGemakBeDomesticProductOptionProvider()
    {
        return [
            'BE Post Offce + Extra Cover' => ['4878'],
            'BE Post Office' => ['4880'],
        ];
    }

    /**
     * @dataProvider getDefaultPakjeGemakBeDomesticProductOptionProvider
     *
     * @param $value
     */
    public function testGetDefaultPakjeGemakBeDomesticProductOption($value) {
        $instance = $this->getInstance();
        $this->setXpath(ProductOptions::XPATH_DEFAULT_PAKJEGEMAK_BE_DOMESTIC_PRODUCT_OPTION, $value);
        $this->assertEquals($value, $instance->getDefaultPakjeGemakBeDomesticProductOption());
    }
}
