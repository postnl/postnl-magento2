<?php

namespace TIG\PostNL\Service\Shipping;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\AbstractModel;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Store\Model\ScopeInterface;
use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Config\Provider\PepsConfiguration;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Config\Provider\AddressConfiguration;

// @codingStandardsIgnoreFile
class LetterboxPackage
{
    protected const ATTRIBUTE_KEY = 'postnl_max_qty_letterbox';

    public float $totalVolume = 0;
    public float $totalWeight = 0;
    public bool $hasMaximumQty = true;
    public float $maximumWeight = 2;

    protected ScopeConfigInterface $scopeConfig;

    protected PepsConfiguration $pepsConfiguration;

    protected AddressConfiguration $addressConfiguration;

    protected OrderRepositoryInterface $orderRepository;

    protected ShippingOptions $shippingOptions;
    private ShippingDataProvider $shippingDataProvider;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        OrderRepositoryInterface $orderRepository,
        ShippingDataProvider $shippingDataProvider,
        ShippingOptions $shippingOptions,
        PepsConfiguration $pepsConfiguration,
        AddressConfiguration $addressConfiguration
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->orderRepository = $orderRepository;
        $this->shippingDataProvider = $shippingDataProvider;
        $this->shippingOptions = $shippingOptions;
        $this->pepsConfiguration = $pepsConfiguration;
        $this->addressConfiguration = $addressConfiguration;
    }

    public function canFixInTheBox(array $products): bool
    {
        return $this->isLetterboxPackage($products);
    }

    /**
     * @param QuoteItem[]|OrderItem[]|ShipmentItemInterface[] $products
     * @param bool $isPossibleLetterboxPackage
     *
     * @return bool
     */
    public function isLetterboxPackage(array $products, bool $isPossibleLetterboxPackage = false): bool
    {
        if (!$this->isEnabled($isPossibleLetterboxPackage)) {
            return false;
        }
        $this->totalVolume                 = 0;
        $this->totalWeight                 = 0;
        $this->hasMaximumQty               = true;

        // When a configurable product is added Magento adds both the configurable and the simple product so we need to
        // filter the configurable product out for the calculation.
        $products = $this->filterOutConfigurableProducts($products);

        $productIds = [];
        foreach ($products as $product) {
            $productIds[$product->getProductId()] = $product->getTotalQty();
        }
        $collection = $this->shippingDataProvider->loadCollection(array_keys($productIds));

        foreach ($collection->getItems() as $product) {
            // $productIds[$product->getId()] contains the qty, seen in the previous foreach

            $this->fitsLetterboxPackage($product, $productIds[$product->getId()], static::ATTRIBUTE_KEY);
        }

        // check if all products fit in a letterbox package and the weight is equal or lower than 2 kilograms.
        if ($this->totalVolume <= 1 && $this->totalWeight <= $this->maximumWeight && $this->hasMaximumQty) {
            return true;
        }

        return false;
    }

    /**
     * @param AbstractModel|ProductInterface $product
     * @param float $qty
     * @param string $attributeKey
     *
     * Based on the product attribute postnl_max_qty_letterbox (or similar), a percentage
     * is calculated for each product. If, for example, the attribute is set
     * to 4, each product will weight 25%. If the products in the cart
     * have a total weight of over 100%, the order will not fit as a letterbox.
     */
    public function fitsLetterboxPackage($product, $qty, string $attributeKey): void
    {
        $maximumQtyLetterbox = (float)$product->getData($attributeKey);

        if ($maximumQtyLetterbox < PHP_FLOAT_EPSILON) {
            $this->hasMaximumQty = false;
            return;
        }

        $this->totalVolume += 1 / $maximumQtyLetterbox * $qty;
        $this->getTotalWeight($product, $qty);
    }

    /**
     * @param AbstractModel|ProductInterface $product
     * @param float $orderedQty
     */
    public function getTotalWeight($product, $orderedQty): void
    {
        $weightUnit = $this->scopeConfig->getValue(
            'general/locale/weight_unit',
            ScopeInterface::SCOPE_STORE
        );

        // maximum weight for a letterbox package is 4.4 in lbs
        if ($weightUnit === 'lbs') {
            $this->maximumWeight = 4.4;
        }

        $this->totalWeight += $product->getWeight() * $orderedQty;
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isPossibleLetterboxPackage(OrderInterface $order): bool
    {
        $magentoOrder = $this->orderRepository->get($order->getQuoteId());
        $shippingAddress = $order->getShippingAddress();

        if ($order->getProductCode() == '3085' &&
            $shippingAddress->getCountryId() === 'NL' &&
            $this->isLetterboxPackage($magentoOrder->getAllItems(), true)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param $products
     *
     * @return mixed
     */
    public function filterOutConfigurableProducts($products)
    {
        foreach($products as $key => $product) {
            if ($product->getProductType() === 'configurable') {
                unset($products[$key]);
            }
        }

        return $products;
    }

    protected function isEnabled(bool $isPossibleLetterboxPackage = false): bool
    {
        // If the order is not a letterbox package but it could be we want to return true so the shipment type comment is updated on the order grid.
        if (!$isPossibleLetterboxPackage && $this->shippingOptions->isLetterboxPackageActive() === false) {
            return false;
        }

        return true;
    }
}
