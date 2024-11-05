<?php

namespace TIG\PostNL\Webservices\Endpoints\Address;

use Magento\Framework\App\CacheInterface;
use Symfony\Component\HttpFoundation\Exception\JsonException;
use TIG\PostNL\Webservices\Endpoints\RestInterface;
use TIG\PostNL\Webservices\Rest;

class InternationalAddressCheck implements RestInterface
{
    /**
     * @var Rest
     */
    private $restApi;

    private string $method = 'GET';
    private string $resource = '';
    private string $version = 'v4';
    private string $endpoint = 'address/international/';

    /**
     * @var array
     */
    private $data = [];
    private CacheInterface $cache;

    /**
     * International Address Check constructor.
     *
     * @param Rest $rest
     * @param CacheInterface $cache
     */
    public function __construct(
        Rest $rest,
        CacheInterface $cache
    ) {
        $this->restApi = $rest;
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function call()
    {
        $cacheKey = $this->getIdentifier();
        if ($cacheKey && $content = $this->cache->load($cacheKey)) {
            return $content;
        }

        $response = $this->restApi->getRequest($this);

        if ($cacheKey && $response) {
            $this->cache->save($response, $cacheKey, ['POSTNL_REQUESTS'], 60 * 5);
        }
        return $response;
    }

    /**
     * {@inheritDoc}
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * {@inheritDoc}
     */
    public function getRequestData()
    {
        return $this->data;
    }

    /**
     * {@inheritDoc}
     */
    public function updateRequestData(array $data)
    {
        $this->data = $data;
    }

    /**
     * {@inheritDoc}
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * {@inheritDoc}
     */
    public function getVersion()
    {
        return $this->version;
    }

    public function getResource(): string
    {
        return $this->resource;
    }

    private function getIdentifier(): ?string
    {
        try {
            return 'POSTNL_INT_ADDRESS_' . json_encode($this->data, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return null;
        }
    }

}
