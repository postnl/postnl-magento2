<?php

namespace TIG\PostNL\Service\Api\ShipmentManagement;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;

class GenerateLabel
{
    private ShipmentRepositoryInterface $shipmentRepository;

    private BarcodeHandler $barcodeHandler;

    private GetLabels $getLabels;

    public function __construct(
        ShipmentRepositoryInterface $shipmentRepository,
        BarcodeHandler $barcodeHandler,
        GetLabels $getLabels
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->barcodeHandler = $barcodeHandler;
        $this->getLabels = $getLabels;
    }

    /**
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function generate(int $shipmentId, $smartReturns = false): bool
    {
        $postnlShipment = $this->shipmentRepository->getByShipmentId($shipmentId);
        // Check if smart returns could be created for this shipping
        if ($smartReturns) {
            if (!$postnlShipment->getConfirmed() && !$postnlShipment->getMainBarcode()) {
                throw new LocalizedException(__('Smart Returns are only active after main barcode is generated.'));
            }
        }

        /** @var Shipment|ShipmentInterface $shipment */
        $shipment = $postnlShipment->getShipment();
        $shippingAddress = $shipment->getShippingAddress();

        $this->barcodeHandler->prepareShipment($shipment->getId(), $shippingAddress->getCountryId(), $smartReturns);
        $labels = $this->getLabels->get($shipment->getId(), false, true);

        if (empty($labels)) {
            return false;
        }

        return true;
    }
}
