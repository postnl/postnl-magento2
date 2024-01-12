<?php

namespace TIG\PostNL\Block\Adminhtml\Renderer;

use Magento\Framework\Phrase;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Model\ShipmentRepository;
use TIG\PostNL\Api\Data\ShipmentInterface;

class SmartReturnEmail
{
    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    /**
     * DeepLink constructor.
     *
     * @param ShipmentRepository $shipmentRepository
     */
    public function __construct(
        ShipmentRepository $shipmentRepository
    ) {
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * @param Shipment|string|int $shipment
     *
     * @return string
     */
    public function render($shipment)
    {
        if (!$shipment) {
            return '';
        }

        if ($shipment instanceof Shipment ) {
            $output = $shipment->getSmartReturnEmailSent();
        }else {
            /** @var ShipmentInterface $shipmentModel */
            $shipmentModel = $this->shipmentRepository->getByShipmentId($shipment);
            if (!$shipmentModel){
                return '';
            }
            $output = $shipmentModel->getSmartReturnEmailSent();
        }
        // return a bool based on Smart return email sent
        if (!isset($output)){
            return '';
        }
        if ($output) {
            return '&check;';
        }

        return '&#10539';
    }
}
