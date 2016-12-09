<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Sales\Model\Order\Shipment;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Sales\Model\Convert\Order as ConvertOrder;

class CreateShipments extends Action
{
    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var ConvertOrder
     */
    protected $convertOrder;

    /**
     * @var Shipment
     */
    protected $shipment;

    /**
     * @var Order
     */
    protected $currentOrder;

    /**
     * @param Context           $context
     * @param Filter            $filter
     * @param CollectionFactory $collectionFactory
     * @param ConvertOrder      $convertOrder
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        ConvertOrder $convertOrder
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->convertOrder = $convertOrder;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function execute()
    {
        $collection = $this->collectionFactory->create();
        $collection = $this->filter->getCollection($collection);

        /** @var Order $order */
        foreach ($collection as $order) {
            $this->currentOrder = $order;
            $this->createShipment();
        }
    }

    /**
     * @return bool
     */
    protected function orderHasShipment()
    {
        $collection = $this->currentOrder->getShipmentsCollection();
        $size = $collection->getSize();

        return $size !== 0;
    }

    /**
     * @return $this
     */
    protected function createShipment()
    {
        if ($this->isValidOrder()) {
            return $this;
        }

        $this->shipment = $this->convertOrder->toShipment($this->currentOrder);

        /** @var OrderItem $item */
        foreach ($this->currentOrder->getAllItems() as $item) {
            $this->handleItem($item);
        }

        return $this;
    }

    /**
     * @return bool
     */
    protected function orderIsValid()
    {
        if ($this->orderHasShipment()) {
            return false;
        }

        if (!$this->currentOrder->canShip()) {
            //            throw new \Magento\Framework\Exception\LocalizedException(
            //                __('You can\'t create an shipment.')
            //            );

            return false;
        }

        return true;
    }

    protected function handleItem(OrderItem $item)
    {
        // Check if order item has qty to ship or is virtual
        if (!$item->getQtyToShip() || $item->getIsVirtual()) {
            return;
        }

        $qtyShipped = $item->getQtyToShip();

        // Create shipment item with qty
        $shipmentItem = $this->convertOrder->itemToShipmentItem($item);
        $shipmentItem->setQty($qtyShipped);

        // Add shipment item to shipment
        $this->shipment->addItem($shipmentItem);
    }
}
