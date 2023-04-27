<?php

namespace TIG\PostNL\Service\Shipment\Barcode;

use TIG\PostNL\Model\ShipmentBarcodeRepository;
use TIG\PostNL\Api\Data\ShipmentBarcodeInterface;
use TIG\PostNL\Service\Shipment\ShipmentServiceAbstract;
use Magento\Framework\Api\SearchCriteriaBuilder;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Exception as PostNLException;
use Magento\Sales\Model\Order\ShipmentRepository;
use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;

class DeleteBarcode extends ShipmentServiceAbstract
{
    /**
     * @var ShipmentBarcodeRepository
     */
    private $shipmentBarcodeRepository;

    /**
     * @param ShipmentBarcodeRepository $shipmentBarcodeRepository
     * @param Log                       $log
     * @param PostNLShipmentRepository  $postNLShipmentRepository
     * @param ShipmentRepository        $shipmentRepository
     * @param SearchCriteriaBuilder     $searchCriteriaBuilder
     */
    public function __construct(
        ShipmentBarcodeRepository $shipmentBarcodeRepository,
        Log $log,
        PostNLShipmentRepository $postNLShipmentRepository,
        ShipmentRepository $shipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct(
            $log,
            $postNLShipmentRepository,
            $shipmentRepository,
            $searchCriteriaBuilder
        );

        $this->shipmentBarcodeRepository = $shipmentBarcodeRepository;
    }

    /**
     * Deletes a single barcode.
     *
     * @param ShipmentBarcodeInterface $barcode
     */
    public function delete($barcode)
    {
        try {
            $this->shipmentBarcodeRepository->delete($barcode);
        } catch (PostNLException $exception) {
            $this->logger->alert('Can\'t delete shipment barcode', $exception->getLogMessage());
        }
    }

    /**
     * Deletes all barcodes associated to the PostNL Shipment ID.
     *
     * @param $postNLShipmentId
     */
    public function deleteAllByShipmentId($postNLShipmentId)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter(
            'parent_id',
            $postNLShipmentId
        );

        $barcodes = $this->shipmentBarcodeRepository->getList($searchCriteria->create());

        /** @var ShipmentBarcodeInterface $barcode */
        foreach ($barcodes->getItems() as $barcode) {
            // @codingStandardsIgnoreLine
            $this->delete($barcode);
        }
    }
}
