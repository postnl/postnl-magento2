<?php

namespace TIG\PostNL\Webservices;

use Laminas\Http\Client as HttpClient;
use Laminas\Http\Client\Exception\RuntimeException;
use Laminas\Http\Request;
use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\DefaultConfiguration;
use TIG\PostNL\Service\Module\Version;
use TIG\PostNL\Webservices\Api\RestLog;
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
    private RestLog $log;
    private Version $versionService;

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
        DefaultConfiguration $defaultConfiguration,
        RestLog $log,
        Version $versionService
    ) {
        $this->httpClient           = $httpClient;
        $this->accountConfiguration = $accountConfiguration;
        $this->defaultConfiguration = $defaultConfiguration;
        $this->log = $log;
        $this->versionService = $versionService;
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
            $responseBody = $this->httpClient->send();
            $response = $responseBody->getBody();
        } catch (RuntimeException $exception) {
            $response = [
                'status' => 'error',
                'error'  => __('Address API exception : %1', $exception->getCode())
            ];
        } finally {
            $this->log->request($endpoint, $responseBody);
        }

        return $response;
    }

    public function callRequest(RestInterface $endpoint): array
    {
        $result = $this->getRequest($endpoint);
        if (is_string($result)) {
            try {
                $result = \json_decode($result, true, 50, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                $result = [
                    'status' => 'error',
                    'error'  => __('Unable to extract API response.')
                ];
            }
        }
        return $result;
    }

    /**
     * Includes the API key into the headers.
     */
    private function addHeaders()
    {
        $headers = [
            'apikey: ' . $this->getApiKey(),
            'SourceSystem: 66',
            'Content-Type: application/json',
            ... $this->versionService->getCachedVersions()
        ];
        $this->httpClient->setHeaders($headers);
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
