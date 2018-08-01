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

use TIG\PostNL\Config\Provider\PackingslipBarcode;

class PackingslipBarcodeTest extends AbstractConfigurationTest
{
    protected $instanceClass = PackingslipBarcode::class;

    /**
     * @param $value
     *
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::enabledAndDisabled
     */
    public function testIsEnabled($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(PackingslipBarcode::XPATH_BARCODE_ENABLED, $value);

        $this->assertEquals($value, $instance->isEnabled());
    }

    /**
     * @param $value
     *
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::randomWordsProvider
     */
    public function testGetValue($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(PackingslipBarcode::XPATH_BARCODE_VALUE, $value);

        $this->assertEquals($value, $instance->getValue());
    }

    public function positionDataProvider()
    {
        return [
            'correct config parsed' => ['250,250,250,250', [250,250,250,250]],
            'incorrect config parsed' => ['24,24,24', [360, 750, 550, 790]]
        ];
    }

    /**
     * @param $value
     * @param $expected
     *
     * @dataProvider positionDataProvider
     */
    public function testGetPosition($value, $expected)
    {
        $instance = $this->getInstance();
        $this->setXpath(PackingslipBarcode::XPATH_BARCODE_POSITION, $value);

        $this->assertEquals($expected, $instance->getPosition());
    }

    /**
     * @param $value
     *
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::randomWordsProvider
     */
    public function testGetType($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(PackingslipBarcode::XPATH_BARCODE_TYPE, $value);

        $this->assertEquals($value, $instance->getType());
    }

    public function colorProvider()
    {
        return [
            ['#cecece', '#CECECE']
        ];
    }

    /**
     * @param $value
     * @param $expected
     *
     * @dataProvider colorProvider
     */
    public function testGetBackgroundColor($value, $expected)
    {
        $instance = $this->getInstance();
        $this->setXpath(PackingslipBarcode::XPATH_BARCODE_BACKGROUND, $value);

        $this->assertEquals($expected, $instance->getBackgroundColor());
    }

    /**
     * @param $value
     * @param $expected
     *
     * @dataProvider colorProvider
     */
    public function testGetFontColor($value, $expected)
    {
        $instance = $this->getInstance();
        $this->setXpath(PackingslipBarcode::XPATH_BARCODE_COLOR, $value);

        $this->assertEquals($expected, $instance->getFontColor());
    }

    /**
     * @param $value
     *
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::enabledAndDisabled
     */
    public function testIncludeNumber($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(PackingslipBarcode::XPATH_BARCODE_INCLUDE_NUMBER, $value);

        $this->assertEquals($value, $instance->includeNumber());
    }
}
