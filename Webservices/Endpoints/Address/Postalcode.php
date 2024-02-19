<?php

namespace TIG\PostNL\Webservices\Endpoints\Address;

use TIG\PostNL\Webservices\Rest;

class Postalcode implements RestInterface
{
    /**
     * @var Rest
     */
    private $restApi;

    /**
     * @var bool
     */
    private $useAddressUri = true;

    /**
     * @var string
     */
    private $endpoint = 'postalcodecheck/';

    /**
     * @var string
     */
    private $method = 'POST';

    /**
     * @var string
     */
    private $version = 'v1';

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

    /**
     * {@inheritDoc}
     */
    public function useAddressUri()
    {
        return $this->useAddressUri;
    }
}
