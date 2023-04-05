<?php

namespace TIG\PostNL\Service\Order;

use \Magento\Sales\Api\OrderRepositoryInterface;
use \Magento\Quote\Api\CartRepositoryInterface;

class MagentoOrder
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * MagentoOrder constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        CartRepositoryInterface $cartRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->cartRepository  = $cartRepository;
    }

    /**
     * @param $identifier
     * @param string $type
     *
     * @return \Magento\Sales\Api\Data\OrderInterface|\Magento\Quote\Api\Data\CartInterface
     */
    public function get($identifier, $type = 'order')
    {
        if ($type !== 'order') {
            return $this->cartRepository->get($identifier);
        }
        return $this->orderRepository->get($identifier);
    }

    /**
     * @param $identifier
     * @param string $type
     *
     * @return null|string
     */
    public function getCountry($identifier, $type = 'order')
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->get($identifier, $type);
        if (!$order) {
            return null;
        }

        $address = $order->getShippingAddress();
        if (!$address) {
            return null;
        }

        return $address->getCountryId();
    }

    /**
     * @param $identifier
     * @param string $type
     *
     * @return \Magento\Sales\Model\Order\Address|null
     */
    public function getShippingAddress($identifier, $type = 'order')
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->get($identifier, $type);
        if (!$order) {
            return null;
        }

        return $order->getShippingAddress();
    }
}
