<?php

namespace TIG\PostNL\Test\Unit\Service\Validation;

use TIG\PostNL\Service\Validation\ContractInterface as ContractInterface;
use TIG\PostNL\Test\TestCase;

class Contract extends TestCase
{
    public function testClassImplementsTheContract()
    {
        $this->assertArrayHasKey(ContractInterface::class, class_implements($this->instanceClass));
    }
}
