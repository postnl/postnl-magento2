<?php

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
