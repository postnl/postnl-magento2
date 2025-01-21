<?php

namespace TIG\PostNL\Webservices\Endpoints\Address;

use TIG\PostNL\Webservices\Endpoints\RestInterface;
use TIG\PostNL\Webservices\Rest;

class InternationalAddressCheck implements RestInterface
{
    /**
     * @var Rest
     */
    private $restApi;

    private string $method = 'GET';
    private string $resource = 'shipment/checkout/';
    private string $version = 'v4';
    private string $endpoint = 'address/international/';

    /**
     * @var array
     */
    private $data = [];

    /**
     * International Address Check constructor.
     *
     * @param Rest $rest
     */
    public function __construct(
        Rest $rest
    ) {
        $this->restApi = $rest;
    }

    /**
     * {@inheritDoc}
     */
    public function call()
    {
        return $this->restApi->getRequest($this);
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
}
