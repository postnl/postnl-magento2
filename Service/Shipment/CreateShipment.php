<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Service\Shipment;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Convert\Order as ConvertOrder;

class CreateShipment
{
    /**
     * @var ConvertOrder
     */
    private $convertOrder;

    /**
     * @var Order
     */
    private $currentOrder;

    /**
     * @var Shipment
     */
    private $shipment;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @param ConvertOrder $convertOrder
     */
    public function __construct(
        ConvertOrder $convertOrder
    ) {
        $this->convertOrder = $convertOrder;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param Order $order
     *
     * @return Shipment|null
     */
    public function create(Order $order)
    {
        $this->currentOrder = $order;

        if (!$this->isValidOrder()) {
            return null;
        }

        $this->shipment = $this->convertOrder->toShipment($this->currentOrder);

        /** @var OrderItem $item */
        foreach ($this->currentOrder->getAllItems() as $item) {
            $this->handleItem($item);
        }

        $this->saveShipment();

        return $this->shipment;
    }

    /**
     * @return bool
     */
    private function isValidOrder()
    {
        if ($this->orderHasShipment()) {
            return false;
        }

        if (!$this->currentOrder->canShip()) {
            return false;
        }

        if ($this->currentOrder->getShippingMethod() !== 'tig_postnl_regular') {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function orderHasShipment()
    {
        $collection = $this->currentOrder->getShipmentsCollection();
        $size = $collection->getSize();

        return $size !== 0;
    }

    /**
     * @param OrderItem $item
     *
     * @return $this
     */
    private function handleItem(OrderItem $item)
    {
        if (!$item->getQtyToShip() || $item->getIsVirtual()) {
            return $this;
        }

        $qtyShipped = $item->getQtyToShip();

        $shipmentItem = $this->convertOrder->itemToShipmentItem($item);
        $shipmentItem->setQty($qtyShipped);

        $this->shipment->addItem($shipmentItem);

        return $this;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function saveShipment()
    {
        $this->shipment->register();
        $order = $this->shipment->getOrder();
        $order->setState(Order::STATE_PROCESSING);
        $order->setStatus('processing');

        try {
            $this->shipment->save();
            $order->save();
        } catch (\Exception $exception) {
            $message = $exception->getMessage();
            $localizedErrorMessage = __($message)->render();
            $this->errors[] = $localizedErrorMessage;
        }

        return $this;
    }
}
