<?php

namespace TIG\PostNL\Service\Shipment;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\ShipmentInterface;
use TIG\PostNL\Api\ShipmentLabelRepositoryInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Controller\Adminhtml\Order\Email;
use TIG\PostNL\Service\Api\ShipmentManagement;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;

class SmartReturnShipmentManager
{
    private ShipmentManagement $shipmentManagement;
    private GetLabels $getLabels;
    private ShipmentRepositoryInterface $shipmentRepository;
    private Email $email;
    private ShipmentLabelRepositoryInterface $shipmentLabelRepository;

    public function __construct(
        ShipmentManagement $shipmentManagement,
        GetLabels $getLabels,
        ShipmentRepositoryInterface $shipmentRepository,
        ShipmentLabelRepositoryInterface $shipmentLabelRepository,
        Email $email
    ) {
        $this->shipmentManagement = $shipmentManagement;
        $this->getLabels = $getLabels;
        $this->shipmentRepository = $shipmentRepository;
        $this->email = $email;
        $this->shipmentLabelRepository = $shipmentLabelRepository;
    }
    public function processShipmentLabel(ShipmentInterface $magentoShipment): void
    {
        $postnlShipment = $this->shipmentRepository->getByShipmentId($magentoShipment->getId());
        // Check if smart returns could be created for this shipping
        if (!$postnlShipment->getConfirmed() && !$postnlShipment->getMainBarcode()) {
            throw new LocalizedException(__('Smart Returns are only active after main barcode is generated.'));
        }

        $this->removeOldSmartShippingLabels($postnlShipment->getEntityId());
        $this->shipmentManagement->generateLabel($magentoShipment->getId(), true);
        $labels = $this->getLabels->get($magentoShipment->getId(), false);

        if (empty($labels)) {
            throw new LocalizedException(__('[POSTNL-0252] - There are no valid labels generated. Please check the logs for more information'));
        }

        $this->email->sendEmail($magentoShipment, $labels);

        // set smart return email sent true
        // Reload object, as current repository doesn't use caches
        $postnlShipment = $this->shipmentRepository->getByShipmentId($magentoShipment->getId());
        $postnlShipment->setSmartReturnEmailSent(true);
        $this->shipmentRepository->save($postnlShipment);
    }

    private function removeOldSmartShippingLabels(int $shipmentId): void
    {
        $labels = $this->shipmentLabelRepository->getByShipmentId($shipmentId);
        foreach ($labels as $label) {
            if ($label->getSmartReturnLabel()) {
                $this->shipmentLabelRepository->delete($label);
            }
        }
    }

}
