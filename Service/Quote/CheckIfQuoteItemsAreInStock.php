<?php

namespace TIG\PostNL\Service\Quote;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Module\Manager;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Psr\Log\LoggerInterface;
use TIG\PostNL\Service\Order\Compatibility\SourceItemsDataBySkuProxy;

// @codingStandardsIgnoreFile
class CheckIfQuoteItemsAreInStock
{
    /**
     * @var StockRegistryInterface
     */
    private $stockRegistry;

    /**
     * @var null
     */
    private $itemsAreInStock = null;

    /**
     * @var StockConfigurationInterface
     */
    private $stockConfiguration;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var SourceItemsDataBySkuProxy
     */
    private $sourceItemsDataBySkuProxy;

    /**
     * @param StockRegistryInterface      $stockRegistryInterface
     * @param StockConfigurationInterface $stockConfiguration
     * @param LoggerInterface             $logger
     * @param Manager                     $moduleManager
     * @param SourceItemsDataBySkuProxy   $sourceItemsDataBySkuProxy
     */
    public function __construct(
        StockRegistryInterface $stockRegistryInterface,
        StockConfigurationInterface $stockConfiguration,
        LoggerInterface $logger,
        Manager $moduleManager,
        SourceItemsDataBySkuProxy $sourceItemsDataBySkuProxy
    ) {
        $this->stockRegistry = $stockRegistryInterface;
        $this->stockConfiguration = $stockConfiguration;
        $this->logger = $logger;
        $this->moduleManager = $moduleManager;
        $this->sourceItemsDataBySkuProxy = $sourceItemsDataBySkuProxy;
    }

    /**
     * @param $quote
     *
     * @return bool
     */
    public function getValue($quote)
    {
        $items = $quote->getAllItems();

        return $this->itemsAreInStock($items);
    }

    /**
     * Loop over the items and remove all items that have stock. If there are any items left, it means that not all
     * items are in stock so we return false.
     *
     * @param QuoteItem[] $items
     *
     * @return bool
     */
    private function itemsAreInStock($items)
    {
        if ($this->itemsAreInStock !== null) {
            return $this->itemsAreInStock;
        }

        $items = array_filter($items, function (QuoteItem $item) {
            $product = $item->getProduct();

            if ($product->getTypeId() != 'simple') {
                return false;
            }

            return !$this->isItemInStock($item);
        });

        $this->itemsAreInStock = empty($items);
        return $this->itemsAreInStock;
    }

    /**
     * @param QuoteItem $item
     *
     * @return bool
     */
    private function isItemInStock(QuoteItem $item)
    {
        $stockItem = $this->getStockItem($item);
        $requiredQuantity = $this->getRequiredQuantity($item);
        $minimumQuantity = $this->getMinimumQuantity($stockItem);

        if ($stockItem->getId() && $stockItem->getManageStock() == false) {
            return true;
        }

        if ($this->hasMultiStockInventoryStock($item, $requiredQuantity, $minimumQuantity)) {
            return true;
        }

        // Check if the product has the required quantity available.
        if (($stockItem->getQty() - $minimumQuantity) < $requiredQuantity) {
            return false;
        }

        return true;
    }

    /**
     * @param StockItemInterface $stockItem
     *
     * @return float
     */
    private function getMinimumQuantity(StockItemInterface $stockItem)
    {
        if (!$stockItem->getUseConfigMinQty()) {
            return $stockItem->getMinQty();
        }

        return $this->stockConfiguration->getMinQty();
    }

    /**
     * @param QuoteItem $item
     *
     * @return int
     */
    private function getRequiredQuantity(QuoteItem $item)
    {
        $parentItem = $item->getParentItem();
        if ($parentItem) {
            return $parentItem->getQty();
        }

        return $item->getQty();
    }

    /**
     * @param QuoteItem $item
     *
     * @return StockItemInterface
     */
    private function getStockItem(QuoteItem $item)
    {
        $product = $item->getProduct();

        /** @noinspection PhpUndefinedMethodInspection */
        $stockItem = $this->stockRegistry->getStockItem($product->getId(), $product->getStoreId());

        return $stockItem;
    }

    /**
     * @param $item
     *
     * @param $requiredQuantity
     * @param $minimumQuantity
     *
     * @return int|void
     */
    private function hasMultiStockInventoryStock($item, $requiredQuantity, $minimumQuantity)
    {
        if ($this->moduleManager->isEnabled('Magento_InventoryCatalogAdminUi') &&
            method_exists(\Magento\InventoryCatalogAdminUi\Model\GetSourceItemsDataBySku::class, 'execute')
        ) {
            try {
                /** @var SourceItemsDataBySkuProxy $sourceItemsDataBySku */
                $sourceItemsDataBySku = $this->sourceItemsDataBySkuProxy->create();
                $sources = $sourceItemsDataBySku->execute($item->getSku());
            } catch (NoSuchEntityException $noSuchEntityException) {
                $this->logger->critical($noSuchEntityException);

                return false;
            }

            return $this->getStockFromSources($sources, $requiredQuantity, $minimumQuantity);
        }
    }

    private function getStockFromSources($sources, $requiredQuantity, $minimumQuantity)
    {
        $sourceQty = 0;

        foreach ($sources as $source) {
            $sourceQty += $source['quantity'];
        }

        if (($sourceQty - $minimumQuantity) >= $requiredQuantity) {
            return true;
        }

        return false;
    }
}
