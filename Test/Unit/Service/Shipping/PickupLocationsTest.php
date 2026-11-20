<?php

namespace TIG\PostNL\Test\Unit\Service\Shipping;

use Magento\Framework\App\CacheInterface;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Helper\AddressEnhancer;
use TIG\PostNL\Service\Shipping\DeliveryDate;
use TIG\PostNL\Service\Shipping\PickupLocations;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Webservices\Endpoints\Locations as LocationsEndpoint;

class PickupLocationsTest extends TestCase
{
    protected $instanceClass = PickupLocations::class;

    public function testGetAcceptsStreetWithHouseNumberSubmittedAsString()
    {
        $this->assertLocationsReturned(['street' => 'Teststraat 1']);
    }

    public function testGetAcceptsStreetSubmittedAsStringWithSeparateHouseNumber()
    {
        $this->assertLocationsReturned(['street' => 'Teststraat', 'housenumber' => '1']);
    }

    private function assertLocationsReturned(array $address)
    {
        $webshop = $this->createMock(Webshop::class);
        $webshop->method('getIsAddressCheckEnabled')->willReturn(true);
        $enhancer = new AddressEnhancer($webshop);
        $enhancer->set(['country' => 'NL', 'postcode' => '1234AB'] + $address);

        $locations = [(object)['LocationCode' => '1']];
        $response = (object)['GetLocationsResult' => (object)['ResponseLocation' => $locations]];

        $endpoint = $this->createMock(LocationsEndpoint::class);
        $endpoint->method('call')->willReturn($response);
        $cache = $this->createMock(CacheInterface::class);
        $cache->method('load')->willReturn(false);
        $deliveryDate = $this->createMock(DeliveryDate::class);
        $deliveryDate->method('get')->willReturn(null);

        $instance = new PickupLocations($deliveryDate, $endpoint, $cache);

        $this->assertSame($locations, $instance->get($enhancer->get()));
    }
}
