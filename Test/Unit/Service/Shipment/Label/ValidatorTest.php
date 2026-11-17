<?php

namespace TIG\PostNL\Test\Unit\Service\Shipment\Label;

use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Service\Shipment\Label\Validator;
use TIG\PostNL\Test\TestCase;

class ValidatorTest extends TestCase
{
    public $instanceClass = Validator::class;

    public static function validateProvider()
    {
        return array_map('array_values', [
            [
                'input' => [
                    [],
                    'asdf1',
                    [],
                    'asdf2',
                ],
                'expected' => 2
            ],
            [
                'input' => [
                    [],
                    'Invalid response',
                ],
                'expected' => 0
            ],
            [
                'input' => [
                    null,
                ],
                'expected' => 0
            ],
        ]);
    }

    /**
     * @dataProvider validateProvider
     *
     * @param $input
     * @param $expected
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('validateProvider')]
    public function testValidate($input, $expected)
    {
        $input = array_map(function ($label) {
            return $label === null ? null : $this->getLabelMock($label);
        }, $input);
        /** @var Validator $instance */
        $instance = $this->getInstance();
        $result = $instance->validate($input);

        $this->assertCount($expected, $result);
    }

    private function getLabelMock($label)
    {
        $labelMock = $this->getMock(ShipmentLabelInterface::class);
        $this->mockFunction($labelMock, 'getLabel', $label);

        return $labelMock;
    }
}
