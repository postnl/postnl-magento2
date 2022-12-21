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
use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\DefaultConfiguration;
use Magento\Framework\HTTP\ZendClient as ZendClient;
use TIG\PostNL\Webservices\Endpoints\Address\RestInterface;

class Rest
{
    /**
     * @var string
     */
    private $apiKey = null;

    /**
     * @var ZendClient
     */
    private $zendClient;

    /**
     * @var AccountConfiguration
     */
    private $accountConfiguration;

    /**
     * @var DefaultConfiguration
     */
    private $defaultConfiguration;

    /**
     * Rest constructor.
     *
     * @param ZendClient           $zendClient
     * @param AccountConfiguration $accountConfiguration
     * @param DefaultConfiguration $defaultConfiguration
     */
    public function __construct(
        ZendClient $zendClient,
        AccountConfiguration $accountConfiguration,
        DefaultConfiguration $defaultConfiguration
    ) {
        $this->zendClient           = $zendClient;
        $this->accountConfiguration = $accountConfiguration;
        $this->defaultConfiguration = $defaultConfiguration;
    }

    /**
     * @param RestInterface $endpoint
     *
     * @return array|\Zend_Http_Response
     */
    public function getRequest(RestInterface $endpoint)
    {
        $this->zendClient->resetParameters();
        $this->addUri($endpoint);
        $this->addApiKeyToHeaders();
        $this->addParameters($endpoint);

        try {
            $response = $this->zendClient->request();
            $response = $response->getBody();
        } catch (\Zend_Http_Client_Exception $exception) {
            $response = [
                'status' => 'error',
                'error'  => __('Address API exception : %1', $exception->getCode())
            ];
        }

        return $response;
    }

    /**
     * Includes the API key into the headers.
     */
    private function addApiKeyToHeaders()
    {
        $this->zendClient->setHeaders([
            'apikey' => $this->getApiKey()
        ]);
    }

    /**
     * @param RestInterface $endpoint
     */
    private function addParameters(RestInterface $endpoint)
    {
        $params = $endpoint->getRequestData();
        if ($endpoint->getMethod() == ZendClient::GET) {
            $this->zendClient->setParameterGet($params);
        }

        if ($endpoint->getMethod() == ZendClient::POST) {
            $this->zendClient->setRawData(json_encode($params), 'application/json');
        }

        $this->zendClient->setMethod($endpoint->getMethod());
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
     * @param RestInterface $endpoint
     */
    private function addUri(RestInterface $endpoint)
    {
        $url = $this->defaultConfiguration->getModusAddressApiUrl();

        if (!$endpoint->useAddressUri()) {
            $url = $this->defaultConfiguration->getModusApiUrl();
        }

        $uri = $url . $endpoint->getVersion() . '/' . $endpoint->getEndpoint();
        $this->zendClient->setUri($uri);
    }
}
