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

    public function pakjegemakEarlyOptionsProvider()
    {
        return [
            'Post Office + extra cover + Notification' => ['3544'],
            'Post Office + Signature on Delivery + Notification' => ['3543']
        ];
    }

    /**
     * @dataProvider pakjegemakEarlyOptionsProvider
     * @param $value
     */
    public function testGetDefaultPakjeGemakEarlyProductOption($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(ProductOptions::XPATH_DEFAULT_PAKJEGEMAK_EARLY_PRODUCT_OPTION, $value);
        $this->assertEquals($value, $instance->getDefaultPakjeGemakEarlyProductOption());
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
}
