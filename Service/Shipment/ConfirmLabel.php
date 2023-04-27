<?php

namespace TIG\PostNL\Service\Shipment;

use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Webservices\Endpoints\Confirming;
use TIG\PostNL\Helper\Data as Helper;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Api\Data\ShipmentInterface;

class ConfirmLabel
{
    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var Confirming
     */
    private $confirming;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * ConfirmLabel constructor.
     *
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param Confirming                  $confirming
     * @param Helper                        $data
     */
    public function __construct(
        ShipmentRepositoryInterface $shipmentRepository,
        Confirming $confirming,
        Helper $data
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->confirming = $confirming;
        $this->helper = $data;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param int               $number
     *
     * @throws LocalizedException
     */
    public function confirm(ShipmentInterface $shipment, $number = 1)
    {
        $this->confirming->setParameters($shipment, $number);

        try {
            $this->confirming->call();
        } catch (LocalizedException $exception) {
            throw new LocalizedException(__($exception->getMessage()));
        }

        $shipment->setConfirmedAt($this->helper->getDate());
        $shipment->setConfirmed(true);
        $this->shipmentRepository->save($shipment);
    }
}
