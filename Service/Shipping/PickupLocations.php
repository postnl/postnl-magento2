<?php

namespace TIG\PostNL\Service\Shipping;

use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Webservices\Endpoints\Locations as LocationsEndpoint;

class PickupLocations
{
    private ?string $deliveryDateRequest = null;
    private $cache = [];
    private DeliveryDate $deliveryDate;
    private LocationsEndpoint $locationsEndpoint;

    public function __construct(
        DeliveryDate $deliveryDate,
        LocationsEndpoint $locationsEndpoint,
    ) {
        $this->deliveryDate = $deliveryDate;
        $this->locationsEndpoint = $locationsEndpoint;
    }

    public function getLastDeliveryDate(): ?string
    {
        return $this->deliveryDateRequest;
    }

    /**
     * @param array $address
     * @return \stdClass[]
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Webapi\Exception
     */
    public function get(array $address): array
    {
        $cacheKey = $address['country'] . $address['postcode'] . implode('', $address['street']);
        if (isset($this->cache[$cacheKey])) {
            return $this->cache[$cacheKey];
        }
        $deliveryDate = false;
        if ($availableDelivery = $this->deliveryDate->get($address)) {
            // Reduce requests
            $this->deliveryDateRequest = $availableDelivery;
            $deliveryDate = $availableDelivery;
        }

        $this->locationsEndpoint->changeAPIKeyByStoreId($this->deliveryDate->getStoreId());
        $this->locationsEndpoint->updateParameters($address ,$deliveryDate);
        $response = $this->locationsEndpoint->call();
        if (!is_object($response) || !isset($response->GetLocationsResult->ResponseLocation)) {
            throw new LocalizedException(__('Invalid GetLocationsResult response: %1', var_export($response, true)));
        }

        $this->cache[$cacheKey] = $response->GetLocationsResult->ResponseLocation;
        return $response->GetLocationsResult->ResponseLocation;
    }
}
