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
use Magento\Quote\Model\Quote\Item as QuoteItem;

class CheckIfQuoteItemsCanBackorder
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
     * @var StockConfigurationInterface
     */
    private $stockConfiguration;

    /**
     * @var null | bool
     */
    private $itemsCanBackorder = null;

    /**
     * @param CheckoutSession             $checkoutSession
     * @param StockRegistryInterface      $stockRegistryInterface
     * @param StockConfigurationInterface $stockConfiguration
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        StockRegistryInterface $stockRegistryInterface,
        StockConfigurationInterface $stockConfiguration
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->stockRegistry = $stockRegistryInterface;
        $this->stockConfiguration = $stockConfiguration;
    }

    /**
     * @return bool
     */
    public function getValue()
    {
        $quote = $this->checkoutSession->getQuote();
        $items = $quote->getAllItems();

        return $this->itemsCanBackorder($items);
    }

    /**
     * @param $items
     *
     * @return bool
     */
    private function itemsCanBackorder($items)
    {
        if ($this->itemsCanBackorder !== null) {
            return $this->itemsCanBackorder;
        }

        $items = array_filter($items, function (QuoteItem $item) {
            $product = $item->getProduct();

            if ($product->getTypeId() != 'simple') {
                return false;
            }

            return !$this->canItemBackorder($item);
        });

        $this->itemsCanBackorder = empty($items);
        return $this->itemsCanBackorder;
    }

    /**
     * @param $item
     *
     * @return bool
     */
    private function canItemBackorder($item)
    {
        $stockItem = $this->getStockItem($item);
        return $this->useConfigBackOrders($stockItem);
    }

    /**
     * @param StockItemInterface $stockItem
     *
     * @return bool
     */
    private function useConfigBackOrders(StockItemInterface $stockItem)
    {
        if ($stockItem->getUseConfigBackorders()) {
            return $this->stockConfiguration->getBackorders() !== 0;
        }

        return $stockItem->getBackorders() !== 0;
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
