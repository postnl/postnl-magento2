<?php

namespace TIG\PostNL\Service\Shipment;

use TIG\PostNL\Config\Provider\ShippingOptions;

class PickupValidator
{
    private ShippingOptions $shippingOptions;

    public function __construct(
        ShippingOptions $shippingOptions
    ) {
        $this->shippingOptions = $shippingOptions;
    }

    public function isPickupEnabledForCountry(string $countryId): bool
    {
        $result = ($countryId === 'NL' || $countryId === 'BE') && $this->shippingOptions->isPakjegemakActive($countryId);
        if (!$result && $this->shippingOptions->isPakjegemakGlobalActive()
            && in_array($countryId, $this->shippingOptions->getPakjegemakGlobalCountries(), true)
        ) {
            $result = true;
        }
        return $result;
    }

    public function isDefaultPickupActive(string $countryId): bool
    {
        $isDefaultPickupEnabled = $this->shippingOptions->isPakjegemakDefault($countryId);
        return $isDefaultPickupEnabled && $this->isPickupEnabledForCountry($countryId);
    }
}
