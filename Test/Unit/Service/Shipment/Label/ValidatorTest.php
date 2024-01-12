<?php

namespace TIG\PostNL\Test\Unit\Service\Shipment\Label;

use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Service\Shipment\Label\Validator;
use TIG\PostNL\Test\TestCase;

class ValidatorTest extends TestCase
{
    public $instanceClass = Validator::class;

    public function validateProvider()
    {
        return [
            [
                'input' => [
                    $this->getLabelMock([]),
                    $this->getLabelMock('asdf1'),
                    $this->getLabelMock([]),
                    $this->getLabelMock('asdf2'),
                ],
                'output length' => 2
            ],
            [
                'input' => [
                    $this->getLabelMock([]),
                    $this->getLabelMock('Invalid response'),
                ],
                'output length' => 0
            ],
            [
                'input' => [
                    null,
                ],
                'output length' => 0
            ],
        ];
    }

    /**
     * @dataProvider validateProvider
     *
     * @param $input
     * @param $expected
     */
    public function testValidate($input, $expected)
    {
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
