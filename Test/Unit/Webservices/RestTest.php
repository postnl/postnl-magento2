<?php

namespace TIG\PostNL\Test\Unit\Webservices;

use Laminas\Http\Client as HttpClient;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Webservices\Rest;
use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\DefaultConfiguration;
use TIG\PostNL\Webservices\Endpoints\Address\Postalcode;
use TIG\PostNL\Service\Handler\PostcodecheckHandler;

class RestTest extends TestCase
{
    protected $instanceClass = Rest::class;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $httpClient;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $accountConfiguration;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $defaultConfiguration;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $handler;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $endpoint;

    protected function setUp() : void
    {
        parent::setup();

        $this->httpClient = $this->getMock(HttpClient::class);
        $this->accountConfiguration = $this->getFakeMock(AccountConfiguration::class, true);
        $this->defaultConfiguration = $this->getFakeMock(DefaultConfiguration::class, true);
        $this->handler = $this->getFakeMock(PostcodecheckHandler::class, true);
        $this->endpoint = $this->getFakeMock(Postalcode::class)->disableOriginalConstructor()->getMock();

    }

    /**
     * @param array $args
     *
     * @return object
     */
    public function getInstance(array $args = [])
    {
        return parent::getInstance($args + [
                'httpClient' => $this->httpClient,
                'accountConfiguration' => $this->accountConfiguration,
                'defaultConfiguration' => $this->defaultConfiguration,
                'postcodecheckHandler' => $this->handler
            ]
        );
    }

    public function testRestInstance()
    {
        $resetZendParams = $this->httpClient->expects($this->once());
        $resetZendParams->method('resetParameters');

        $this->defaultConfiguration->method('getModusApiUrl')->willReturn('https://api.postnl.nl/');

        $uri = 'https://api.postnl.nl/shipment/checkout/';
        $endpointGetEndpoint = $this->endpoint->expects($this->once());
        $endpointGetEndpoint->method('getEndpoint')->willReturn('postalcode/');

        $endpointGetVersion = $this->endpoint->expects($this->once());
        $endpointGetVersion->method('getVersion')->willReturn('v1');

        $endpointGetResource = $this->endpoint->expects($this->once());
        $endpointGetResource->method('getResource')->willReturn('shipment/checkout/');

        $zendClientUri = $this->httpClient->expects($this->once());
        $zendClientUri->method('setUri')->with($uri.'v1/postalcode/');

        $apiKey = '123243235235';
        $accountApiKey = $this->accountConfiguration->expects($this->any());
        $accountApiKey->method('getApiKey')->willReturn($apiKey);

        $zendClientHeaders = $this->httpClient->expects($this->once());
        $zendClientHeaders->method('setHeaders')->with(['apikey: ' .$apiKey, 'SourceSystem: 66', 'Content-Type: application/json']);

        $requestData = ['postcode' => '1014BA', 'housenumber' => '37'];
        $this->endpoint->expects($this->any())->method('getRequestData')->willReturn($requestData);

        $endpointMethod = $this->endpoint->expects($this->exactly(3));
        $endpointMethod->method('getMethod')->willReturn(\Laminas\Http\Request::METHOD_GET);

        $zendClientSetMethod = $this->httpClient->expects($this->once());
        $zendClientSetMethod->method('setMethod')->with(\Laminas\Http\Request::METHOD_GET);

        $zendClientParams = $this->httpClient->expects($this->once());
        $zendClientParams->method('setParameterGet')->with($requestData);

        $fakeResponse = new \Laminas\Http\Response();
        $fakeResponse->setStatusCode(200)
            ->setContent('{}');

        $zendRequest = $this->httpClient->expects($this->once());
        $zendRequest->method('send')->willReturn($fakeResponse);

        $this->getInstance()->getRequest($this->endpoint);
    }
}
