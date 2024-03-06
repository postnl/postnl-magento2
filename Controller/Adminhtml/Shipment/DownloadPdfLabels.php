<?php

namespace TIG\PostNL\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\NoSuchEntityException;
use TIG\PostNL\Api\ShipmentLabelRepositoryInterface;
use TIG\PostNL\Controller\Adminhtml\PdfDownload;

class DownloadPdfLabels extends Action
{
    private ShipmentLabelRepositoryInterface $shipmentLabelRepository;
    private PdfDownload $pdf;
    private SearchCriteriaBuilder $criteriaBuilder;

    public function __construct(
        Context $context,
        ShipmentLabelRepositoryInterface $shipmentLabelRepository,
        PdfDownload $pdf,
        SearchCriteriaBuilder $criteriaBuilder
    ) {
        parent::__construct($context);
        $this->shipmentLabelRepository = $shipmentLabelRepository;
        $this->pdf = $pdf;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    public function execute()
    {
        try {
            $labelIds = (string)$this->getRequest()->getParam('ids');
            $labelIds = explode(',', $labelIds);
            if (empty($labelIds)) {
                throw new NoSuchEntityException();
            }

            $fileName = (string)$this->getRequest()->getParam('name');

            $searchCriteria = $this->criteriaBuilder
                ->addFilter('entity_id', $labelIds, 'IN')
                ->create();

            $labels = $this->shipmentLabelRepository->getList($searchCriteria)->getItems();
            if (empty($labels)) {
                throw new NoSuchEntityException();
            }

            return $this->pdf->get($labels, $fileName);
        } catch (NoSuchEntityException $e) {
            return $this->pdf->emptyResponse();
        }
    }
}
