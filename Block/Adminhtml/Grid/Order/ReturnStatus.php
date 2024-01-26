<?php

namespace TIG\PostNL\Block\Adminhtml\Grid\Order;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Block\Adminhtml\Grid\AbstractGrid;
use TIG\PostNL\Block\Adminhtml\Grid\Filter\ReturnStatus as ReturnStatusOptions;
use TIG\PostNL\Service\Shipment\ShipmentLoader;

class ReturnStatus extends AbstractGrid
{
    private ShipmentLoader $shipmentLoader;
    private ReturnStatusOptions $returnStatus;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ShipmentLoader $shipmentLoader,
        ReturnStatusOptions $returnStatus,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->shipmentLoader = $shipmentLoader;
        $this->returnStatus = $returnStatus;
    }

    /**
     * Preload all the needed models in 1 query.
     */
    protected function prepareData()
    {
        $this->shipmentLoader->prepareData($this->items);
    }

    /**
     * @param array $item
     *
     * @return string
     */
    protected function getCellContents($item): string
    {
        $entityId = $item['entity_id'];
        $output = '';

        $shipments = $this->shipmentLoader->getShipmentsByOrderId($entityId);
        if (!empty($shipments)) {
            $values = $this->returnStatus->getOptions();
            /** @var ShipmentInterface $model */
            foreach ($shipments as $model) {
                $value = $model->getReturnStatus();
                $output .= ($values[$value] ?? $values[$model::RETURN_STATUS_DEFAULT]) . '<br>';
            }
        }
        return $output;
    }
}
