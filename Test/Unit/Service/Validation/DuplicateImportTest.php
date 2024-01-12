<?php

namespace TIG\PostNL\Test\Unit\Service\Validation;

use TIG\PostNL\Test\TestCase;

class DuplicateImportTest extends TestCase
{
    public $instanceClass = \TIG\PostNL\Service\Validation\DuplicateImport::class;

    public function testItShouldReturnTrueOnUniqueItems()
    {
        $instance = $this->getInstance();
        foreach (range('a', 'z') as $letter) {
            $this->assertTrue($instance->validate(['a', 'a', 'a', 'a', 'a', 'a', $letter]));
        }
    }

    public function testItShouldThrowAnExceptionOnAnInvalidLengthArray()
    {
        try {
            $this->getInstance()->validate(['not enought elements']);
        } catch (\TIG\PostNL\Exception $exception) {
            $message = 'The array to validate is expected to have 7 elements, you only have 1';
            $this->assertEquals($message, $exception->getMessage());
            return;
        }

        $this->fail('We expected an exception to be thrown, but we got none');
    }

    public function testTheReturnValueForDuplicateImports()
    {
        $instance = $this->getInstance();
        $elements = ['a row', 'with', 'enough', 'elements', 'with a', 'length of', '7'];

        $result = $instance->validate($elements);
        $this->assertTrue($result);

        $result = $instance->validate($elements);
        $this->assertFalse($result);
    }
}
