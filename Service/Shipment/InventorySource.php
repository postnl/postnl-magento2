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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
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
namespace TIG\PostNL\Service\Shipment;

use Magento\Framework\App\RequestInterface;
use Magento\InventoryApi\Api\GetSourcesAssignedToStockOrderedByPriorityInterface;
use Magento\InventoryCatalogApi\Api\DefaultSourceProviderInterface;
use Magento\InventorySalesApi\Model\StockByWebsiteIdResolverInterface;
use Magento\Sales\Api\Data\ShipmentExtensionFactory;

class InventorySource
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ShipmentExtensionFactory
     */
    private $shipmentExtensionFactory;

    /**
     * @var StockByWebsiteIdResolverInterface
     */
    private $stockByWebsiteIdResolver;

    /**
     * @var GetSourcesAssignedToStockOrderedByPriorityInterface
     */
    private $getSourcesAssignedToStockOrderedByPriority;

    /**
     * @var DefaultSourceProviderInterface
     */
    private $defaultSourceProvider;

    /**
     * InventorySource constructor.
     *
     * @param RequestInterface $request
     * @param ShipmentExtensionFactory $shipmentExtensionFactory
     * @param StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver
     * @param GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedToStockOrderedByPriority
     * @param DefaultSourceProviderInterface $defaultSourceProvider
     */
    public function __construct(
        RequestInterface $request,
        ShipmentExtensionFactory $shipmentExtensionFactory,
        StockByWebsiteIdResolverInterface $stockByWebsiteIdResolver,
        GetSourcesAssignedToStockOrderedByPriorityInterface $getSourcesAssignedToStockOrderedByPriority,
        DefaultSourceProviderInterface $defaultSourceProvider
    ) {
        $this->request = $request;
        $this->shipmentExtensionFactory = $shipmentExtensionFactory;
        $this->stockByWebsiteIdResolver = $stockByWebsiteIdResolver;
        $this->getSourcesAssignedToStockOrderedByPriority = $getSourcesAssignedToStockOrderedByPriority;
        $this->defaultSourceProvider = $defaultSourceProvider;
    }

    /**
     * Code based on \Magento\InventoryShipping\Plugin\Sales\Shipment\AssignSourceCodeToShipmentPlugin::afterCreate
     * Unable to directly call this method because the InventoryShipping method doesn't exist in 2.2
     *
     * @param $shipment
     * @param $order
     *
     * @return mixed
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setSource($shipment, $order)
    {
        $sourceCode = $this->request->getParam('sourceCode');
        if (empty($sourceCode)) {
            $websiteId = $order->getStore()->getWebsiteId();
            $stockId = $this->stockByWebsiteIdResolver->execute((int)$websiteId)->getStockId();
            $sources = $this->getSourcesAssignedToStockOrderedByPriority->execute((int)$stockId);
            //TODO: need ro rebuild this logic | create separate service
            if (!empty($sources) && count($sources) == 1) {
                $sourceCode = $sources[0]->getSourceCode();
            } else {
                $sourceCode = $this->defaultSourceProvider->getCode();
            }
        }
        $shipmentExtension = $shipment->getExtensionAttributes();

        if (empty($shipmentExtension)) {
            $shipmentExtension = $this->shipmentExtensionFactory->create();
        }
        $shipmentExtension->setSourceCode($sourceCode);
        $shipment->setExtensionAttributes($shipmentExtension);

        return $shipment;
    }
}
