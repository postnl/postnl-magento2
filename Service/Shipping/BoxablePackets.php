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
namespace TIG\PostNL\Service\Shipping;

use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Sales\Model\Order\Item as OrderItem;
use TIG\PostNL\Service\Shipment\EpsCountries;

class BoxablePackets extends LetterboxPackage {

    /**
     * @param QuoteItem[]|OrderItem[]|ShipmentItemInterface[] $products
     * @param $isPossibleLetterboxPackage
     *
     * @return bool
     */
    public function isBoxablePacket($products, $isPossibleLetterboxPackage)
    {
        if ($this->shippingOptions->isBoxablePacketsActive() === false) {
            return false;
        }

        //only when send from NL
        $senderAddressCountry = $this->addressConfiguration->getCountry();
        if ($senderAddressCountry != 'NL') {
            return false;
        }

        $this->totalVolume                 = 0;
        $this->totalWeight                 = 0;
        $this->hasMaximumQty               = true;

        $calculationMode = $this->pepsConfiguration->getBoxablePacketCalculationMode();

        // If the order is not a letterbox package but it could be we want to return true so the shipment type comment is updated on the order grid.
        if ($calculationMode === 'manually' && !$isPossibleLetterboxPackage) {
            return false;
        }

        // When a configurable product is added Magento adds both the configurable and the simple product so we need to
        // filter the configurable product out for the calculation.
        $products = $this->filterOutConfigurableProducts($products);

        $productIds = [];
        foreach ($products as $product) {
            $productIds[$product->getProductId()] = $product->getTotalQty();
        }

        $productCollection = $this->productCollectionFactory->create();
        $productCollection->addFieldToFilter('entity_id', ['in' => array_keys($productIds)]);
        $productCollection->addAttributeToSelect('postnl_max_qty_letterbox');

        foreach ($productCollection->getItems() as $product) {
            // $productIds[$product->getId()] contains the qty, seen in the previous foreach

            $this->fitsLetterboxPackage($product, $productIds[$product->getId()]);
        }

        // check if all products fit in a letterbox package and the weight is equal or lower than 2 kilograms.
        if ($this->totalVolume <= 1 && $this->totalWeight <= $this->maximumWeight && $this->hasMaximumQty == true) {
            return true;
        }

        return false;
    }

    /**
     * @param \TIG\PostNL\Model\Order $order
     *
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isPossibleBoxablePacket($order)
    {
        $magentoOrder = $this->orderRepository->get($order->getQuoteId());
        $products = $magentoOrder->getAllItems();
        $shippingAddress = $order->getShippingAddress();

        if ($order->getProductCode() == '3085' &&
            $this->isBoxablePacket($products, true) &&
            in_array($shippingAddress->getCountryId(), EpsCountries::ALL)
        ) {
            return true;
        }

        return false;
    }
}
