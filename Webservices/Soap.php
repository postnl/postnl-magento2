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
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\PostNL\Webservices;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\Exception as WebapiException;
use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\DefaultConfiguration;

/**
 * Class Soap
 *
 * @package TIG\PostNL\Webservices
 */
class Soap
{
    /**
     * @var AccountConfiguration
     */
    private $accountConfig;

    /**
     * @var AbstractEndpoint
     */
    private $endpoint;

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
     * @param AccountConfiguration   $accountConfiguration
     * @param DefaultConfiguration   $defaultConfiguration
     * @param ExceptionHandler       $exceptionHandler
     * @param Response               $response
     *
     * @throws WebapiException
     */
    public function __construct(
        AccountConfiguration $accountConfiguration,
        DefaultConfiguration $defaultConfiguration,
        ExceptionHandler $exceptionHandler,
        Response $response
    ) {
        $this->checkSoapExtensionIsLoaded();

        $this->accountConfig = $accountConfiguration;
        $this->defaultConfiguration = $defaultConfiguration;
        $this->exceptionHandler = $exceptionHandler;
        $this->response = $response;
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
        $this->endpoint = $endpoint;
        $soapClient = $this->getClient();

        try {
            $result = $soapClient->__call($method, [$requestParams]);
            $this->response->set($result);

            return $this->response->get();
        } catch (\Exception $exception) {
            $this->exceptionHandler->handle($exception, $soapClient);

            throw new WebapiException(
                __('Failed on soap call : %1', $exception->getMessage()),
                0,
                WebapiException::HTTP_INTERNAL_ERROR
            );
        }
    }

    /**
     * @return \SoapClient
     */
    public function getClient()
    {
        $wsdlUrl    = $this->getWsdlUrl();
        $soapClient = new \SoapClient($wsdlUrl, $this->getOptionsArray());

        return $soapClient;
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function getOptionsArray()
    {
        $apiKey = $this->getApiKey();

        return [
            'location'       => $this->getLocation(),
            'soap_version'   => SOAP_1_2,
            'features'       => SOAP_SINGLE_ELEMENT_ARRAYS,
            'trace'          => true,
            'cache_wsdl'     => WSDL_CACHE_BOTH,
            'stream_context' => stream_context_create(
                [
                    'http' => [
                        'header' => 'apikey:' . $apiKey
                    ]
                ]
            )
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
        $wsdlUrl = $prefix . $this->endpoint->getWsdlUrl() . '?wsdl';

        return $wsdlUrl;
    }

    /**
     * @return string
     */
    private function getLocation()
    {
        $base = $this->defaultConfiguration->getModusApiBaseUrl();
        $locationUrl = $base . $this->endpoint->getLocation();

        return $locationUrl;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getApiKey()
    {
        $apiKey = $this->accountConfig->getApiKey();

        if (empty($apiKey)) {
            throw new LocalizedException('Please enter your API key');
        }

        return $apiKey;
    }
}
