<?php

namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use TIG\PostNL\Api\ShipmentLabelRepositoryInterface;
use TIG\PostNL\Controller\Adminhtml\FileDownload;

class DownloadLabel extends Action
{
    private ShipmentLabelRepositoryInterface $shipmentLabelRepository;
    private FileDownload $fileDownload;

    public function __construct(
        Context $context,
        ShipmentLabelRepositoryInterface $shipmentLabelRepository,
        FileDownload $fileDownload,
    ) {
        parent::__construct($context);
        $this->shipmentLabelRepository = $shipmentLabelRepository;
        $this->fileDownload = $fileDownload;
    }

    public function execute()
    {
        try {
            $labelId = $this->getRequest()->getParam('id');
            $fileName = $this->getRequest()->getParam('name');
            $label = $this->shipmentLabelRepository->getById($labelId);
            return $this->fileDownload->returnFile($label, $fileName);
        } catch (NoSuchEntityException $e) {
            return $this->fileDownload->emptyResponse();
        }
    }
}
