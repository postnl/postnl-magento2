<?php

namespace TIG\PostNL\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Framework\App\ResponseInterface;
use Magento\Sales\Model\Order;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use TIG\PostNL\Service\Shipment\SmartReturnShipmentManager;

class CreateSmartReturnBarcode extends Action
{
    private Filter $filter;
    private OrderCollectionFactory $collectionFactory;
    private SmartReturnShipmentManager $smartReturnShipmentManager;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param OrderCollectionFactory $collectionFactory
     * @param SmartReturnShipmentManager $smartReturnShipmentManager
     */
    public function __construct(
        Context $context,
        Filter $filter,
        OrderCollectionFactory $collectionFactory,
        SmartReturnShipmentManager $smartReturnShipmentManager
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->smartReturnShipmentManager = $smartReturnShipmentManager;
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
            $shipments = $order->getShipmentsCollection();
            foreach ($shipments as $shipment) {
                $countryId = $shipment->getShippingAddress()->getCountryId();

                if ($countryId !== 'NL') {
                    $this->messageManager->addErrorMessage(
                        __('Smart Returns is only available for NL shipments.')
                    );

                    return $this->redirectBack();
                }

                $this->smartReturnShipmentManager->processShipmentLabel($shipment);
            }
        }

        return $this->redirectBack();
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function redirectBack()
    {
        $redirectPath = 'sales/order/index';
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath($redirectPath);

        return $resultRedirect;
    }
}
