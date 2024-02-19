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
    /** @var ShipmentRepositoryInterface */
    private $shipmentRepository;

    /** @var BarcodeHandler */
    private $barcodeHandler;

    /** @var GetLabels */
    private $getLabels;

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
     * @param $shipmentId
     *
     * @return bool
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function generate($shipmentId, $smartReturns = false)
    {
        $postnlShipment = $this->shipmentRepository->getByShipmentId($shipmentId);

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
