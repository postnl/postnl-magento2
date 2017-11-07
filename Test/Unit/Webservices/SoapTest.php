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
use TIG\PostNL\Webservices\Soap;

class SoapTest extends TestCase
{
    public $instanceClass = Soap::class;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\TIG\PostNL\Webservices\AbstractEndpoint
     */
    private $endpointMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $soapClient;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $accountConfiguration;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $defaultConfiguration;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $exceptionHandler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $log;

    public function setUp()
    {
        $this->soapClient = $this->getMock(\Zend\Soap\Client::class);
        $this->endpointMock = $this->getMock(\TIG\PostNL\Webservices\AbstractEndpoint::class);
        $this->accountConfiguration = $this->getFakeMock(\TIG\PostNL\Config\Provider\AccountConfiguration::class, true);
        $this->defaultConfiguration = $this->getFakeMock(\TIG\PostNL\Config\Provider\DefaultConfiguration::class, true);
        $this->exceptionHandler = $this->getFakeMock(\TIG\PostNL\Webservices\ExceptionHandler::class, true);
        $this->log = $this->getFakeMock(\TIG\PostNL\Webservices\Api\Log::class, true);

        $getModusApiBaseUrl = $this->defaultConfiguration->method('getModusApiBaseUrl');
        $getModusApiBaseUrl->willReturn('https://api.fakelocation.com');

        parent::setUp();
    }

    /**
     * @param array $args
     *
     * @return Soap
     */
    public function getInstance(array $args = [])
    {
        return parent::getInstance($args + [
            'soapClient' => $this->soapClient,
            'accountConfiguration' => $this->accountConfiguration,
            'defaultConfiguration' => $this->defaultConfiguration,
            'exceptionHandler' => $this->exceptionHandler,
            'log' => $this->log,
        ]);
    }

    public function testTheCorrectUrlIsUsed()
    {
        $location = 'fakelocation';

        $setWSDL = $this->soapClient->expects($this->once());
        $setWSDL->method('setWSDL');
        // This is the main assertion
        $setWSDL->with('https://api.fakelocation.com/' . $location . '/soap.wsdl');

        $getLocation = $this->endpointMock->expects($this->once());
        $getLocation->method('getLocation');
        $getLocation->willReturn('/fakelocation');

        $this->useApiKey();

        $this->getInstance()->call($this->endpointMock, 'fakemethod', []);
    }

    public function testItThrowsAnExceptionWhenNoApiKeyIsEntered()
    {
        try {
            $this->getInstance()->call($this->endpointMock, 'fakemethod', []);
        } catch (\Magento\Framework\Exception\LocalizedException $exception) {
            $this->assertEquals($exception->getMessage(), 'Please enter your API key');
            return;
        }

        $this->fail('We expected an exception but got none');
    }

    public function testSoapExceptionsAreHandled()
    {
        $this->useApiKey();

        $this->mockExceptionHandler();

        try {
            $this->getInstance()->call($this->endpointMock, 'fakemethod', []);
        } catch (\SoapFault $exception) {
            $this->assertEquals('Some random exception', $exception->getMessage());
            return;
        }

        $this->fail('We expected an SoapFaul but got none');
    }

    public function testTheResultIsLoggedWhenSuccessful()
    {
        $this->useApiKey();

        /**
         * This is the assertion.
         */
        $request = $this->log->expects($this->once());
        $request->method('request');

        $this->getInstance()->call($this->endpointMock, 'fakemethod', []);
    }

    public function testTheResultIsLoggedWhenAnExceptionIsThrown()
    {
        $this->useApiKey();

        $this->mockExceptionHandler();

        /**
         * This is the assertion.
         */
        $request = $this->log->expects($this->once());
        $request->method('request');

        try {
            $this->getInstance()->call($this->endpointMock, 'fakemethod', []);
        } catch (\SoapFault $exception) {}
    }

    private function useApiKey($storeId = null)
    {
        $getApiKey = $this->accountConfiguration->expects($this->once());
        $getApiKey->method('getApiKey');
        $getApiKey->willReturn('APIKEY_OF_STORE_' . $storeId);
    }

    protected function mockExceptionHandler()
    {
        $exception = new \SoapFault('Sender', 'Some random exception');

        $call = $this->soapClient->expects($this->once());
        $call->method('__call');
        $call->willThrowException($exception);

        $handle = $this->exceptionHandler->expects($this->once());
        $handle->method('handle');
        $handle->willThrowException($exception);
        $handle->with($exception);
    }
}
