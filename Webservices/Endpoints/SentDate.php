<?php

namespace TIG\PostNL\Webservices\Endpoints;

use Magento\Customer\Model\Address\AbstractAddress as Address;
use TIG\PostNL\Api\Data\OrderInterface as PostNLOrder;
use TIG\PostNL\Model\Order;
use TIG\PostNL\Service\Order\ProductInfo;
use TIG\PostNL\Service\Timeframe\Options;
use TIG\PostNL\Webservices\AbstractEndpoint;
use TIG\PostNL\Webservices\Api\Exception;
use TIG\PostNL\Webservices\Api\Message;
use TIG\PostNL\Webservices\Api\DeliveryDateFallback;
use TIG\PostNL\Webservices\Api\CutoffTimes;
use TIG\PostNL\Webservices\Parser\Label\Shipments as ShipmentData;
use TIG\PostNL\Webservices\Soap;

class SentDate extends AbstractEndpoint
{
    /**
     * @var string
     */
    private $version = 'v2_2';

    /**
     * @var string
     */
    private $endpoint = 'calculate/date';

    /**
     * @var string
     */
    private $type = 'GetSentDate';

    /**
     * @var array
     */
    private $requestParams;

    /**
     * @var Soap
     */
    private $soap;

    /**
     * @var Message
     */
    private $message;

    /**
     * @var CutoffTimes
     */
    private $cutoffTimes;

    /**
     * @var Options
     */
    private $timeframeOptions;

    /**
     * @var DeliveryDateFallback
     */
    private $dateFallback;

    /**
     * SentDate constructor.
     *
     * @param \TIG\PostNL\Webservices\Soap                     $soap
     * @param \TIG\PostNL\Webservices\Api\CutoffTimes          $cutoffTimes
     * @param \TIG\PostNL\Service\Timeframe\Options            $timeframeOptions
     * @param \TIG\PostNL\Webservices\Api\Message              $message
     * @param \TIG\PostNL\Webservices\Api\DeliveryDateFallback $dateFallback
     * @param \TIG\PostNL\Webservices\Parser\Label\Shipments   $shipmentData
     */
    public function __construct(
        Soap $soap,
        CutoffTimes $cutoffTimes,
        Options $timeframeOptions,
        Message $message,
        DeliveryDateFallback $dateFallback,
        ShipmentData $shipmentData
    ) {
        $this->soap             = $soap;
        $this->message          = $message->get('');
        $this->cutoffTimes      = $cutoffTimes;
        $this->timeframeOptions = $timeframeOptions;
        $this->dateFallback     = $dateFallback;

        parent::__construct(
            $shipmentData
        );
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Webapi\Exception
     * @throws Exception
     */
    public function call()
    {
        $response = $this->soap->call($this, $this->type, $this->requestParams);

        return $response->SentDate;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->version . '/' . $this->endpoint;
    }

    /**
     * @param \Magento\Sales\Model\Order\Address|\Magento\Quote\Model\Quote\Address|null $address
     * @param int|string $storeId
     * @param PostNLOrder $postNLOrder
     * @return void
     */
    public function updateParameters($address, $storeId, $postNLOrder)
    {
        $this->soap->updateApiKey($storeId);

        $this->requestParams = [
            $this->type => [
                'CountryCode'        => $this->getCountryId($address),
                'PostalCode'         => $this->getPostcode($address),
                'HouseNr'            => '',
                'HouseNrExt'         => '',
                'Street'             => '',
                'City'               => $address->getCity(),
                'DeliveryDate'       => $this->getDeliveryDate($address, $postNLOrder),
                'ShippingDuration'   => '1', // Request by PostNL not to use $postNLOrder->getShippingDuration()
                'AllowSundaySorting' => $this->timeframeOptions->isSundaySortingAllowed(),
                'Options'            => [$this->getOption($address, $postNLOrder)]
            ], 'Message'   => $this->message
        ];
    }

    /**
     * GetSentDate 2.2 doesn't support multiple options for requests. That's why we send
     * along the option actually selected.
     *
     * @param PostNLOrder $postNLOrder
     * @return string
     */
    private function getOption($address, PostNLOrder $postNLOrder)
    {
        $availableOptions = $this->timeframeOptions->get($this->getCountryId($address));
        $currentType      = $postNLOrder->getType();

        if (in_array($currentType, $availableOptions, true)) {
            return $currentType;
        }

        if ($currentType === ProductInfo::SHIPMENT_TYPE_PG) {
            return ucfirst(ProductInfo::TYPE_PICKUP);
        }

        return ProductInfo::SHIPMENT_TYPE_DAYTIME;
    }

    /**
     * This endpoint is only available for dutch and belgian addresses.
     *
     * @var \Magento\Quote\Model\Quote\Address|\Magento\Sales\Model\Order\Address|null $shippingAddress
     * @return string
     * @see getPostcode
     */
    private function getCountryId($shippingAddress)
    {
        return $shippingAddress && in_array($shippingAddress->getCountryId(), ['NL', 'BE'])
            ? $shippingAddress->getCountryId() : 'NL';
    }

    /**
     * The sent date webservice can only work with NL and BE addresses. That's why we default use the PostNL Pakketten
     * office postcode for addresses outside the Netherlands or Belgium.
     *
     * @param Address $address
     *
     * @return string
     */
    private function getPostcode($address)
    {
        if ($address->getCountryId() != 'NL' && $address->getCountryId() != 'BE') {
            return '2521CA';
        }

        $postcode = $address->getPostcode();
        $postcode = str_replace(' ', '', $postcode);
        $postcode = strtoupper($postcode);
        $postcode = trim($postcode);

        return $postcode;
    }

    /**
     * @param Address     $address
     * @param PostNLOrder $postNLOrder
     *
     * @return string
     */
    private function getDeliveryDate($address, PostNLOrder $postNLOrder)
    {
        $deliveryDate = $postNLOrder->getDeliveryDate();
        if ($deliveryDate == null) {
            return $this->dateFallback->get();
        }

        if (in_array($address->getCountryId(), ['NL', 'BE'])
            || ($address->getCountryId() === null && !empty($deliveryDate))) {
            return $this->dateFallback->getDate($deliveryDate);
        }

        return $this->dateFallback->get();
    }
}
