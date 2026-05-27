<?php

namespace TIG\PostNL\Service\Shipping;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\AbstractModel;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Store\Model\ScopeInterface;
use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Config\Provider\AddressConfiguration;
use TIG\PostNL\Config\Provider\PepsConfiguration;
use TIG\PostNL\Config\Provider\ShippingOptions;
use function array_keys;

// @codingStandardsIgnoreFile
class LetterboxPackage
{
    protected const ATTRIBUTE_KEY = 'postnl_max_qty_letterbox';
    protected const PRODUCT_CODE_DOMESTIC = '3085';

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

        $totalVolume = 0.0;
        $totalWeight = 0.0;
        $hasMaximumQty = true;
        $maximumWeight = 2.0;

        $weightUnit = $this->scopeConfig->getValue('general/locale/weight_unit', ScopeInterface::SCOPE_STORE);
        if ($weightUnit === 'lbs') {
            $maximumWeight = 4.4;
        }

        // When a configurable or bundle product is added Magento adds both the parent and the simple product so we
        // need to filter the parent out for the calculation.
        $products = $this->filterOutConfigurableProducts($products);

        $productIds = [];
        foreach ($products as $product) {
            $productIds[$product->getProductId()] = $product->getTotalQty();
        }
        $collection = $this->shippingDataProvider->loadCollection(array_keys($productIds));

        foreach ($collection->getItems() as $product) {
            $this->fitsLetterboxPackage(
                $product,
                $productIds[$product->getId()],
                static::ATTRIBUTE_KEY,
                $totalVolume,
                $totalWeight,
                $hasMaximumQty
            );
        }

        // check if all products fit in a letterbox package and the weight is equal or lower than 2 kilograms.
        return $totalVolume <= 1 && $totalWeight <= $maximumWeight && $hasMaximumQty;
    }

    /**
     * @param AbstractModel|ProductInterface $product
     * @param float $qty
     * @param string $attributeKey
     * @param float $totalVolume
     * @param float $totalWeight
     * @param bool $hasMaximumQty
     *
     * Based on the product attribute postnl_max_qty_letterbox (or similar), a percentage
     * is calculated for each product. If, for example, the attribute is set
     * to 4, each product will weight 25%. If the products in the cart
     * have a total weight of over 100%, the order will not fit as a letterbox.
     */
    public function fitsLetterboxPackage(
        $product,
        $qty,
        string $attributeKey,
        float &$totalVolume,
        float &$totalWeight,
        bool &$hasMaximumQty
    ): void {
        $maximumQtyLetterbox = (float) $product->getData($attributeKey);

        if ($maximumQtyLetterbox < PHP_FLOAT_EPSILON) {
            $hasMaximumQty = false;

            return;
        }

        $totalVolume += 1 / $maximumQtyLetterbox * $qty;
        $this->getTotalWeight($product, $qty, $totalWeight);
    }

    /**
     * @throws InputException
     * @throws NoSuchEntityException
     */
    public function isPossibleLetterboxPackage(OrderInterface $order): bool
    {
        $magentoOrder = $this->orderRepository->get($order->getOrderId());
        $shippingAddress = $order->getShippingAddress();

        return $order->getProductCode() == static::PRODUCT_CODE_DOMESTIC
            && $shippingAddress->getCountryId() === 'NL'
            && $this->isLetterboxPackage($magentoOrder->getAllItems(), true);
    }

    /**
     * @param $products
     *
     * @return mixed
     */
    public function filterOutConfigurableProducts($products)
    {
        foreach ($products as $key => $product) {
            $type = $product->getProductType();
            if ($type === 'configurable' || $type === 'bundle') {
                unset($products[$key]);
            }
        }

        return $products;
    }

    /**
     * @param AbstractModel|ProductInterface $product
     * @param float $orderedQty
     * @param float $totalWeight
     */
    protected function getTotalWeight($product, float $orderedQty, float &$totalWeight): void
    {
        $totalWeight += $product->getWeight() * $orderedQty;
    }

    protected function isEnabled(bool $isPossibleLetterboxPackage = false): bool
    {
        // isPossibleLetterboxPackage=true bypasses the active check so the admin grid can show
        // "possible letterbox package" for standard shipments even when automatic mode is off.
        return $isPossibleLetterboxPackage || $this->shippingOptions->isLetterboxPackageActive();
    }
}
