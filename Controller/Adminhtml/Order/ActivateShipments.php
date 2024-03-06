<?php
namespace TIG\PostNL\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action\Context;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Service\Shipment\Returns\ActivateReturn;
use TIG\PostNL\Service\Shipment\ShipmentLoader;

class ActivateShipments extends Action
{
    private ActivateReturn $activateReturnAction;
    private Filter $filter;
    private OrderCollectionFactory $collectionFactory;
    private ShipmentLoader $shipmentLoader;

    public function __construct(
        Context $context,
        OrderCollectionFactory $collectionFactory,
        Filter $filter,
        ActivateReturn $activateReturnAction,
        ShipmentLoader $shipmentLoader
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->activateReturnAction = $activateReturnAction;
        $this->collectionFactory = $collectionFactory;
        $this->shipmentLoader = $shipmentLoader;
    }

    /**
     * Dispatch request
     *
     * @return \Magento\Framework\Controller\ResultInterface|ResponseInterface
     */
    public function execute()
    {
        $this->processSelectedOrders();
        return $this->_redirect($this->_redirect->getRefererUrl());
    }

    private function processSelectedOrders()
    {
        $collection = $this->collectionFactory->create();

        try {
            $collection = $this->filter->getCollection($collection);
        } catch (LocalizedException $exception) {
            $this->messageManager->addWarningMessage($exception->getMessage());
            return;
        }
        $this->shipmentLoader->prepareData($collection->getItems());

        /** @var Order $order */
        foreach ($collection as $order) {
            $shipments = $this->shipmentLoader->getShipmentsByOrderId($order->getId());
            foreach ($shipments as $shipment) {
                $this->activateReturnAction->processsShipment($shipment);
            }
        }
    }
}
