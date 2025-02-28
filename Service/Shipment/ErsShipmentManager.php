<?php

namespace TIG\PostNL\Service\Shipment;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Api\ShipmentLabelRepositoryInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Config\Provider\ReturnOptions;
use TIG\PostNL\Controller\Adminhtml\Order\Email;
use TIG\PostNL\Service\Api\ShipmentManagement;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Service\Shipment\Labelling\Generate\ErsReturn as GenerateLabels;

class ErsShipmentManager
{
    private ShipmentManagement $shipmentManagement;
    private GenerateLabels $getLabels;
    private ShipmentRepositoryInterface $shipmentRepository;
    private ShipmentLabelRepositoryInterface $shipmentLabelRepository;
    private ReturnOptions $returnOptions;
    private BarcodeHandler $barcodeHandler;

    public function __construct(
        ShipmentManagement $shipmentManagement,
        GenerateLabels $getLabels,
        ShipmentRepositoryInterface $shipmentRepository,
        ShipmentLabelRepositoryInterface $shipmentLabelRepository,
        BarcodeHandler$barcodeHandler,
        ReturnOptions $returnOptions
    ) {
        $this->shipmentManagement = $shipmentManagement;
        $this->getLabels = $getLabels;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentLabelRepository = $shipmentLabelRepository;
        $this->barcodeHandler = $barcodeHandler;
        $this->returnOptions = $returnOptions;
    }

    public function processShipmentLabel(ShipmentInterface $magentoShipment): array
    {
        if (!$this->returnOptions->isEasyReturnServiceActive()) {
            throw new LocalizedException(__('Easy Return Services are disabled.'));
        }
        $postnlShipment = $this->shipmentRepository->getByShipmentId($magentoShipment->getId());
        // Check if smart returns could be created for this shipping
        if (!$postnlShipment->getConfirmed() && !$postnlShipment->getMainBarcode()) {
            throw new LocalizedException(__('Easy Return Service are only active after main barcode is generated.'));
        }

        $this->removeOldShippingLabels($postnlShipment->getEntityId());

        /** @var Shipment|ShipmentInterface $shipment */
        $shippingAddress = $magentoShipment->getShippingAddress();
        $this->barcodeHandler->prepareShipment($magentoShipment->getId(), $shippingAddress->getCountryId(), ShipmentLabelInterface::RETURN_LABEL_ERS);
        // Reload object saved above
        $postnlShipment = $this->shipmentRepository->getByShipmentId($magentoShipment->getId());
        $labels = $this->getLabels->get($postnlShipment, 1);

        if (empty($labels)) {
            throw new LocalizedException(__('[POSTNL-0252] - There are no valid labels generated. Please check the logs for more information'));
        }
        foreach ($labels as $label) {
            $this->shipmentLabelRepository->save($label);
        }
        return $labels;
    }

    private function removeOldShippingLabels(int $shipmentId): void
    {
        $labels = $this->shipmentLabelRepository->getByShipmentId($shipmentId);
        foreach ($labels as $label) {
            if ($label->isErsLabelFlag()) {
                $this->shipmentLabelRepository->delete($label);
            }
        }
    }

}
