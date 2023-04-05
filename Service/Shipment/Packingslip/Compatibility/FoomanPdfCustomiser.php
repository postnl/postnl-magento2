<?php

namespace TIG\PostNL\Service\Shipment\Packingslip\Compatibility;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use TIG\PostNL\Service\Shipment\Packingslip\Factory;

class FoomanPdfCustomiser
{

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var OrderShipmentFactoryProxy
     */
    private $orderShipmentFactoryProxy;

    /**
     * @var ShipmentFactoryProxy
     */
    private $shipmentFactoryProxy;

    /**
     * @var PdfRendererFactoryProxy
     */
    private $pdfRendererFactoryProxy;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ScopeConfigInterface $scopeConfig,
        OrderShipmentFactoryProxy $orderShipmentFactoryProxy,
        ShipmentFactoryProxy $shipmentFactoryProxy,
        PdfRendererFactoryProxy $pdfRendererFactoryProxy
    ) {
        $this->orderRepository = $orderRepository;
        $this->scopeConfig = $scopeConfig;
        $this->orderShipmentFactoryProxy = $orderShipmentFactoryProxy;
        $this->shipmentFactoryProxy = $shipmentFactoryProxy;
        $this->pdfRendererFactoryProxy = $pdfRendererFactoryProxy;
    }

    /**
     * @param Factory $factory
     * @param ShipmentInterface $magentoShipment
     *
     * @return string
     * @throws NotFoundException
     */
    public function getPdf(Factory $factory, ShipmentInterface $magentoShipment)
    {
        $document = $this->getPdfDocument($magentoShipment);
        /** @var \Fooman\PdfCore\Model\PdfRenderer $pdfRenderer */
        $pdfRenderer = $this->pdfRendererFactoryProxy->create();

        $pdfRenderer->addDocument($document);

        if (!$pdfRenderer->hasPrintContent()) {
            throw new NotFoundException(__('Nothing to print'));
        }

        $factory->changeY(500);

        return $pdfRenderer->getPdfAsString();
    }

    /**
     * @param ShipmentInterface $magentoShipment
     *
     * @return \Fooman\PdfCustomiser\Block\OrderShipment|\Fooman\PdfCustomiser\Block\Shipment
     */
    private function getPdfDocument(ShipmentInterface $magentoShipment)
    {
        if ($this->scopeConfig->isSetFlag('sales_pdf/shipment/shipmentuseorder')) {
            $orderId = $magentoShipment->getOrderId();
            $order = $this->orderRepository->get($orderId);
            return $this->orderShipmentFactoryProxy->create(['data' => ['order' => $order]]);
        }
        return $this->shipmentFactoryProxy->create(['data' => ['shipment' => $magentoShipment]]);
    }
}
