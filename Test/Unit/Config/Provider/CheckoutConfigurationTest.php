<?php

namespace TIG\PostNL\Test\Unit\Config\Provider;

use TIG\PostNL\Config\CheckoutConfiguration\CheckoutConfigurationInterface;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\Provider\CheckoutConfiguration;

class CheckoutConfigurationTest extends TestCase
{
    public $instanceClass = CheckoutConfiguration::class;

    public function getConfigProvider()
    {
        return [
            [
                'input' => [
                    'key1' => 'test1',
                    'else' => 'random',
                ],
                'expected' => [
                    'shipping' => [
                        'postnl' => [
                            'key1' => 'test1',
                            'else' => 'random',
                        ]
                    ]
                ]
            ],

            [
                'input' => [
                    'key1' => 'output1',
                    'key2' => 'output2',
                    'key3' => 'output3',
                ],
                'expected' => [
                    'shipping' => [
                        'postnl' => [
                            'key1' => 'output1',
                            'key2' => 'output2',
                            'key3' => 'output3',
                        ]
                    ]
                ]
            ],
        ];
    }

    /**
     * @param $shippingConfigurationInput
     * @param $expected
     *
     * @dataProvider getConfigProvider
     */
    public function testGetConfig($shippingConfigurationInput, $expected)
    {
        $shippingConfiguration = [];
        foreach ($shippingConfigurationInput as $key => $value) {
            $mock = $this->getMock(CheckoutConfigurationInterface::class);
            $mock->expects($this->once())->method('getValue')->willReturn($value);

            $shippingConfiguration[$key] = $mock;
        }

        /** @var CheckoutConfiguration $instance */
        $instance = $this->getInstance([
            'shippingConfiguration' => $shippingConfiguration,
        ]);

        $result = $instance->getConfig();

        $this->assertEquals($expected, $result);
    }
}
