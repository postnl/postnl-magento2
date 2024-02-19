<?php

namespace TIG\PostNL\Test\Unit\Helper;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Helper\AddressEnhancer;

class AddressEnhancerTest extends TestCase
{
    protected $instanceClass = AddressEnhancer::class;

    public function addressProvider()
    {
        return [
            'regular address' => [
                ['Kabelweg 37', ''],
                ['housenumber' => '37', 'housenumberExtension' => '']
            ],
            'address with extension' => [
                ['Kabelweg 37B', ''],
                ['housenumber' => '37', 'housenumberExtension' => 'B']
            ],
            'address with extension and space' => [
                ['Kabelweg 37 B', ''],
                ['housenumber' => '37', 'housenumberExtension' => 'B']
            ],
            'housenumber on second line' => [
                ['Kabelweg', '37'],
                ['housenumber' => '37', 'housenumberExtension' => '']
            ],
            'housenumber on second line with extension' => [
                ['Kabelweg', '37B'],
                ['housenumber' => '37', 'housenumberExtension' => 'B']
            ],
            'housenumber on second line with extension and space' => [
                ['Kabelweg', '37 B'],
                ['housenumber' => '37', 'housenumberExtension' => 'B']
            ],
            'Streetname with spaces' => [
                ['Clare Lennarthof 7', ''],
                ['housenumber' => '7', 'housenumberExtension' => '']
            ],
            'Streetname with spaces on second line' => [
                ['Clare Lennarthof ', '7'],
                ['housenumber' => '7', 'housenumberExtension' => '']
            ],
            'Streetname with spaces and extension' => [
                ['Clare Lennarthof 7B', ''],
                ['housenumber' => '7', 'housenumberExtension' => 'B']
            ],
            'Streetname with spaces and extension and extra space' => [
                ['Clare Lennarthof 7 B', ''],
                ['housenumber' => '7', 'housenumberExtension' => 'B']
            ],
            'Streetname with numbers' => [
                ['7 Januaristraat 18', ''],
                ['housenumber' => '18', 'housenumberExtension' => '']
            ],
            'Streetname with three spaces' => [
                ['De La Reylaan 43', ''],
                ['housenumber' => '43', 'housenumberExtension' => '']
            ],
            'Streetname with three spaces and number on second street field' => [
                ['De La Reylaan', '43'],
                ['housenumber' => '43', 'housenumberExtension' => '']
            ],
            'Streetname with addition in second street field' => [
                ['TestStraat 67', ' -1 hoog'],
                ['housenumber' => '67', 'housenumberExtension' => '-1 hoog']
            ],
            'Streetname parsed with three values in street fields' => [
                ['TestStraat', '67', ' -1 hoog'],
                ['housenumber' => '67', 'housenumberExtension' => '-1 hoog']
            ],
            'Streetname with acute apostrophe' => [
                ["matena'spad 58", ''],
                ['housenumber' => '58', 'housenumberExtension' => '']
            ],
            'Streetname with grave apostrophe' => [
                ['matena`spad 58', ''],
                ['housenumber' => '58', 'housenumberExtension' => '']
            ],
            'Streetname with quote apostrophe' => [
                ['matena‘spad 58', ''],
                ['housenumber' => '58', 'housenumberExtension' => '']
            ]
        ];
    }

    /**
     * @param $street
     * @param $expected
     *
     * @dataProvider addressProvider
     */
    public function testGet($street, $expected)
    {
        $instance = $this->getInstance();
        $instance->set([
            'postcode' => '1014AB',
            'country'  => 'NL',
            'street'   => $street
        ]);

        $result = $instance->get();

        $this->assertEquals($expected['housenumber'], $result['housenumber']);
        $this->assertEquals($expected['housenumberExtension'], $result['housenumberExtension']);
    }
}
