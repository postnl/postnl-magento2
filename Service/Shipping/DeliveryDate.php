<?php

namespace TIG\PostNL\Service\Shipping;

use DateInterval;
use DateTime;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Webapi\Exception;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Config\Source\General\PickupCountries;
use TIG\PostNL\Service\Quote\ShippingDuration;
use TIG\PostNL\Service\Timeframe\Filters\DaysSkipInterface;
use TIG\PostNL\Service\Timeframe\IsPastCutOff;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate as DeliveryDateEndpoint;
use function __;
use function explode;
use function in_array;
use function is_object;

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

    private IsPastCutOff $isPastCutOff;

    private Webshop $webshop;

    public function __construct(
        Session $checkoutSession,
        DeliveryDateEndpoint $deliveryDateEndpoint,
        ShippingDuration $shippingDuration,
        TimezoneInterface $timezone,
        IsPastCutOff $isPastCutOff,
        Webshop $webshop,
        array $daysFilter = []
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->deliveryEndpoint = $deliveryDateEndpoint;
        $this->shippingDuration = $shippingDuration;
        $this->timezone = $timezone;
        $this->isPastCutOff = $isPastCutOff;
        $this->webshop = $webshop;
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
            return __('Invalid GetDeliveryDate response: %1', var_export($response, true))->render();
        }

        $this->checkoutSession->setPostNLDeliveryDate($response->DeliveryDate);

        return $response->DeliveryDate;
    }

    public function advanceDisabledPickupDate(string $initialDate): string
    {
        try {
            $day = new DateTime($initialDate);
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

    private function checkOtherCountries(array $address): ?string
    {
        if (!isset($this->approximateDeliveryTime[$address['country']])) {
            return null;
        }

        $shippingDays = explode(',', $this->webshop->getShipmentDays());
        $date = $this->timezone->date();

        // If today is not a valid shipping day or the cutoff has already passed,
        // advance to the next day before searching for the first valid shipping day.
        $todayDayNumber = $this->normaliseToConfigDayNumber($date->format('N'));
        if (!in_array($todayDayNumber, $shippingDays) || $this->isPastCutOff->calculate()) {
            $date->modify('+1 day');
        }

        // Advance until we land on a configured shipping day (cap at 14 to prevent loops).
        $i = 0;
        while (!in_array($this->normaliseToConfigDayNumber($date->format('N')), $shippingDays) && $i < 14) {
            $date->modify('+1 day');
            $i++;
        }

        // Add any extra warehouse-processing days on top of the first valid shipping day.
        $shippingDuration = $this->shippingDuration->get();
        if ($shippingDuration > 0) {
            $date->modify('+' . (int) $shippingDuration . ' days');
        }

        // Add the international transit days for this country.
        $date->add(new DateInterval('P' . $this->approximateDeliveryTime[$address['country']] . 'D'));

        return $date->format('d-m-Y');
    }

    /**
     * Convert date('N') output (1=Mon … 7=Sun) to the day-number format used in
     * the shipment-days configuration, where Sunday is stored as '0'.
     */
    private function normaliseToConfigDayNumber(string $isoWeekday): string
    {
        if ($isoWeekday === '7') {
            return '0';
        }

        return $isoWeekday;
    }

}
