<?php

namespace TIG\PostNL\Service\Timeframe;

use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Helper\AddressEnhancer;

class Options
{
    const DAYTIME_DELIVERY_OPTION = 'Daytime';
    const EVENING_DELIVERY_OPTION = 'Evening';
    const SUNDAY_DELIVERY_OPTION  = 'Sunday';
    const TODAY_DELIVERY_OPTION   = 'Today';

    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var AddressEnhancer
     */
    private $addressEnhancer;

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
        AddressEnhancer $addressEnhancer,
        Webshop $webshop
    ) {
        $this->shippingOptions = $shippingOptions;
        $this->addressEnhancer = $addressEnhancer;
        $this->webshop         = $webshop;
    }

    /**
     * @param string $countryId
     *
     * @return array
     */
    public function get($countryId = 'NL')
    {
        $deliveryTimeframesOptions = [self::DAYTIME_DELIVERY_OPTION];

        if ($this->shippingOptions->isEveningDeliveryActive($countryId)) {
            $deliveryTimeframesOptions[] = self::EVENING_DELIVERY_OPTION;
        }

        // Sunday Delivery is only available for the Netherlands
        if ($this->shippingOptions->isSundayDeliveryActive()
            && $this->hasSaturdayAsShippingDay() && $countryId == 'NL') {
            $deliveryTimeframesOptions[] = self::SUNDAY_DELIVERY_OPTION;
        }

        if ($this->shippingOptions->isTodayDeliveryActive() && $countryId == 'NL') {
            $deliveryTimeframesOptions[] = self::TODAY_DELIVERY_OPTION;
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
