<?php

namespace TIG\PostNL\Test\Unit\Service\Order;

use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Service\Order\FeeCalculator;
use TIG\PostNL\Test\TestCase;

class FeeCalculatorTest extends TestCase
{
    public $instanceClass = FeeCalculator::class;

    public function feeCalculationProvider()
    {
        return [
            'Evening delivery with fee' => [
                'params' => ['option' => 'Evening'],
                'config options' => [
                    'isEveningDeliveryActive' => true,
                    'getEveningDeliveryFee' => 3,
                ],
                'expected' => 3.0
            ],
            'Evening delivery with fee but inactive' => [
                'params' => ['option' => 'Evening'],
                'config options' => [
                    'isEveningDeliveryActive' => false,
                    'getEveningDeliveryFee' => 3,
                ],
                'expected' => 0.0
            ],
            'Evening delivery without fee' => [
                'params' => ['option' => 'Evening'],
                'config options' => [
                    'isEveningDeliveryActive' => true,
                    'getEveningDeliveryFee' => '',
                ],
                'expected' => 0.0
            ],
            'params are missing' => [
                'params' => [],
                'config options' => [
                    'isEveningDeliveryActive' => true,
                    'getEveningDeliveryFee' => 3,
                ],
                'expected' => 0.0
            ],
        ];
    }

    /**
     * @dataProvider feeCalculationProvider
     */
    public function testFeeCalculation($params, $configOptions, $expected)
    {
        $configMock = $this->getFakeMock(ShippingOptions::class)->getMock();
        $this->addConfigOptions($configOptions, $configMock);

        /** @var FeeCalculator $instance */
        $instance = $this->getInstance([
            'shippingOptions' => $configMock
        ]);

        $result = $instance->get($params);
        $this->assertEquals($expected, $result);
        $this->assertSame($expected, $result);
    }

    private function addConfigOptions($configOptions, \PHPUnit\Framework\MockObject\MockObject $instance)
    {
        foreach ($configOptions as $method => $value) {
            $expected = $instance->method($method);
            $expected->willReturn($value);
        }
    }
}
