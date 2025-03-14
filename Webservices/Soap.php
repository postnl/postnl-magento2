<?php

namespace TIG\PostNL\Webservices;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\Exception as WebapiException;
use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\DefaultConfiguration;
use Laminas\Soap\Client as SoapClient;
use TIG\PostNL\Service\Module\Version;

class Soap
{
    /**
     * @var string
     */
    private $apiKey = null;

    /**
     * @var string
     */
    private $location;

    /**
     * @var DefaultConfiguration
     */
    private $defaultConfiguration;

    /**
     * @var ExceptionHandler
     */
    private $exceptionHandler;

    /**
     * @var SoapClient
     */
    private $soapClient;

    /**
     * @var Api\Log
     */
    private $log;

    /**
     * @var AccountConfiguration
     */
    private $accountConfiguration;
    private Version $versionService;

    /**
     * @param AccountConfiguration $accountConfiguration
     * @param DefaultConfiguration $defaultConfiguration
     * @param ExceptionHandler     $exceptionHandler
     * @param SoapClient           $soapClient
     * @param Api\Log              $log
     *
     * @throws WebapiException
     */
    public function __construct(
        AccountConfiguration $accountConfiguration,
        DefaultConfiguration $defaultConfiguration,
        ExceptionHandler $exceptionHandler,
        SoapClient $soapClient,
        Api\Log $log,
        Version $versionService
    ) {
        $this->defaultConfiguration = $defaultConfiguration;
        $this->exceptionHandler = $exceptionHandler;
        $this->soapClient = $soapClient;
        $this->log = $log;
        $this->accountConfiguration = $accountConfiguration;
        $this->versionService = $versionService;
    }

    /**
     * @param \TIG\PostNL\Webservices\AbstractEndpoint $endpoint
     * @param                                          $method
     * @param                                          $requestParams
     *
     * @return mixed
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    public function call(AbstractEndpoint $endpoint, $method, $requestParams)
    {
        $this->checkSoapExtensionIsLoaded();
        $this->parseEndpoint($endpoint);
        $soapClient = $this->getClient();

        try {
            return $soapClient->__call($method, [$requestParams]);
        } catch (\Exception $exception) {
            $this->exceptionHandler->handle($exception, $soapClient);
        } finally {
            $this->log->request($soapClient);
        }
    }

    /**
     * @return SoapClient
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getClient()
    {
        $this->soapClient->setWSDL($this->getLocation() . '/soap.wsdl');
        $this->soapClient->setOptions($this->getOptionsArray());

        return $this->soapClient;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getOptionsArray()
    {
        /** Unable to find an alternative, so ignore the coding standards. */
        // @codingStandardsIgnoreLine
        $headers = [
            'apikey:' . $this->getApiKey(),
            'SourceSystem:66',
            ... $this->versionService->getCachedVersions()
        ];

        $stream_context = stream_context_create([
            'http' => [
                'header' => implode("\r\n", $headers)
            ]
        ]);

        return [
            'location'       => $this->getLocation(),
            'soap_version'   => SOAP_1_2,
            'features'       => SOAP_SINGLE_ELEMENT_ARRAYS,
            'cache_wsdl'     => WSDL_CACHE_BOTH,
            'stream_context' => $stream_context,
        ];
    }

    /**
     * @throws \Magento\Framework\Webapi\Exception
     */
    private function checkSoapExtensionIsLoaded()
    {
        if (!extension_loaded('soap')) {
            // @codeCoverageIgnoreStart
            throw new WebapiException(
                // @codingStandardsIgnoreLine
                __('SOAP extension is not loaded.'),
                0,
                WebapiException::HTTP_INTERNAL_ERROR
            );
            // @codeCoverageIgnoreEnd
        }
    }

    /**
     * @return string
     */
    private function getLocation()
    {
        $base = $this->defaultConfiguration->getModusApiBaseUrl();
        $locationUrl = $base . $this->location;

        return $locationUrl;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getApiKey()
    {
        if ($this->apiKey === null) {
            $this->apiKey = $this->accountConfiguration->getApiKey();
        }

        if (empty($this->apiKey)) {
            // @codingStandardsIgnoreLine
            throw new LocalizedException(__('Please enter your API key'));
        }

        return $this->apiKey;
    }

    /**
     * @param AbstractEndpoint $endpoint
     */
    private function parseEndpoint(AbstractEndpoint $endpoint)
    {
        $this->location = $endpoint->getLocation();
    }

    /**
     * @param null|int $scopeId
     */
    public function updateApiKey($scopeId = null, $websiteScope = false)
    {
        $this->apiKey = $this->accountConfiguration->getApiKey($scopeId, $websiteScope);
    }
}
