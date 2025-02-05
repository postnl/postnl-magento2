<?php

namespace TIG\PostNL\Service\Timeframe;

use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Helper\AddressEnhancer;

class Options
{
    const DAYTIME_DELIVERY_OPTION = 'Daytime';
    const EVENING_DELIVERY_OPTION = 'Evening';
    const NOON_DELIVERY_OPTION   = 'Noon';

    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var Webshop
     */
    private $webshop;

    /**
     * @param ShippingOptions $shippingOptions
     * @param AddressEnhancer $addressEnhancer
     * @param Webshop $webshop
     */
    public function __construct(
        ShippingOptions $shippingOptions,
        Webshop $webshop
    ) {
        $this->shippingOptions = $shippingOptions;
        $this->webshop         = $webshop;
    }

    /**
     * @param string $countryId
     *
     * @return array
     */
    public function get(string $countryId = 'NL')
    {
        $deliveryTimeframesOptions = [self::DAYTIME_DELIVERY_OPTION];

        if ($this->shippingOptions->isEveningDeliveryActive($countryId)) {
            $deliveryTimeframesOptions[] = self::EVENING_DELIVERY_OPTION;
        }

        if ($countryId === 'NL' && $this->shippingOptions->isGuaranteedDeliveryActive()) {
            $deliveryTimeframesOptions[] = self::NOON_DELIVERY_OPTION;
        }

        return $deliveryTimeframesOptions;
    }

    /**
     * @return string
     */
    public function isSundaySortingAllowed()
    {
        $shipmentDays = explode(',', $this->webshop->getShipmentDays());
        return !empty(array_intersect(['0', '6'], $shipmentDays)) ? 'true' : 'false';
    }

    /**
     * @return bool
     */
    private function hasSaturdayAsShippingDay()
    {
        $shipmentDays = explode(',', $this->webshop->getShipmentDays());
        return in_array('6', $shipmentDays);
    }
}
