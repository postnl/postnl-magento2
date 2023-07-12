<?php

namespace TIG\PostNL\Block\Adminhtml\Grid\Shipment;

use TIG\PostNL\Block\Adminhtml\Grid\AbstractGrid;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use TIG\PostNL\Block\Adminhtml\Renderer\ShipmentType as Renderer;
use TIG\PostNL\Model\ShipmentRepository;

class ShipmentType extends AbstractGrid
{
    /**
     * @var Renderer
     */
    private $codeRenderer;

    /**
     * @var ShipmentRepository
     */
    private $shipmentRepository;

    /**
     * @param ContextInterface   $context
     * @param UiComponentFactory $uiComponentFactory
     * @param Renderer           $codeRenderer ,
     * @param ShipmentRepository $shipmentRepository
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Renderer $codeRenderer,
        ShipmentRepository $shipmentRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);

        $this->codeRenderer = $codeRenderer;
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * @param array $item
     *
     * @return string
     */
    // @codingStandardsIgnoreLine
    protected function getCellContents($item)
    {
        $shipment = $this->shipmentRepository->getByShipmentId($item['entity_id']);
        if (!$shipment) {
            return '';
        }

        if ($shipment->getShipmentType()) {
            return $this->codeRenderer->render($shipment->getProductCode(), $shipment->getShipmentType());
        }

        return '';
    }
}
