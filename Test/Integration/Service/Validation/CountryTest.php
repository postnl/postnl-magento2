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

namespace TIG\PostNL\Test\Integration\Service\Validation;

use TIG\PostNL\Test\Integration\TestCase;

class CountryTest extends TestCase
{
    public $instanceClass = \TIG\PostNL\Service\Validation\Country::class;

    public function testInvalidCountry()
    {
        $instance = $this->getInstance();

        $this->assertFalse($instance->validate('AA'));
    }

    public function testReturnsIso2WhenPassedIso3()
    {
        $instance = $this->getInstance();

        $this->assertEquals('NL', $instance->validate('NLD'));
        $this->assertEquals('DE', $instance->validate('DEU'));
    }

    public function testAllowsMultipleCountriesInTheSameField()
    {
        $instance = $this->getInstance();

        $this->assertEquals('NL,FR', $instance->validate('NL,FR'));
        $this->assertEquals('NL,FR', $instance->validate('NLD,FRA'));
        $this->assertEquals('NL,DE,BE,FR', $instance->validate('NL,DEU,BE,FRA'));
        $this->assertEquals('NL,DE,BE,FR', $instance->validate('NLD,DEU,BEL,FRA'));
    }

    public function testReturnsFalseWhenProvidedMultipleCountriesAndOneIsIncorrect()
    {
        $instance = $this->getInstance();

        $this->assertFalse($instance->validate('NLD,AA'));
        $this->assertFalse($instance->validate('NLD,AAA,BEL,BBB'));
    }
}
