<?php
declare(strict_types=1);

namespace TIG\PostNL\Service\Handler;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Model\Order;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Webservices\Api\DeliveryDateFallback;

class SentDateHandler
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly DeliveryDateFallback $deliveryDateFallback
    ) {
    }

    /**
     * @throws NoSuchEntityException
     */
    public function get(Shipment $shipment): ?string
    {
        /** @var Order $postnlOrder */
        $postnlOrder = $this->orderRepository->getByFieldWithValue('order_id', $shipment->getOrderId());

        if (!$postnlOrder) {
            return null;
        }

        return $this->deliveryDateFallback->getShipAtDate();
    }
}
