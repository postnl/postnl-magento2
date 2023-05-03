<?php

namespace TIG\PostNL\Service\Shipment;

use TIG\PostNL\Service\Shipment\Track\DeleteTrack;
use TIG\PostNL\Service\Shipment\Label\DeleteLabel;
use TIG\PostNL\Service\Shipment\Barcode\DeleteBarcode;
use TIG\PostNL\Model\Shipment as PostNLShipment;
use TIG\PostNL\Api\ShipmentRepositoryInterface;

class ResetPostNLShipment
{
    /**
     * @var DeleteLabel
     */
    private $labelDeleteHandler;

    /**
     * @var DeleteBarcode
     */
    private $barcodeDeleteHandler;

    /**
     * @var DeleteTrack
     */
    private $trackDeleteHandler;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var ShipmentService
     */
    private $shipmentService;

    /**
     * @param DeleteLabel                 $labelDeleteHandler
     * @param DeleteBarcode               $barcodeDeleteHandler
     * @param DeleteTrack                 $trackDeleteHandler
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param ShipmentService             $shipmentService
     */
    public function __construct(
        DeleteLabel $labelDeleteHandler,
        DeleteBarcode $barcodeDeleteHandler,
        DeleteTrack $trackDeleteHandler,
        ShipmentRepositoryInterface $shipmentRepository,
        ShipmentService $shipmentService
    ) {
        $this->labelDeleteHandler = $labelDeleteHandler;
        $this->barcodeDeleteHandler = $barcodeDeleteHandler;
        $this->trackDeleteHandler = $trackDeleteHandler;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentService = $shipmentService;
    }

    /**
     * Resets the confirmation date to null.
     *
     * @param $shipmentId
     *
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     *
     * @return PostNLShipment
     */
    public function resetShipment($shipmentId)
    {
        $postNLShipment = $this->shipmentRepository->getByShipmentId($shipmentId);

        /** @var PostNLShipment $postNLShipment */
        $postNLShipment->setConfirmedAt(null);
        $postNLShipment->setConfirmed(false);
        $postNLShipment->setMainBarcode(null);
        $this->shipmentService->save($postNLShipment);

        $this->barcodeDeleteHandler->deleteAllByShipmentId($postNLShipment->getId());
        $this->labelDeleteHandler->deleteAllByParentId($postNLShipment->getId());
        $this->trackDeleteHandler->deleteAllByShipmentId($shipmentId);

        return $postNLShipment;
    }
}
