<?php

namespace TIG\PostNL\Service\Shipping;

use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Webapi\Exception;
use TIG\PostNL\Service\Quote\ShippingDuration;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate as DeliveryDateEndpoint;

class DeliveryDate
{
    private DeliveryDateEndpoint $deliveryEndpoint;
    private Session $checkoutSession;
    private ShippingDuration $shippingDuration;

    public function __construct(
        Session $checkoutSession,
        DeliveryDateEndpoint $deliveryDateEndpoint,
        ShippingDuration $shippingDuration
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->deliveryEndpoint = $deliveryDateEndpoint;
        $this->shippingDuration = $shippingDuration;
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
            return null;
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

}
