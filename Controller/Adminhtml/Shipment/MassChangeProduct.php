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
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Service\Shipment\ProductOptions as ShipmentProductOptions;
use TIG\PostNL\Service\Shipment\ResetPostNLShipment;

class MassChangeProduct extends ToolbarAbstract
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
        $newParcelCount = $this->getRequest()->getParam(self::PRODUCTCODE_PARAM_KEY);
        $timeOption     = $this->getRequest()->getParam(self::PRODUCT_TIMEOPTION);
        $insuredTier    = $this->getRequest()->getParam(self::PRODUCT_INSUREDTIER);

        $this->changeProductCode($collection, $newParcelCount, $timeOption, $insuredTier);

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/shipment/index');
        return $resultRedirect;
    }

    /**
     * @param AbstractDb $collection
     * @param            $newParcelCount
     * @param            $timeOption
     * @param            $insuredTier
     */
    private function changeProductCode($collection, $newParcelCount, $timeOption, $insuredTier)
    {
        /** @var Shipment $shipment */
        foreach ($collection as $shipment) {
            $this->orderChangeProductCode($shipment->getOrder(), $newParcelCount, $timeOption, $insuredTier);
        }

        $this->handelErrors();

        $count = $this->getTotalCount($collection->getSize());
        if ($count > 0) {
            $this->messageManager->addSuccessMessage(
                __('Productcode changed for %1 shipment(s)', $count)
            );
        }
    }
}
