<?php

namespace TIG\PostNL\Test\Integration\Service\Validation;

use TIG\PostNL\Test\Integration\TestCase;
use \TIG\PostNL\Service\Validation;

class FactoryTest extends TestCase
{
    public $instanceClass = Validation\Factory::class;

    /**
     * @var Validation\Factory
     */
    public $instance;

    public function setUp() : void
    {
        parent::setUp();

        $this->instance = $this->getInstance([
            'validators' => [
                'country' => $this->getObject(Validation\Country::class),
                'region' => $this->getObject(Validation\Region::class),
            ]
        ]);
    }

    public function testCountryValidator()
    {
        $this->assertSame('NL', $this->instance->validate('country', 'NLD'));
    }

    public function testRegionValidator()
    {
        $this->assertSame(12, $this->instance->validate('region', ['country' => 'US', 'region' => 'California']));
    }
}
