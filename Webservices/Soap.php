<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\PostNL\Webservices;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\Exception as WebapiException;
use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\DefaultConfiguration;
use Zend\Soap\Client as ZendSoapClient;

/**
 * Class Soap
 *
 * @package TIG\PostNL\Webservices
 */
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
     * @var Response
     */
    private $response;

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
     * @param AccountConfiguration $accountConfiguration
     * @param DefaultConfiguration $defaultConfiguration
     * @param ExceptionHandler     $exceptionHandler
     * @param Response             $response
     * @param ZendSoapClient       $soapClient
     * @param Api\Log              $log
     *
     * @throws WebapiException
     */
    public function __construct(
        AccountConfiguration $accountConfiguration,
        DefaultConfiguration $defaultConfiguration,
        ExceptionHandler $exceptionHandler,
        Response $response,
        ZendSoapClient $soapClient,
        Api\Log $log
    ) {
        $this->checkSoapExtensionIsLoaded();

        $this->defaultConfiguration = $defaultConfiguration;
        $this->exceptionHandler = $exceptionHandler;
        $this->response = $response;
        $this->soapClient = $soapClient;

        $this->apikey = $accountConfiguration->getApiKey();
        $this->log = $log;
    }

    /**
     * @param AbstractEndpoint $endpoint
     * @param                  $method
     * @param                  $requestParams
     *
     * @return Response
     * @throws WebapiException
     */
    public function call(AbstractEndpoint $endpoint, $method, $requestParams)
    {
        $this->parseEndpoint($endpoint);
        $soapClient = $this->getClient();

        try {
            $result = $soapClient->__call($method, [$requestParams]);
            $this->log->request($soapClient);
            $this->response->set($result);

            return $this->response->get();
        } catch (\Exception $exception) {
            $this->log->request($soapClient);
            $this->exceptionHandler->handle($exception, $soapClient);

            throw new WebapiException(
                __('Failed on soap call : %1', $exception->getMessage()),
                0,
                WebapiException::HTTP_INTERNAL_ERROR
            );
        }
    }

    /**
     * @return ZendSoapClient
     */
    public function getClient()
    {
        $wsdlUrl = $this->getWsdlUrl();

        $this->soapClient->setWSDL($wsdlUrl);
        $this->soapClient->setOptions($this->getOptionsArray());

        return $this->soapClient;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getOptionsArray()
    {
        /**
         * Unable to find an alternative, so ignore the coding standards.
         */
        // @codingStandardsIgnoreLine
        $stream_context = stream_context_create([
            'http' => [
                'header' => 'apikey:' . $this->getApiKey() . "\r\n"
            ]
        ]);

        return [
            'location'       => $this->getLocation(),
            'soap_version'   => SOAP_1_2,
            'features'       => SOAP_SINGLE_ELEMENT_ARRAYS,
            'cache_wsdl'     => WSDL_CACHE_NONE,
            'stream_context' => $stream_context
        ];
    }

    /**
     * @throws WebapiException
     */
    private function checkSoapExtensionIsLoaded()
    {
        if (!extension_loaded('soap')) {
            throw new WebapiException(
                // @codingStandardsIgnoreLine
                __('SOAP extension is not loaded.'),
                0,
                WebapiException::HTTP_INTERNAL_ERROR
            );
        }
    }

    /**
     * @return string
     */
    private function getWsdlUrl()
    {
        $prefix = $this->defaultConfiguration->getModusCifBaseUrl();
        $wsdlUrl = $prefix . $this->wsdlUrl . '?wsdl';

        return $wsdlUrl;
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
            throw new LocalizedException('Please enter your API key');
        }

        return $this->apikey;
    }

    /**
     * @param AbstractEndpoint $endpoint
     */
    private function parseEndpoint(AbstractEndpoint $endpoint)
    {
        $this->location = $endpoint->getLocation();
        $this->wsdlUrl  = $endpoint->getWsdlUrl();
    }
}
