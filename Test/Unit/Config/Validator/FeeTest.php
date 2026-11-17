<?php

namespace TIG\PostNL\Test\Unit\Config\Validator;

use TIG\PostNL\Config\Validator\Fee;
use TIG\PostNL\Test\TestCase;

class FeeTest extends TestCase
{
    public $instanceClass = Fee::class;

    public static function theConfigInputIsProcessedOkProvider()
    {
        return [
            ['2,5', '2.5'],
            ['2,0', '2.0'],
            [' ', ''],
        ];
    }

    /**
     * @dataProvider theConfigInputIsProcessedOkProvider
     *
     * @param $input
     * @param $output
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('theConfigInputIsProcessedOkProvider')]
    public function testTheConfigInputIsProcessedOk($input, $output)
    {
        /** @var Fee $instance */
        $instance = $this->getInstance();
        $instance->setValue($input);
        $instance->beforeSave();

        $this->assertEquals($output, $instance->getValue());
    }
}
