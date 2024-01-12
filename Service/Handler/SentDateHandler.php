<?php

namespace TIG\PostNL\Service\Handler;

use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Webservices\Endpoints\SentDate;
use TIG\PostNL\Model\Order;

class SentDateHandler
{
    /**
     * @var \TIG\PostNL\Model\ShipmentRepository
     */
    private $orderRepository;

    /**
     * @var SentDate
     */
    private $sentDate;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @param SentDate          $sentDate
     * @param OrderRepository   $orderRepository
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        SentDate $sentDate,
        OrderRepository $orderRepository,
        TimezoneInterface $timezone
    ) {
        $this->sentDate = $sentDate;
        $this->orderRepository = $orderRepository;
        $this->timezone = $timezone;
    }

    /**
     * @param Shipment $shipment
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get(Shipment $shipment)
    {
        /** @var  Order $postnlOrder */
        $postnlOrder = $this->getPostnlOrder($shipment);
        if (!$postnlOrder) {
            return null;
        }

        $this->sentDate->updateParameters($shipment->getShippingAddress(), $shipment->getStoreId(), $postnlOrder);

        try {
            return $this->sentDate->call();
        } catch (\Exception $exception) {
            /**
             * GetSentDate calls could break during holidays, but this variable is only used to inform merchants.
             * It shouldn't break the shipping flow. #POSTNLM2-1012
             */
            return null;
        }
    }

    /**
     * @param Shipment $shipment
     *
     * @return \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getPostnlOrder(Shipment $shipment)
    {
        return $this->orderRepository->getByFieldWithValue('order_id', $shipment->getOrderId());
    }
}
