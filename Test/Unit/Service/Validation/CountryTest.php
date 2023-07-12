<?php

namespace TIG\PostNL\Test\Unit\Service\Validation;

class CountryTest extends Contract
{
    public $instanceClass = \TIG\PostNL\Service\Validation\Country::class;

    public function testCountryStarShouldDefaultToZero()
    {
        $this->assertSame(0, $this->getInstance()->validate('*'));
    }
}
