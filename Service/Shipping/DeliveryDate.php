<?php

namespace TIG\PostNL\Service\Shipping;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Exception;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use TIG\PostNL\Config\Source\General\PickupCountries;
use TIG\PostNL\Service\Quote\ShippingDuration;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate as DeliveryDateEndpoint;

class DeliveryDate
{
    private DeliveryDateEndpoint $deliveryEndpoint;
    private Session $checkoutSession;
    private ShippingDuration $shippingDuration;

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
        TimezoneInterface $timezone
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->deliveryEndpoint = $deliveryDateEndpoint;
        $this->shippingDuration = $shippingDuration;
        $this->timezone = $timezone;
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
        $interval = new \DateInterval('P'.$this->approximateDeliveryTime[$address['country']].'D');
        $date->add($interval);
        return $date->format('d-m-Y');
    }

}
