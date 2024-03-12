<?php

namespace TIG\PostNL\Webservices\Endpoints\Shipment;

use TIG\PostNL\Webservices\Endpoints\RestInterface;
use TIG\PostNL\Webservices\Rest;

class ActivateReturn implements RestInterface
{
    private string $method = 'POST';
    private string $resource = 'parcels/';
    private string $version = 'v1';
    private string $endpoint = 'shipment/activatereturn/';

    private Rest $restApi;
    private array $data = [];

    public function __construct(
        Rest $restApi
    ) {
        $this->restApi = $restApi;
    }

    public function call()
    {
        return $this->restApi->callRequest($this);
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function updateRequestData(array $data): void
    {
        $this->data = $data;
    }

    public function getRequestData(): array
    {
        return $this->data;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getResource(): string
    {
        return $this->resource;
    }
}
