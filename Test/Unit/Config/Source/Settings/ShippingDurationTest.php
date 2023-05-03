<?php

namespace TIG\PostNL\Test\Unit\Config\Source\Settings;

use TIG\PostNL\Config\Source\Settings\ShippingDuration;
use TIG\PostNL\Config\Provider\ShippingDuration as SourceProvider;
use TIG\PostNL\Test\TestCase;

class ShippingDurationTest extends TestCase
{
    protected $instanceClass = ShippingDuration::class;

    public function testToOptionArray()
    {
        $optionsFromProvider = [
            [
                'label' => '1 Day',
                'value' => 1
            ],
            [
                'label' => 'Use configuration value',
                'value' => SourceProvider::CONFIGURATION_VALUE
            ]
        ];

        $sourceProviderMock = $this->getFakeMock(SourceProvider::class)->getMock();
        $providerExpects = $sourceProviderMock->expects($this->once())->method('getAllOptions');
        $providerExpects->willReturn($optionsFromProvider);

        $options = $this->getInstance([
            'shippingDuration' => $sourceProviderMock
        ])->toOptionArray();

        $this->assertEquals(1, $options[0]['value']);
        $this->assertCount(1, $options);
    }
}
