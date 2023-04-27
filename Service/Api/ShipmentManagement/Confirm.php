<?php

namespace TIG\PostNL\Service\Api\ShipmentManagement;

use Magento\Framework\Webapi\Exception;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Shipment\ConfirmLabel;

class Confirm
{
    /** @var ShipmentRepositoryInterface */
    private $postnlShipmentRepository;

    /** @var ConfirmLabel */
    private $confirmLabel;

    /** @var Track */
    private $track;

    /**
     * @param ShipmentRepositoryInterface $postnlShipmentRepository
     * @param ConfirmLabel                $confirmLabel
     * @param Track                       $track
     */
    public function __construct(
        ShipmentRepositoryInterface $postnlShipmentRepository,
        ConfirmLabel $confirmLabel,
        Track $track
    ) {
        $this->postnlShipmentRepository = $postnlShipmentRepository;
        $this->confirmLabel = $confirmLabel;
        $this->track = $track;
    }

    /**
     * @param int $shipmentId
     *
     * @return void
     * @throws Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function confirm($shipmentId)
    {
        $postnlShipment = $this->postnlShipmentRepository->getByShipmentId($shipmentId);

        $this->confirmLabel->confirm($postnlShipment);

        $magentoShipment = $postnlShipment->getShipment();

        if (!$magentoShipment->getTracks()) {
            $this->track->set($magentoShipment);
        }
    }
}
