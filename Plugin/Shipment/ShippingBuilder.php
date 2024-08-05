<?php

namespace TIG\PostNL\Plugin\Shipment;

use Magento\Framework\Api\ExtensionAttributesFactory;
use Magento\Sales\Api\Data\ShippingInterface;
use Magento\Sales\Model\Order\ShippingBuilder as SubjectClass;
use TIG\PostNL\Api\Data\ShipmentAttributeInterface;
use TIG\PostNL\Api\Data\ShipmentAttributeInterfaceFactory;
use TIG\PostNL\Api\OrderRepositoryInterface;

class ShippingBuilder
{
    /**
     * @var null|int
     */
    private $orderId = null;

    private ShipmentAttributeInterfaceFactory $shipmentAttributeFactory;

    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ShipmentAttributeInterfaceFactory $shipmentAttributeFactory
    ) {
        $this->shipmentAttributeFactory = $shipmentAttributeFactory;
        $this->orderRepository = $orderRepository;
    }
    public function afterCreate(
        SubjectClass       $subject,
        ?ShippingInterface $shipping
    ): ?ShippingInterface {
        if ($shipping) {
            $method = $shipping->getMethod();
            if ($this->orderId &&
                is_string($method) &&
                strpos($method, 'tig_postnl') !== false &&
                $extensionAttributes = $shipping->getExtensionAttributes()
            ) {
                $postnlOrder = $this->orderRepository->getByOrderId($this->orderId);
                if ($postnlOrder) {
                    /** @var ShipmentAttributeInterface $model */
                    $model = $this->shipmentAttributeFactory->create();

                    $model->setProductCode($postnlOrder->getProductCode())
                        ->setType((string)$postnlOrder->getType())
                        ->setShipAt((string)$postnlOrder->getShipAt())
                        ->setDeliveryDate($postnlOrder->getDeliveryDate())
                        ->setExpectedDeliveryTimeStart($postnlOrder->getExpectedDeliveryTimeStart())
                        ->setExpectedDeliveryTimeEnd($postnlOrder->getExpectedDeliveryTimeEnd());
                    $extensionAttributes->setPostnl($model);
                    $shipping->setExtensionAttributes($extensionAttributes);
                }
            }
        }
        $this->orderId = null;
        return $shipping;
    }

    public function afterSetOrderId(
        SubjectClass $subject,
        $result,
        $orderId
    ) {
        $this->orderId = (int)$orderId;
        return $result;
    }

    public function afterSetOrder(
        SubjectClass $subject,
        $result,
        $order
    ) {
        if ($order) {
            $this->orderId = $order->getId();
        }
        return $result;
    }
}
