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
namespace TIG\PostNL\Service\Quote;

use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Checkout\Model\Session;
use Magento\Checkout\Model\Session\Proxy as CheckoutSession;
use Magento\Inventory\Model\SourceItem\Command\GetSourceItemsBySku;
use Magento\Quote\Model\Quote\Item as QuoteItem;

class CheckIfQuoteItemsAreInStock
{
    /**
     * @var Session
     */
    private $checkoutSession;

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
     * @var GetSourceItemsBySku
     */
    private $getSourceItemsBySku;

    /**
     * @param CheckoutSession             $checkoutSession
     * @param StockRegistryInterface      $stockRegistryInterface
     * @param StockConfigurationInterface $stockConfiguration
     * @param GetSourceItemsBySku         $getSourceItemsBySku
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        StockRegistryInterface $stockRegistryInterface,
        StockConfigurationInterface $stockConfiguration,
        GetSourceItemsBySku $getSourceItemsBySku
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->stockRegistry = $stockRegistryInterface;
        $this->stockConfiguration = $stockConfiguration;
        $this->getSourceItemsBySku = $getSourceItemsBySku;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getValue()
    {
        $quote = $this->checkoutSession->getQuote();
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
        $sources = $this->getSourceItemsBySku->execute($item->getSku());
        $sourceQty = 0;

        foreach ($sources as $source) {
            $sourceQty+= $source->getQuantity();
        }

        if ($stockItem->getId() && $stockItem->getManageStock() == false) {
            return true;
        }

        /**
         * Check if the product has MSI quantity available.
         */
        if (($sourceQty - $minimumQuantity) > $requiredQuantity) {
            return true;
        }

        /**
         * Check if the product has the required quantity available.
         */
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
}
