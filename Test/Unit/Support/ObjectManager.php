<?php

namespace TIG\PostNL\Test\Unit\Support;

class ObjectManager extends \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
{
    /**
     * Create a stub for a dependency without interaction expectations.
     *
     * @param string $className
     * @return \PHPUnit\Framework\MockObject\Stub
     */
    protected function _getMockWithoutConstructorCall($className)
    {
        $method = new \ReflectionMethod(\PHPUnit\Framework\TestCase::class, 'createStub');

        return $method->invoke($this->_testObject, $className);
    }
}
