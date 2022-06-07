<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Test\Unit\Webservices;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Webservices\Rest;

use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\DefaultConfiguration;
use Magento\Framework\HTTP\ZendClient as ZendClient;
use TIG\PostNL\Webservices\Endpoints\Address\Postalcode;
use TIG\PostNL\Service\Handler\PostcodecheckHandler;

class RestTest extends TestCase
{
    protected $instanceClass = Rest::class;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $zendClient;

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

    public function setUp() : void
    {
        parent::setup();

        $this->zendClient = $this->getMock(ZendClient::class);
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
                'zendClient' => $this->zendClient,
                'accountConfiguration' => $this->accountConfiguration,
                'defaultConfiguration' => $this->defaultConfiguration,
                'postcodecheckHandler' => $this->handler
            ]
        );
    }

    public function testRestInstance()
    {
        $resetZendParams = $this->zendClient->expects($this->once());
        $resetZendParams->method('resetParameters');

        $uri = 'https://api.postnl.nl/address/national/basic/';
        $defaultConfig = $this->defaultConfiguration->expects($this->once());
        $defaultConfig->method('getModusAddressApiUrl')->willReturn($uri);

        $endpointGetEndpoint = $this->endpoint->expects($this->once());
        $endpointGetEndpoint->method('getEndpoint')->willReturn('postalcode/');

        $endpointGetVersion = $this->endpoint->expects($this->once());
        $endpointGetVersion->method('getVersion')->willReturn('v1');

        $zendClientUri = $this->zendClient->expects($this->once());
        $zendClientUri->method('setUri')->with($uri.'v1/postalcode/');

        $apiKey = '123243235235';
        $accountApiKey = $this->accountConfiguration->expects($this->any());
        $accountApiKey->method('getApiKey')->willReturn($apiKey);

        $zendClientHeaders = $this->zendClient->expects($this->once());
        $zendClientHeaders->method('setHeaders')->with(['apikey' => $apiKey]);

        $requestData = ['postcode' => '1014BA', 'housenumber' => '37'];
        $this->endpoint->expects($this->any())->method('getRequestData')->willReturn($requestData);

        $endpointMethod = $this->endpoint->expects($this->exactly(3));
        $endpointMethod->method('getMethod')->willReturn(ZendClient::GET);

        $zendClientSetMethod = $this->zendClient->expects($this->once());
        $zendClientSetMethod->method('setMethod')->with(ZendClient::GET);

        $zendClientParams = $this->zendClient->expects($this->once());
        $zendClientParams->method('setParameterGet')->with($requestData);

        $fakeResponse = new \Zend_Http_Response('200', array(), '{}');

        $zendRequest = $this->zendClient->expects($this->once());
        $zendRequest->method('request')->willReturn($fakeResponse);

        $this->getInstance()->getRequest($this->endpoint);
    }
}
