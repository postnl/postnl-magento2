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
namespace TIG\PostNL\Webservices\Endpoints;

use TIG\PostNL\Webservices\Soap;
use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Helper\Data;

class Locations extends AbstractEndpoint
{
    /**
     * @var string
     */
    private $version = 'v2_1';

    /**
     * @var string
     */
    private $endpoint = 'locations';

    /**
     * @var Soap
     */
    private $soap;

    /**
     * @var array
     */
    private $requestParams;

    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var Data
     */
    private $postNLhelper;

    /**
     * @var  Message
     */
    private $message;

    /**
     * @param Soap            $soap
     * @param Data            $postNLhelper
     * @param ShippingOptions $shippingOptions
     * @param Message         $message
     */
    public function __construct(
        Soap $soap,
        Data $postNLhelper,
        ShippingOptions $shippingOptions,
        Message $message
    ) {
        $this->soap = $soap;
        $this->shippingOptions = $shippingOptions;
        $this->postNLhelper  = $postNLhelper;
        $this->message = $message;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function call()
    {
        return $this->soap->call($this, 'GetNearestLocations', $this->requestParams);
    }

    /**
     * @param $address
     * @param $startDate
     */
    public function setParameters($address, $startDate = false)
    {
        $this->requestParams = [
            'Location' => [
                'DeliveryOptions'    => $this->postNLhelper->getAllowedDeliveryOptions(),
                'DeliveryDate'       => $this->getDeliveryDate($startDate),
                'Postalcode'         => str_replace(' ', '', $address['postcode']),
                'Options'            => ['Daytime', 'Morning'],
                'AllowSundaySorting' => 'true'
            ],
            'Countrycode' => $address['country'],
            'Message'     => $this->message->get('')
        ];
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->version .'/'. $this->endpoint;
    }

    /**
     * @param $startDate
     *
     * @return bool|string
     */
    public function getDeliveryDate($startDate)
    {
        if ($startDate !== false) {
            return $startDate;
        }

        return $this->postNLhelper->getTommorowsDate();
    }

    /**
     * @param int $storeId
     */
    public function setStoreId($storeId)
    {
        $this->soap->updateApiKey($storeId);
    }
}
