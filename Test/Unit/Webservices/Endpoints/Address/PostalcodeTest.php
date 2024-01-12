<?php

namespace TIG\PostNL\Test\Unit\Webservices\Endpoints\Address;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Webservices\Endpoints\Address\Postalcode;

class PostalcodeTest extends TestCase
{
    protected $instanceClass = Postalcode::class;

    public function testGetEndpoint()
    {
        $instance = $this->getInstance();
        $this->assertEquals('postalcodecheck/', $instance->getEndpoint());
    }

    public function testGetMethod()
    {
        $instance = $this->getInstance();
        $this->assertEquals('POST', $instance->getMethod());
    }

    public function testGetVersion()
    {
        $instance = $this->getInstance();
        $this->assertEquals('v1', $instance->getVersion());
    }

    public function testTheRequestDataIsSetCorrectly()
    {
        $requestData = ['postcode' => '1014BA', 'housenumber' => '37'];
        $instance = $this->getInstance();
        $instance->updateRequestData($requestData);

        $result = $instance->getRequestData();
        $this->assertEquals($requestData, $result);
    }
}
