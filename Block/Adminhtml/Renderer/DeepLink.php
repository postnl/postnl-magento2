<?php

namespace TIG\PostNL\Block\Adminhtml\Renderer;

use TIG\PostNL\Model\ShipmentRepository;
use TIG\PostNL\Api\Data\ShipmentInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use TIG\PostNL\Config\Provider\Webshop;

class DeepLink
{
    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    /**
     * @var Webshop
     */
    private $webshopConfig;

    /**
     * DeepLink constructor.
     *
     * @param ShipmentRepository $shipmentRepository
     * @param Webshop            $webshop
     */
    public function __construct(
        ShipmentRepository $shipmentRepository,
        Webshop $webshop
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->webshopConfig = $webshop;
    }

    /**
     * @param $shipmentId
     *
     * @return null|string
     */
    public function render($shipmentId)
    {
        /** @var ShipmentInterface $shipment */
        $shipment = $this->shipmentRepository->getByShipmentId($shipmentId);
        if (!$shipment) {
            return '';
        }

        // If the shipment hasn't been confirmed yet, the barcode will not be known by PostNL track & trace.
        if (!$shipment->getConfirmed()) {
            return $shipment->getMainBarcode();
        }

        $deeplink = $this->getBarcodeUrl($shipment);
        $output = "<a href='{$deeplink}' target='_blank'>{$shipment->getMainBarcode()}</a>";
        $output .= $this->addJavascript();

        return $output;
    }

    /**
     * Disable click event on barcode columns.
     * @return string
     */
    private function addJavascript()
    {
        // @codingStandardsIgnoreLine
        return "<script type='text/javascript'>jQuery('.tig_barcode_column').unbind('click');</script>";
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return string
     */
    private function getBarcodeUrl($shipment)
    {
        /** @var OrderAddressInterface $address */
        $address = $shipment->getOriginalShippingAddress();

        $params = [
            'B=' . $shipment->getMainBarcode(),
            'D=' . $address->getCountryId(),
            'P=' . str_replace(' ', '', (string)$address->getPostcode()),
            'T=' . 'B', // Business for backend, which will retuns the Mijn PostNL link.
        ];

        return $this->webshopConfig->getTrackAndTraceServiceUrl() . implode('&', $params);
    }
}
