<?php
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
}
