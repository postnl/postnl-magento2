<?php

namespace TIG\PostNL\Test\Unit\Config\Source\Options;

use TIG\PostNL\Config\Source\Options\StockOptions;

class StockOptionsTest extends \TIG\PostNL\Test\TestCase
{
    public $instanceClass = StockOptions::class;

    public function returnsTheCorrectOptionsProvider()
    {
        return [
            ['value' => 'in_stock'],
            ['value' => 'backordered'],
        ];
    }

    /**
     * @dataProvider returnsTheCorrectOptionsProvider
     */
    public function testReturnsTheCorrectOptions($value)
    {
        /** @var StockOptions $instance */
        $instance = $this->getInstance();

        $result = $instance->toOptionArray();

        $hasValue = false;
        foreach ($result as $option) {
            if ($option['value'] == $value) {
                $hasValue = true;
            }
        }

        $this->assertTrue($hasValue, '$result should contains ["value" => "' . $value . '"]');
    }
}
