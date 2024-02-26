<?php

namespace TIG\PostNL\Controller\Adminhtml\Order;

use TIG\PostNL\Config\Source\Options\ProductOptions;
use TIG\PostNL\Controller\Adminhtml\ToolbarAbstract;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Api\OrderRepositoryInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use TIG\PostNL\Service\Shipment\ProductOptions as ShipmentProductOptions;
use TIG\PostNL\Service\Shipment\ResetPostNLShipment;

class MassChangeProduct extends ToolbarAbstract
{
    /**
     * @var OrderCollectionFactory
     */
    private $collectionFactory;

    public function __construct(
        Context $context,
        Filter $filter,
        ShipmentRepositoryInterface $shipmentRepository,
        OrderRepositoryInterface $orderRepository,
        ShipmentProductOptions $productOptions,
        ResetPostNLShipment $resetPostNLShipment,
        ProductOptions $options,
        OrderCollectionFactory $collectionFactory
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
        $newProductCode = $this->getRequest()->getParam(self::PRODUCTCODE_PARAM_KEY);
        $timeOption     = $this->getRequest()->getParam(self::PRODUCT_TIMEOPTION);
        $insuredTier    = $this->getRequest()->getParam(self::PRODUCT_INSUREDTIER);

        $this->changeProductCode($collection, $newProductCode, $timeOption, $insuredTier);

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('sales/*/');
        return $resultRedirect;
    }

    /**
     * @param AbstractDb $collection
     * @param            $productCode
     * @param            $timeOption
     * @param            $insuredTier
     */
    private function changeProductCode($collection, $productCode, $timeOption, $insuredTier)
    {
        foreach ($collection as $order) {
            $this->orderChangeProductCode($order, $productCode, $timeOption, $insuredTier);
        }

        $this->handelErrors();

        $count = $this->getTotalCount($collection->getSize());
        if ($count > 0) {
            $this->messageManager->addSuccessMessage(
                __('Productcode changed for %1 order(s)', $count)
            );
        }
    }
}
