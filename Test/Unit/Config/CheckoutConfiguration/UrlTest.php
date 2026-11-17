<?php

namespace TIG\PostNL\Test\Unit\Config\CheckoutConfiguration;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Config\CheckoutConfiguration\Urls;
use Magento\Framework\UrlInterface;

class UrlTest extends TestCase
{
    protected $instanceClass = Urls::class;


    public function testGetValue()
    {
        $urlBuilder = $this->getMock(UrlInterface::class);

        $builderExpects = $urlBuilder->method('getUrl');
        $builderExpects->willReturnCallback($this->consecutiveCalls([
            ['postnl/deliveryoptions/timeframes'],
            ['postnl/deliveryoptions/locations'],
            ['postnl/deliveryoptions/save'],
            ['postnl/pakjegemak/address'],
            ['postnl/address/postcode'],
            ['postnl/international/address'],
        ], [
            'timeframesurl',
            'locationsurl',
            'saveurl',
            'pakjegemakurl',
            'postcodecheckurl',
            'internationaladdress'
        ]));

        $instance = $this->getInstance([
            'urlBuilder' => $urlBuilder
        ]);

        $expected = [
            'deliveryoptions_timeframes' => 'timeframesurl',
            'deliveryoptions_locations'  => 'locationsurl',
            'deliveryoptions_save'       => 'saveurl',
            'pakjegemak_address'         => 'pakjegemakurl',
            'address_postcode'           => 'postcodecheckurl',
            'international_address'      => 'internationaladdress'
        ];

        $this->assertEquals($expected, $instance->getValue());
    }
}
