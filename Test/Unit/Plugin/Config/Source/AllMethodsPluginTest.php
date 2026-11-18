<?php

namespace TIG\PostNL\Test\Unit\Plugin\Config\Source;

use TIG\PostNL\Plugin\Config\Source\AllMethodsPlugin;
use Magento\Shipping\Model\Config\Source\Allmethods\Interceptor;
use TIG\PostNL\Test\TestCase;

class AllMethodsPluginTest extends TestCase
{
    public $instanceClass = AllMethodsPlugin::class;

    public static function dataProvider()
    {
        $given = [
            'flatrate' => [
                'label' => 'Flate Rate',
                'value' => [
                    [
                        'value' => 'flatrate_flatrate',
                        'label' => '[flatrate] Fixed'
                    ]
                ]
            ],
            'tig_postnl' => [
                'label' => 'Verzenden via PostNL',
                'value' => [
                    [
                        'value' => 'tig_postnl_tig_postnl',
                        'label' => '[tig_postnl] PostNL'
                    ]
                ]
            ]
        ];

        $expected = [
            'flatrate' => [
                'label' => 'Flate Rate',
                'value' => [
                    [
                        'value' => 'flatrate_flatrate',
                        'label' => '[flatrate] Fixed'
                    ]
                ]
            ],
            'tig_postnl' => [
                'label' => 'Verzenden via PostNL',
                'value' => [
                    [
                        'value' => 'tig_postnl_regular',
                        'label' => '[tig_postnl] Regular'
                    ]
                ]
            ]
        ];

        return array_map('array_values', [
            'Set tig_postnl_tig_postnl to tig_postnl_regular' => [$given, $expected]
        ]);
    }

    /**
     * @param $given
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('dataProvider')]
    public function testAfterToOptionArray($given, $expected)
    {
        $interceptor = $this->getFakeMock(Interceptor::class, true);
        $instance    = $this->getInstance();
        $result      = $instance->afterToOptionArray($interceptor, $given);

        $this->assertEquals($expected, $result);
    }
}
