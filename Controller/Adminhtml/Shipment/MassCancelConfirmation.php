<?php

namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Model\Order\Shipment;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Sales\Model\ResourceModel\Order\Shipment\CollectionFactory as ShipmentCollectionFactory;
use TIG\PostNL\Service\Shipment\ResetPostNLShipment;

class MassCancelConfirmation extends Action
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var ShipmentCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var ResetPostNLShipment
     */
    private $resetService;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @param Context                     $context
     * @param Filter                      $filter
     * @param ShipmentCollectionFactory   $collectionFactory
     * @param ResetPostNLShipment         $resetService
     */
    public function __construct(
        Context $context,
        Filter $filter,
        ShipmentCollectionFactory $collectionFactory,
        ResetPostNLShipment $resetService
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->resetService = $resetService;
    }
    
    /**
     * Dispatch request
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $collection = $this->collectionFactory->create();
        $collection = $this->filter->getCollection($collection);

        /** @var Shipment $shipment */
        foreach ($collection as $shipment) {
            $this->resetService->resetShipment($shipment->getId());
        }

        $this->handleErrors();

        return $this->redirectBack();
    }

    /**
     * @return $this
     */
    private function handleErrors()
    {
        foreach ($this->errors as $error) {
            $this->messageManager->addErrorMessage($error);
        }

        return $this;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    private function redirectBack()
    {
        $redirectPath = 'sales/shipment/index';

        $resultRedirect = $this->resultRedirectFactory->create();

        $resultRedirect->setPath($redirectPath);

        return $resultRedirect;
    }
}
