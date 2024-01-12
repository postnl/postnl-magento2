<?php

namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use TIG\PostNL\Config\Source\Options\ProductOptions;
use TIG\PostNL\Controller\Adminhtml\ToolbarAbstract;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Api\OrderRepositoryInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use TIG\PostNL\Service\Shipment\ProductOptions as ShipmentProductOptions;
use TIG\PostNL\Service\Shipment\ResetPostNLShipment;
use Magento\Sales\Model\Order\Shipment;

class MassChangeMulticolli extends ToolbarAbstract
{
    /**
     * @var ShipmentCollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        Context $context,
        Filter $filter,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderRepositoryInterface $orderRepository,
        ShipmentCollectionFactory $collectionFactory,
        ShipmentProductOptions $productOptions,
        ResetPostNLShipment $resetPostNLShipment,
        ProductOptions $options
    ) {
        parent::__construct(
            $context,
            $filter,
            $shipmentRepository,
            $orderRepository,
            $productOptions,
            $resetPostNLShipment,
            $options
        );

        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $collection     = $this->collectionFactory->create();
        $collection     = $this->uiFilter->getCollection($collection);
        $newParcelCount = $this->getRequest()->getParam(self::PARCELCOUNT_PARAM_KEY);

        $this->changeMultiColli($collection, $newParcelCount);

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/shipment/index');
        return $resultRedirect;
    }

    /**
     * @param AbstractDb $collection
     * @param $newParcelCount
     */
    private function changeMultiColli($collection, $newParcelCount)
    {
        /** @var Shipment $shipment */
        foreach ($collection as $shipment) {
            $this->orderChangeParcelCount($shipment->getOrder(), $newParcelCount);
        }

        $this->handelErrors();

        $count = $this->getTotalCount($collection->getSize());
        if ($count > 0) {
            $this->messageManager->addSuccessMessage(
                __('Parcel count changed for %1 shipment(s)', $count)
            );
        }
    }
}
