<?php

namespace TIG\PostNL\Webservices\Endpoints;

interface RestInterface
{
    /**
     * @return mixed
     */
    public function call();

    /**
     * @return string
     */
    public function getEndpoint();

    /**
     * @param array $data
     *
     * @return void
     */
    public function updateRequestData(array $data);

    /**
     * @return array
     */
    public function getRequestData();

    /**
     * @return string
     */
    public function getMethod();

    /**
     * @return string
     */
    public function getVersion();

    public function getResource(): string;
}
