<?php

namespace TIG\PostNL\Test\Unit\Service\Validation;

class DecimalTest extends Contract
{
    public $instanceClass = \TIG\PostNL\Service\Validation\Decimal::class;

    public function testInvalidDecimal()
    {
        $this->assertFalse($this->getInstance()->validate('string'));
    }

    public function testNegativeNumber()
    {
        $this->assertFalse($this->getInstance()->validate('-100'));
    }

    public function testValidNumber()
    {
        $this->assertEquals(1, $this->getInstance()->validate('1'));
    }
}
