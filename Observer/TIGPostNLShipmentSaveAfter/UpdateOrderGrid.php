<?php

namespace TIG\PostNL\Observer\TIGPostNLShipmentSaveAfter;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\ResourceModel\GridInterface;
use TIG\PostNL\Api\OrderRepositoryInterface;
use TIG\PostNL\Model\Shipment;

class UpdateOrderGrid implements ObserverInterface
{
    /**
     * @var GridInterface
     */
    private $orderGrid;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepositoryInterface;

    /**
     * @param OrderRepositoryInterface $orderRepositoryInterface
     * @param GridInterface            $orderGrid
     */
    public function __construct(
        OrderRepositoryInterface $orderRepositoryInterface,
        GridInterface $orderGrid
    ) {
        $this->orderRepositoryInterface = $orderRepositoryInterface;
        $this->orderGrid = $orderGrid;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Shipment $shipment */
        $shipment = $observer->getData('data_object');

        $this->updateOrder($shipment);
        $this->orderGrid->refresh($shipment->getOrderId());
    }

    /**
     * @param Shipment $shipment
     */
    private function updateOrder(Shipment $shipment)
    {
        /** @var \TIG\PostNL\Model\Order $order */
        $order = $this->orderRepositoryInterface->getByFieldWithValue('order_id', $shipment->getOrderId());

        if (!$order) {
            return;
        }

        $productCode = $shipment->getProductCode() ?: $order->getProductCode();

        $data = $order->getData();
        $data['ship_at']        = $shipment->getShipAt();
        $data['product_code']   = $productCode;
        $data['confirmed']      = $shipment->getConfirmedAt() ? 1 : 0;

        $order->setData($data);

        $this->orderRepositoryInterface->save($order);
    }
}
