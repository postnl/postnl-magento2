<?php

namespace PostNL\Test\Unit\Service\Shipment\Label\Type;

use TIG\PostNL\Service\Shipment\Label\Type\GlobalPack;
use TIG\PostNL\Test\TestCase;

class GlobalPackTest extends TestCase
{
    /** @var GlobalPack $instanceClass */
    public $instanceClass = GlobalPack::class;
    
    /**
     * @throws \Exception
     */
    public function testIsExcludedCountry()
    {
        /** @var GlobalPack $instance */
        $instance = $this->getInstance();
        $result   = $instance->isExcludedCountry("US");
        $expected = true;
        
        $this->assertEquals($expected, $result);
    }
}
