<?php

namespace TIG\PostNL\Test\Unit\Webservices;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Webservices\Soap;

class SoapTest extends TestCase
{
    public $instanceClass = Soap::class;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\TIG\PostNL\Webservices\AbstractEndpoint
     */
    private $endpointMock;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $soapClient;

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
    private $exceptionHandler;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $log;

    protected function setUp() : void
    {
        $this->soapClient = $this->getMock(\Laminas\Soap\Client::class);
        $this->endpointMock = $this->getFakeMock(\TIG\PostNL\Webservices\AbstractEndpoint::class, true);
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
