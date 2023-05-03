<?php

namespace TIG\PostNL\Test\Unit\Plugin\Quote\Address;

use TIG\PostNL\Plugin\Quote\Address\RateRequestPlugin;
use TIG\PostNL\Test\TestCase;

class RateRequestPluginTest extends TestCase
{
    public $instanceClass = RateRequestPlugin::class;

    public function dataProvider()
    {
        return [
            'TIG PostNL Limit Carrier' => [
                'limit_carrier',
                'tig',
                ['limit_carrier','tig_postnl']
            ],
            'FlatRate Limit Carrier' => [
                'limit_carrier',
                'flatrate',
                ['limit_carrier','flatrate']
            ],
        ];
    }

    /**
     * @param $key
     * @param $value
     * @param $expected
     *
     * @dataProvider dataProvider
     */
    public function testBeforeSetData($key, $value, $expected)
    {
        $instance = $this->getInstance();
        $result = $instance->beforeSetData(null, $key, $value);

        $this->assertEquals($expected, $result);
    }
}