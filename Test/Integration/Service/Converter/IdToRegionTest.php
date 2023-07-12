<?php

namespace TIG\PostNL\Test\Integration\Service\Converter;

class IdToRegionTest extends Contract
{
    public $instanceClass = \TIG\PostNL\Service\Converter\IdToRegion::class;

    public function testItConvertsZeroToStar()
    {
        $this->assertEquals('*', $this->getInstance()->convert(0));
    }

    public function testItConvertsAnIdToAName()
    {
        $this->assertEquals('California', $this->getInstance()->convert(12));
        $this->assertEquals('Colorado', $this->getInstance()->convert(13));
    }

    public function testThrowsAnExceptionWhenTheRegionIsInvalid()
    {
        try {
            $this->getInstance()->convert('non existing');
        } catch (\TIG\PostNL\Exception $exception) {
            $this->assertEquals('"non existing" is not a valid region', $exception->getMessage());
            return;
        }

        $this->fail('We expected an \TIG\PostNL\Exception but got none');
    }
}
