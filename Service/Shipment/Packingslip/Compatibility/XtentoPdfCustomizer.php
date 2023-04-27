<?php

namespace TIG\PostNL\Service\Shipment\Packingslip\Compatibility;

use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use TIG\PostNL\Service\Shipment\Packingslip\Factory;
use Xtento\PdfCustomizer\Helper\Data;
use Xtento\PdfCustomizer\Model\Source\TemplateType;

class XtentoPdfCustomizer
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var DataHelperFactoryProxy
     */
    private $dataHelper;

    /**
     * @var GeneratePdfFactoryProxy
     */
    private $pdfGenerator;

    /**
     * XtentoPdfCustomiser constructor.
     *
     * @param OrderRepositoryInterface $orderRepository
     * @param DataHelperFactoryProxy   $dataHelper
     * @param GeneratePdfFactoryProxy  $pdfGenerator
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository,
        DataHelperFactoryProxy $dataHelper,
        GeneratePdfFactoryProxy $pdfGenerator
    ) {
        $this->orderRepository = $orderRepository;
        $this->dataHelper = $dataHelper;
        $this->pdfGenerator = $pdfGenerator;
    }

    /**
     * @param Factory           $factory
     * @param ShipmentInterface $magentoShipment
     *
     * @return mixed
     */
    public function getPdf(ShipmentInterface $magentoShipment)
    {
        $orderId = $magentoShipment->getOrderId();
        $order = $this->orderRepository->get($orderId);

        $xtentoDataHelper = $this->dataHelper->create();
        $template = $xtentoDataHelper->getDefaultTemplate($order, TemplateType::TYPE_SHIPMENT);
        $templateId = $template->getId();

        $generatePdfHelper = $this->pdfGenerator->create();
        $document = $generatePdfHelper->generatePdfForObject('shipment', $magentoShipment->getId(), $templateId);

        return $document['output'];
    }

    /**
     * @return bool
     */
    public function isShipmentPdfEnabled()
    {
        $xtentoDataHelper = $this->dataHelper->create();

        if ($xtentoDataHelper->isEnabled(Data::ENABLE_SHIPMENT)) {
            return true;
        }

        return false;
    }
}
