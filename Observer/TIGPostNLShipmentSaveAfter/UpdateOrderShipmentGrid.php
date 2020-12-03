<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
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
