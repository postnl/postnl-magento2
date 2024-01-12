<?php

namespace TIG\PostNL\Test\Unit\Service\Converter;

use TIG\PostNL\Service\Converter\ZeroToStar;

class ZeroToStarTest extends Contract
{
    public $instanceClass = ZeroToStar::class;

    public function testThatZeroIsConvertedToStar()
    {
        $this->assertEquals('*', $this->getInstance()->convert(0));
        $this->assertEquals('*', $this->getInstance()->convert('0'));
    }

    public function testThatNonZeroIsReturnedWithoutModification()
    {
        $this->assertEquals('other value', $this->getInstance()->convert('other value'));
    }
}
