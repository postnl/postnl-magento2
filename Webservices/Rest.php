<?php

namespace TIG\PostNL\Webservices;

use Laminas\Http\Client as HttpClient;
use Laminas\Http\Client\Exception\RuntimeException;
use Laminas\Http\Request;
use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\DefaultConfiguration;
use TIG\PostNL\Webservices\Endpoints\RestInterface;

class Rest
{
    /**
     * @var string
     */
    private $apiKey = null;

    /**
     * @var HttpClient
     */
    private $httpClient;

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
     * @param HttpClient           $httpClient
     * @param AccountConfiguration $accountConfiguration
     * @param DefaultConfiguration $defaultConfiguration
     */
    public function __construct(
        HttpClient $httpClient,
        AccountConfiguration $accountConfiguration,
        DefaultConfiguration $defaultConfiguration
    ) {
        $this->httpClient           = $httpClient;
        $this->accountConfiguration = $accountConfiguration;
        $this->defaultConfiguration = $defaultConfiguration;
    }

    /**
     * @param RestInterface $endpoint
     *
     * @return array|string
     */
    public function getRequest(RestInterface $endpoint)
    {
        $this->httpClient->resetParameters();
        $this->addUri($endpoint);
        $this->addHeaders();
        $this->addParameters($endpoint);

        try {
            $response = $this->httpClient->send();
            $response = $response->getBody();
        } catch (RuntimeException $exception) {
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
    private function addHeaders()
    {
        $this->httpClient->setHeaders([
            'apikey: ' . $this->getApiKey(),
            'SourceSystem: 66',
            'Content-Type: application/json'
        ]);
    }

    /**
     * @param RestInterface $endpoint
     */
    private function addParameters(RestInterface $endpoint)
    {
        $params = $endpoint->getRequestData();
        if ($endpoint->getMethod() == Request::METHOD_GET) {
            $this->httpClient->setParameterGet($params);
        }

        if ($endpoint->getMethod() == Request::METHOD_POST) {
            $this->httpClient->setRawBody(json_encode($params));
        }

        $this->httpClient->setMethod($endpoint->getMethod());
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
        $url = $this->defaultConfiguration->getModusApiUrl();

        $uri = $url . $endpoint->getResource() . $endpoint->getVersion() . '/' . $endpoint->getEndpoint();
        $this->httpClient->setUri($uri);
    }
}
