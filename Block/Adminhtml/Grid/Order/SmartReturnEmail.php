<?php

namespace TIG\PostNL\Block\Adminhtml\Grid\Order;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use TIG\PostNL\Block\Adminhtml\Grid\AbstractGrid;
use TIG\PostNL\Block\Adminhtml\Renderer\SmartReturnEmail as Renderer;
use TIG\PostNL\Service\Shipment\ShipmentLoader;

class SmartReturnEmail extends AbstractGrid
{
    private Renderer $smartReturnEmail;

    private ShipmentLoader $shipmentLoader;

    /**
     * @param ContextInterface            $context
     * @param UiComponentFactory          $uiComponentFactory
     * @param Renderer                    $smartReturnEmail
     * @param ShipmentLoader              $shipmentLoader
     * @param array                       $components
     * @param array                       $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        Renderer $smartReturnEmail,
        ShipmentLoader $shipmentLoader,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->smartReturnEmail      = $smartReturnEmail;
        $this->shipmentLoader        = $shipmentLoader;
    }

    /**
     * Preload all the needed models in 1 query.
     */
    protected function prepareData()
    {
        $this->shipmentLoader->prepareData($this->items);
    }

    /**
     * @param object $item
     *
     * @return null|string
     */
    protected function getCellContents($item)
    {
        $entityId = $item['entity_id'];
        $output = '';

        $shipments = $this->shipmentLoader->getShipmentsByOrderId($entityId);
        if (!empty($shipments)) {
            /** @var \TIG\PostNL\Model\Shipment $model */
            foreach ($shipments as $model) {
                $output .= $this->smartReturnEmail->render($model) . '<br>';
            }
        }
        return $output;
    }
}
