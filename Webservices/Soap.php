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
namespace TIG\PostNL\Webservices;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\Exception as WebapiException;
use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\DefaultConfiguration;
use Zend\Soap\Client as ZendSoapClient;

class Soap
{
    /**
     * @var string
     */
    private $apikey;

    /**
     * @var string
     */
    private $location;

    /**
     * @var string
     */
    private $wsdlUrl;

    /**
     * @var DefaultConfiguration
     */
    private $defaultConfiguration;

    /**
     * @var ExceptionHandler
     */
    private $exceptionHandler;

    /**
     * @var ZendSoapClient
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

    /**
     * @param AccountConfiguration $accountConfiguration
     * @param DefaultConfiguration $defaultConfiguration
     * @param ExceptionHandler     $exceptionHandler
     * @param ZendSoapClient       $soapClient
     * @param Api\Log              $log
     *
     * @throws WebapiException
     */
    public function __construct(
        AccountConfiguration $accountConfiguration,
        DefaultConfiguration $defaultConfiguration,
        ExceptionHandler $exceptionHandler,
        ZendSoapClient $soapClient,
        Api\Log $log
    ) {
        $this->defaultConfiguration = $defaultConfiguration;
        $this->exceptionHandler = $exceptionHandler;
        $this->soapClient = $soapClient;
        $this->log = $log;
        $this->accountConfiguration = $accountConfiguration;

        $this->updateApiKey();
    }

    /**
     * @param AbstractEndpoint $endpoint
     * @param                  $method
     * @param                  $requestParams
     *
     * @return \stdClass
     * @throws WebapiException
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
     * @return ZendSoapClient
     */
    public function getClient()
    {
        $this->soapClient->setWSDL($this->getWsdlUrl());
        $this->soapClient->setOptions($this->getOptionsArray());

        return $this->soapClient;
    }

    /**
     * @return array
     * @throws LocalizedException
     */
    private function getOptionsArray()
    {
        /** Unable to find an alternative, so ignore the coding standards. */
        // @codingStandardsIgnoreLine
        $stream_context = stream_context_create([
            'http' => [
                'header' => 'apikey:' . $this->getApiKey()
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
     * @throws WebapiException
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
    private function getWsdlUrl()
    {
        return $this->getLocation() . '/soap.wsdl';
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
        if (empty($this->apikey)) {
            // @codingStandardsIgnoreLine
            throw new LocalizedException(__('Please enter your API key'));
        }

        return $this->apikey;
    }

    /**
     * @param AbstractEndpoint $endpoint
     */
    private function parseEndpoint(AbstractEndpoint $endpoint)
    {
        $this->location = $endpoint->getLocation();
    }

    /**
     * @param null|int $storeId
     */
    public function updateApiKey($storeId = null)
    {
        $this->apikey = $this->accountConfiguration->getApiKey($storeId);
    }
}
