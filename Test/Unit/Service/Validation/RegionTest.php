<?php

namespace TIG\PostNL\Test\Unit\Service\Validation;

class RegionTest extends Contract
{
    public $instanceClass = \TIG\PostNL\Service\Validation\Region::class;

    public function testRegionStarShouldDefaultToZero()
    {
        $this->assertSame(0, $this->getInstance()->validate(['country' => 'NL', 'region' => '*']));
    }
}
