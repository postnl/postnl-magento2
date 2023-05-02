<?php

namespace TIG\PostNL\Observer\TIGPostNLShipmentSaveAfter;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\ResourceModel\GridInterface;
use TIG\PostNL\Model\Shipment;

class UpdateOrderShipmentGrid implements ObserverInterface
{
    /**
     * @var GridInterface
     */
    private $shipmentGrid;

    /**
     * @var ScopeConfigInterface
     */
    private $globalConfig;

    /**
     * @param GridInterface        $shipmentGrid
     * @param ScopeConfigInterface $globalConfig
     */
    public function __construct(
        GridInterface $shipmentGrid,
        ScopeConfigInterface $globalConfig
    ) {
        $this->shipmentGrid = $shipmentGrid;
        $this->globalConfig = $globalConfig;
    }

    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function execute(Observer $observer)
    {
        if ($this->globalConfig->getValue('dev/grid/async_indexing')) {
            return;
        }

        /** @var Shipment $shipment */
        $shipment = $observer->getData('data_object');
        $shipmentId = $shipment->getShipmentId();

        $this->shipmentGrid->refresh($shipmentId);
    }
}
