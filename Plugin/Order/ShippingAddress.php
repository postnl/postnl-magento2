<?php

namespace TIG\PostNL\Plugin\Order;

use \TIG\PostNL\Model\OrderRepository;

class ShippingAddress
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * ShippingAddress constructor.
     *
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        OrderRepository $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Because Magento does not react to custom address types, we could not name the PG address type as 'pg_address'
     * If we did Magento would totaly ignore it when calling upon the shippingAddresses.
     *
     * This fix makes sure that the PG address is used if set to the order and exists.
     * (Contribution : Mark van der Werf)
     *
     * @param \Magento\Sales\Model\Order $subject
     * @param \Magento\Sales\Model\Order\Address|null $result
     *
     * @return \Magento\Sales\Model\Order\Address|null|bool
     */
    public function afterGetShippingAddress($subject, $result)
    {
        if (!$subject->getId() || !$this->isPostNLShipment($subject)) {
            return $result;
        }

        $postnlOrder = $this->orderRepository->getByOrderId($subject->getId());
        if (!$postnlOrder || !$postnlOrder->getPgOrderAddressId()) {
            return $result;
        }

        $pgAddres = $this->getAddressById($subject, $postnlOrder->getPgOrderAddressId());
        if (!$pgAddres) {
            return $result;
        }

        return $pgAddres;
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     * @param int|string                 $addressId
     *
     * @return \Magento\Sales\Model\Order\Address|false
     */
    private function getAddressById($order, $addressId)
    {
        foreach ($order->getAddresses() as $address) {
            if ($address->getId() == $addressId) {
                return $address;
            }
        }

        return $order->getAddressById($addressId);
    }

    /**
     * @param \Magento\Sales\Model\Order $order
     *
     * @return bool
     */
    private function isPostNLShipment($order)
    {
        $method = $order->getShippingMethod();
        return $method === 'tig_postnl_regular';
    }
}
