<?php

namespace TIG\PostNL\Service\Shipment;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipmentTrackRepositoryInterface;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Magento\Sales\Model\Order\Shipment\Track;
use TIG\PostNL\Api\ShipmentLabelRepositoryInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Config\Provider\ReturnOptions;
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
    private ReturnOptions $returnOptions;
    private TrackFactory $trackFactory;
    private ShipmentTrackRepositoryInterface $trackRepository;
    private SearchCriteriaBuilder $criteriaBuilder;

    public function __construct(
        ShipmentManagement $shipmentManagement,
        GetLabels $getLabels,
        ShipmentRepositoryInterface $shipmentRepository,
        ShipmentLabelRepositoryInterface $shipmentLabelRepository,
        Email $email,
        ReturnOptions $returnOptions,
        TrackFactory $trackFactory,
        ShipmentTrackRepositoryInterface $trackRepository,
        SearchCriteriaBuilder $criteriaBuilder
    ) {
        $this->shipmentManagement = $shipmentManagement;
        $this->getLabels = $getLabels;
        $this->shipmentRepository = $shipmentRepository;
        $this->email = $email;
        $this->shipmentLabelRepository = $shipmentLabelRepository;
        $this->returnOptions = $returnOptions;
        $this->trackFactory = $trackFactory;
        $this->trackRepository = $trackRepository;
        $this->criteriaBuilder = $criteriaBuilder;
    }

    public function processShipmentLabel(ShipmentInterface $magentoShipment): void
    {
        if (!$this->returnOptions->isSmartReturnActive()) {
            throw new LocalizedException(__('Smart Returns are disabled.'));
        }
        $postnlShipment = $this->shipmentRepository->getByShipmentId($magentoShipment->getId());
        // Check if smart returns could be created for this shipping
        if (!$postnlShipment->getConfirmed() && !$postnlShipment->getMainBarcode()) {
            throw new LocalizedException(__('Smart Returns are only active after main barcode is generated.'));
        }

        $this->removeOldSmartShippingLabels($postnlShipment->getEntityId());
        $this->removeOldTrackLabels($magentoShipment->getId());
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

        $track = $this->trackFactory->create();
        $track->setNumber($postnlShipment->getSmartReturnBarcode());
        $track->setCarrierCode('tig_postnl');
        $track->setTitle('PostNL Smart Return');
        $track->setShipment($magentoShipment)
            ->setParentId($magentoShipment->getId())
            ->setOrderId($magentoShipment->getOrderId())
            ->setStoreId($magentoShipment->getStoreId());
        $this->trackRepository->save($track);
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

    private function removeOldTrackLabels(int $shippingId)
    {
        $criteria = $this->criteriaBuilder->addFilter('parent_id', $shippingId)
            ->create();
        $tracks = $this->trackRepository->getList($criteria);
        foreach ($tracks as $track) {
            if ($track->getTitle() === 'PostNL Smart Return') {
                $this->trackRepository->delete($track);
            }
        }
    }

}
