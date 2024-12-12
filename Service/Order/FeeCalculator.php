<?php

namespace TIG\PostNL\Service\Order;

use TIG\PostNL\Config\Provider\ShippingOptions;

class FeeCalculator
{
    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @param ShippingOptions $shippingOptions
     */
    public function __construct(
        ShippingOptions $shippingOptions
    ) {
        $this->shippingOptions = $shippingOptions;
    }

    /**
     * @param $params
     *
     * @return float
     */
    public function get($params)
    {
        if (!array_key_exists('option', $params)) {
            return (float)0.0;
        }

        if ($this->shippingOptions->isEveningDeliveryActive() && $params['option'] === 'Evening') {
            return (float)$this->getEveningDeliveryFee($params);
        }

        if ($this->shippingOptions->isSundayDeliveryActive() && $params['option'] === 'Sunday') {
            return (float)$this->shippingOptions->getSundayDeliveryFee();
        }

        if ($this->shippingOptions->isTodayDeliveryActive() && $params['option'] === 'Today') {
            return (float)$this->shippingOptions->getTodayDeliveryFee();
        }

        if ($this->shippingOptions->isTodayDeliveryActive() && $params['option'] === 'Noon') {
            return (float)$this->shippingOptions->getNoonDeliveryFee();
        }

        return (float)0.0;
    }

    /**
     * @param $params
     *
     * @return mixed
     */
    private function getEveningDeliveryFee($params)
    {
        $country = isset($params['country']) ? $params['country'] : 'NL';
        return $this->shippingOptions->getEveningDeliveryFee($country);
    }

    /**
     * @param array $params
     *
     * @return float
     */
    public function statedAddressOnlyFee($params)
    {
        if (isset($params['stated_address_only'])
            && $params['stated_address_only']
            && $this->shippingOptions->isStatedAddressOnlyActive()) {
            return (float)$this->shippingOptions->getStatedAddressOnlyFee();
        }

        return (float)0.0;
    }
}
