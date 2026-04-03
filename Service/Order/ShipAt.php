<?php
declare(strict_types=1);

namespace TIG\PostNL\Service\Order;

use Exception;
use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Config\Source\General\PickupCountries;
use TIG\PostNL\Service\Wrapper\QuoteInterface;
use TIG\PostNL\Webservices\Api\DeliveryDateFallback;
use TIG\PostNL\Webservices\Endpoints\SentDate;
use function in_array;

class ShipAt
{
    private QuoteInterface $quote;

    private SentDate $sentDate;

    private DeliveryDateFallback $deliveryDateFallback;

    private array $internationalPickupCountries = [
        PickupCountries::COUNTRY_DE,
        PickupCountries::COUNTRY_FR,
        PickupCountries::COUNTRY_DK,
    ];

    public function __construct(
        QuoteInterface $quote,
        SentDate $endpoint,
        DeliveryDateFallback $deliveryDateFallback
    ) {
        $this->quote = $quote;
        $this->sentDate = $endpoint;
        $this->deliveryDateFallback = $deliveryDateFallback;
    }

    /**
     * GetSentDate calls could break during holidays, but this variable is only used to inform merchants.
     * It shouldn't break the shipping flow. This is the reason why $sentDate is set to null on failure. #POSTNLM2-1012
     *
     * @return OrderInterface|null
     */
    public function set(OrderInterface $order)
    {
        $address = $this->quote->getShippingAddress();

        if (!$address || !$order) {
            return null;
        }

        if (!$order->getDeliveryDate()) {
            return null;
        }

        // The SentDate API only supports NL and BE addresses. For international pickup
        // countries (DE, FR, DK) it falls back to an NL postal code (2521CA), which
        // produces a shipping date unrelated to the actual handover day. Instead, we
        // derive the ship-at date directly: the first valid shipping day from today.
        if (in_array($address->getCountryId(), $this->internationalPickupCountries, true)) {
            $order->setShipAt($this->deliveryDateFallback->get());

            return $order;
        }

        $storeId = $this->quote->getStoreId();
        $this->sentDate->updateParameters($address, $storeId, $order);

        try {
            $sentDate = $this->sentDate->call();
        } catch (Exception) {
            $sentDate = null;
        }

        $order->setShipAt($sentDate);

        return $order;
    }
}
