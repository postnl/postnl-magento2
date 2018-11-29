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
namespace TIG\PostNL\Webservices\Endpoints\Address;

use TIG\PostNL\Webservices\Rest;

class Postalcode implements RestInterface
{
    /**
     * @var Rest
     */
    private $restApi;

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
    public function setRequestData(array $data)
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
}
