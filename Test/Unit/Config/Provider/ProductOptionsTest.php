<?php

namespace TIG\PostNL\Test\Unit\Config\Provider;

use TIG\PostNL\Config\Provider\ProductOptions;

class ProductOptionsTest extends AbstractConfiguration
{
    protected $instanceClass = ProductOptions::class;

    public static function defaultProductOptionsProvider()
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
    #[\PHPUnit\Framework\Attributes\DataProvider('defaultProductOptionsProvider')]
    public function testGetDefaultProductOption($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ProductOptions::XPATH_DEFAULT_PRODUCT_OPTION, $value);
        $this->assertEquals($value, $instance->getDefaultProductOption());
    }

    /**
     * @return array
     */
    public static function getUseAlternativeDefaultProvider()
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
    #[\PHPUnit\Framework\Attributes\DataProvider('getUseAlternativeDefaultProvider')]
    public function testGetUseAlternativeDefault($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ProductOptions::XPATH_USE_ALTERNATIVE_DEFAULT_OPTION, $value);
        $this->assertEquals($value, $instance->getUseAlternativeDefault());
    }

    public static function EveningOptionsProvider()
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
    #[\PHPUnit\Framework\Attributes\DataProvider('EveningOptionsProvider')]
    public function testGetDefaultEveningProductOption($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ProductOptions::XPATH_DEFAULT_EVENING_PRODUCT_OPTION, $value);
        $this->assertEquals($value, $instance->getDefaultEveningProductOption());
    }

    /**
     * @return array
     */
    public static function getDefaultExtraAtHomeProductOptionProvider()
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
    #[\PHPUnit\Framework\Attributes\DataProvider('getDefaultExtraAtHomeProductOptionProvider')]
    public function testGetDefaultExtraAtHomeProductOption($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ProductOptions::XPATH_DEFAULT_EXTRAATHOME_PRODUCT_OPTION, $value);

        $result = $instance->getDefaultExtraAtHomeProductOption();
        $this->assertEquals($value, $result);
    }

    public static function pakjegemakOptionsProvider()
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
    #[\PHPUnit\Framework\Attributes\DataProvider('pakjegemakOptionsProvider')]
    public function testGetDefaultPakjeGemakProductOption($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ProductOptions::XPATH_DEFAULT_PAKJEGEMAK_PRODUCT_OPTION, $value);
        $this->assertEquals($value, $instance->getDefaultPakjeGemakProductOption());
    }

    public function testGetDefaultLetterBoxPackageProductOption()
    {
        $value                    = '2928';
        $optionsConfiguration = $this->getObject(\TIG\PostNL\Config\Source\Options\ProductOptions::class);
        $instance = $this->getInstance(['productOptions' => $optionsConfiguration]);
        $this->setXpath(ProductOptions::XPATH_DEFAULT_LETTERBOX_PACKAGE_PRODUCT, '');
        $this->assertEquals($value, $instance->getDefaultLetterboxPackageProductOption());
    }

    /**
     * @dataProvider pakjegemakOptionsProvider
     * @param $value
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('pakjegemakOptionsProvider')]
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
     * @return array
     */
    public static function getDefaultBeDomesticProductOptionProvider()
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
    #[\PHPUnit\Framework\Attributes\DataProvider('getDefaultBeDomesticProductOptionProvider')]
    public function testGetDefaultBeDomesticProductOption($value) {
        $instance = $this->getInstance();
        $this->setXpath(ProductOptions::XPATH_DEFAULT_BE_DOMESTIC_OPTION, $value);
        $this->assertEquals($value, $instance->getDefaultBeDomesticProductOption());
    }

    /**
     * @return array
     */
    public static function getDefaultPakjeGemakBeDomesticProductOptionProvider()
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
    #[\PHPUnit\Framework\Attributes\DataProvider('getDefaultPakjeGemakBeDomesticProductOptionProvider')]
    public function testGetDefaultPakjeGemakBeDomesticProductOption($value) {
        $instance = $this->getInstance();
        $this->setXpath(ProductOptions::XPATH_DEFAULT_PAKJEGEMAK_BE_DOMESTIC_PRODUCT_OPTION, $value);
        $this->assertEquals($value, $instance->getDefaultPakjeGemakBeDomesticProductOption());
    }
}
