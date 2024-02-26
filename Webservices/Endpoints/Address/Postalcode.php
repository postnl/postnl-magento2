<?php

namespace TIG\PostNL\Webservices\Endpoints\Address;

use TIG\PostNL\Webservices\Endpoints\RestInterface;
use TIG\PostNL\Webservices\Rest;

class Postalcode implements RestInterface
{
    /**
     * @var Rest
     */
    private $restApi;

    private string $method = 'POST';
    private string $resource = 'shipment/checkout/';
    private string $version = 'v1';
    private string $endpoint = 'postalcodecheck/';

    /**
     * @var array
     */
    private $data = [];

    /**
     * Postalcode constructor.
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
