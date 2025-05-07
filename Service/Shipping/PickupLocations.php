<?php

namespace TIG\PostNL\Service\Shipping;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Service\Timeframe\Filters\DaysSkipInterface;
use TIG\PostNL\Webservices\Endpoints\Locations as LocationsEndpoint;

class PickupLocations
{
    private ?string $deliveryDateRequest = null;
    private array $localCache = [];
    private DeliveryDate $deliveryDate;
    private LocationsEndpoint $locationsEndpoint;
    private CacheInterface $cache;

    /**
     * @var DaysSkipInterface[]
     */
    private array $daysFilter;

    public function __construct(
        DeliveryDate $deliveryDate,
        LocationsEndpoint $locationsEndpoint,
        CacheInterface $cache,
        array $daysFilter = []
    ) {
        $this->deliveryDate = $deliveryDate;
        $this->locationsEndpoint = $locationsEndpoint;
        $this->cache = $cache;
        $this->daysFilter = $daysFilter;
    }

    public function getLastDeliveryDate(): ?string
    {
        try {
            $day = new \DateTime($this->deliveryDateRequest);
            foreach ($this->daysFilter as $filter) {
                $day = $filter->skip($day);
            }
            return $day->format('d-m-Y');
        } catch (\Exception $e) {
            // Just return default date in case of errors
            return $this->deliveryDateRequest;
        }
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
        $cacheKey = $this->getIdentifier($address);
        if (isset($this->localCache[$cacheKey])) {
            return $this->localCache[$cacheKey];
        }
        $content = $this->cache->load($cacheKey);
        if ($content) {
            try {
                $data = \json_decode($content, false, 32, JSON_THROW_ON_ERROR);
                $this->deliveryDateRequest = $data->delivery_date;
                $this->localCache[$cacheKey] = $data->locations;
                return $this->localCache[$cacheKey];
            } catch (\Exception $e) {
                // retrieve data from api
            }
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

        $this->localCache[$cacheKey] = $response->GetLocationsResult->ResponseLocation;
        $toCache = [
            'delivery_date' => $deliveryDate,
            'locations' => $response->GetLocationsResult->ResponseLocation
        ];
        $this->cache->save(\json_encode($toCache), $cacheKey, ['POSTNL_REQUESTS'], 60 * 5);
        return $response->GetLocationsResult->ResponseLocation;
    }

    private function getIdentifier(array $address): string
    {
        return 'POSTNL_PG_' . $address['country'] . $address['postcode'] . implode('', $address['street']) . $address['housenumber'];
    }
}
