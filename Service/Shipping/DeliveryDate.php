<?php

namespace TIG\PostNL\Service\Shipping;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use TIG\PostNL\Config\Source\General\PickupCountries;
use TIG\PostNL\Service\Quote\ShippingDuration;
use TIG\PostNL\Service\Timeframe\Filters\DaysSkipInterface;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate as DeliveryDateEndpoint;

class DeliveryDate
{
    private DeliveryDateEndpoint $deliveryEndpoint;
    private Session $checkoutSession;
    private ShippingDuration $shippingDuration;

    /**
     * @var DaysSkipInterface[]
     */
    private array $daysFilter;

    private array $approximateDeliveryTime = [
        PickupCountries::COUNTRY_DE => 3,
        PickupCountries::COUNTRY_FR => 4,
        PickupCountries::COUNTRY_DK => 3,
    ];
    private TimezoneInterface $timezone;

    public function __construct(
        Session $checkoutSession,
        DeliveryDateEndpoint $deliveryDateEndpoint,
        ShippingDuration $shippingDuration,
        TimezoneInterface $timezone,
        array $daysFilter = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->deliveryEndpoint = $deliveryDateEndpoint;
        $this->shippingDuration = $shippingDuration;
        $this->timezone = $timezone;
        $this->daysFilter = $daysFilter;
    }

    public function getStoreId(): int
    {
        return $this->checkoutSession->getQuote()->getStoreId();
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     * @throws Exception
     */
    public function get(array $address): ?string
    {
        if ($address['country'] !== 'NL' && $address['country'] !== 'BE') {
            return $this->checkOtherCountries($address);
        }
        $shippingDuration = $this->shippingDuration->get();
        $this->deliveryEndpoint->updateApiKey($this->getStoreId());
        $this->deliveryEndpoint->updateParameters($address, $shippingDuration);
        $response = $this->deliveryEndpoint->call();

        if (!is_object($response) || !isset($response->DeliveryDate)) {
            return __('Invalid GetDeliveryDate response: %1', var_export($response, true));
        }

        $this->checkoutSession->setPostNLDeliveryDate($response->DeliveryDate);
        return $response->DeliveryDate;
    }

    private function checkOtherCountries(array $address): ?string
    {
        if (!isset($this->approximateDeliveryTime[$address['country']])) {
            return null;
        }
        $date = $this->timezone->date();
        $shippingDuration = $this->shippingDuration->get();
        if ($shippingDuration > 0) {
            $shippingDuration --;
        }
        $shippingDuration += $this->approximateDeliveryTime[$address['country']];

        $interval = new \DateInterval('P'.(int)$shippingDuration.'D');
        $date->add($interval);
        return $date->format('d-m-Y');
    }

    public function advanceDisabledPickupDate(string $initialDate): string
    {
        try {
            $day = new \DateTime($initialDate);
            $updated = true;
            $iteration = 0;
            // Need to repeat days validations in case any day change is affected.
            while ($updated) {
                $updated = false;
                $iteration++;
                foreach ($this->daysFilter as $filter) {
                    $updated |= $filter->skip($day);
                }
                if ($iteration > 10) {
                    // In case someone configures days incorrectly - prevent loop
                    break;
                }
            }
            return $day->format('d-m-Y');
        } catch (\Exception $e) {
            return $initialDate;
        }
    }

}
