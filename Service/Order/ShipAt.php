<?php

namespace TIG\PostNL\Service\Order;

use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Service\Wrapper\QuoteInterface;
use TIG\PostNL\Webservices\Endpoints\SentDate;

class ShipAt
{
    /**
     * @var QuoteInterface
     */
    private $quote;

    /**
     * @var SentDate
     */
    private $sentDate;

    /**
     * @param QuoteInterface $quote
     * @param SentDate       $endpoint
     */
    public function __construct(
        QuoteInterface $quote,
        SentDate $endpoint
    ) {
        $this->quote    = $quote;
        $this->sentDate = $endpoint;
    }

    /**
     * GetSentDate calls could break during holidays, but this variable is only used to inform merchants.
     * It shouldn't break the shipping flow. This is the reason why $sentDate is set to null on failure. #POSTNLM2-1012
     *
     * @param OrderInterface $order
     *
     * @return OrderInterface|null
     */
    public function set(OrderInterface $order)
    {
        $address = $this->quote->getShippingAddress();

        if (!$address || !$order) {
            return null;
        }

        $storeId = $this->quote->getStoreId();
        $this->sentDate->updateParameters($address, $storeId, $order);

        try {
            $sentDate = $this->sentDate->call();
        } catch (\Exception $exception) {
            $sentDate = null;
        }

        $order->setShipAt($sentDate);

        return $order;
    }
}
