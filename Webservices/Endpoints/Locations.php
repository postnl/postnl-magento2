<?php

namespace TIG\PostNL\Webservices\Endpoints;

use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Webservices\Soap;
use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Webservices\Parser\Label\Shipments as ShipmentData;

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
     * Locations constructor.
     *
     * @param \TIG\PostNL\Webservices\Soap                   $soap
     * @param \TIG\PostNL\Helper\Data                        $postNLhelper
     * @param \TIG\PostNL\Config\Provider\ShippingOptions    $shippingOptions
     * @param \TIG\PostNL\Webservices\Api\Message            $message
     * @param \TIG\PostNL\Webservices\Parser\Label\Shipments $shipmentData
     */
    public function __construct(
        Soap $soap,
        Data $postNLhelper,
        ShippingOptions $shippingOptions,
        Message $message,
        ShipmentData $shipmentData
    ) {
        $this->soap            = $soap;
        $this->shippingOptions = $shippingOptions;
        $this->postNLhelper    = $postNLhelper;
        $this->message         = $message;

        parent::__construct(
            $shipmentData
        );
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
    public function updateParameters($address, $startDate = false)
    {
        $this->requestParams = [
            'Location'    => [
                'DeliveryOptions'    => $this->postNLhelper->getAllowedDeliveryOptions($address['country']),
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
        return $this->version . '/' . $this->endpoint;
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

        return $this->postNLhelper->getTomorrowsDate();
    }

    /**
     * @param int $storeId
     */
    public function changeAPIKeyByStoreId($storeId)
    {
        $this->soap->updateApiKey($storeId);
    }
}
