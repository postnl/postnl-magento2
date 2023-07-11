<?php

namespace TIG\PostNL\Test\Unit\Service\Converter;

use TIG\PostNL\Test\TestCase;

class Contract extends TestCase
{
    public function testImplementsTheContract()
    {
        $implements = class_implements($this->instanceClass);

        $this->assertArrayHasKey(\TIG\PostNL\Service\Converter\ContractInterface::class, $implements);
    }
}
