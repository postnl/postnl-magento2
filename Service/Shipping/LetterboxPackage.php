<?php

namespace TIG\PostNL\Service\Shipping;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Checkout\Controller\Cart\Add;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Item as QuoteItem;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\Item as OrderItem;
use Magento\Store\Model\ScopeInterface;
use TIG\PostNL\Config\Provider\LetterBoxPackageConfiguration;
use TIG\PostNL\Config\Provider\PepsConfiguration;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Config\Provider\AddressConfiguration;

// @codingStandardsIgnoreFile
class LetterboxPackage
{
    public $totalVolume                 = 0;
    public $totalWeight                 = 0;
    public $hasMaximumQty               = true;
    public $maximumWeight               = 2;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var LetterBoxPackageConfiguration
     */
    protected $letterBoxPackageConfiguration;

    /**
     * @var PepsConfiguration
     */
    protected $pepsConfiguration;

    /**
     * @var AddressConfiguration
     */
    protected $addressConfiguration;

    /**
     * @var OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var CollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var ShippingOptions
     */
    protected $shippingOptions;

    /**
     * LetterboxPackage constructor.
     *
     * @param ScopeConfigInterface          $scopeConfig
     * @param LetterBoxPackageConfiguration $letterBoxPackageConfiguration
     * @param OrderRepositoryInterface      $orderRepository
     * @param CollectionFactory             $productCollectionFactory
     * @param ShippingOptions               $shippingOptions
     * @param PepsConfiguration             $pepsConfiguration
     * @param AddressConfiguration          $addressConfiguration
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        LetterBoxPackageConfiguration $letterBoxPackageConfiguration,
        OrderRepositoryInterface $orderRepository,
        CollectionFactory $productCollectionFactory,
        ShippingOptions $shippingOptions,
        PepsConfiguration $pepsConfiguration,
        AddressConfiguration $addressConfiguration
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->letterBoxPackageConfiguration = $letterBoxPackageConfiguration;
        $this->orderRepository = $orderRepository;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->shippingOptions = $shippingOptions;
        $this->pepsConfiguration = $pepsConfiguration;
        $this->addressConfiguration = $addressConfiguration;
    }

    /**
     * @param QuoteItem[]|OrderItem[]|ShipmentItemInterface[] $products
     * @param $isPossibleLetterboxPackage
     *
     * @return bool
     */
    public function isLetterboxPackage($products, $isPossibleLetterboxPackage)
    {
        if ($this->shippingOptions->isLetterboxPackageActive() === false) {
            return false;
        }

        $this->totalVolume                 = 0;
        $this->totalWeight                 = 0;
        $this->hasMaximumQty               = true;

        $calculationMode = $this->letterBoxPackageConfiguration->getLetterBoxPackageCalculationMode();

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
        $productCollection->addAttributeToSelect(['postnl_max_qty_letterbox', 'postnl_max_qty_international', 'postnl_max_qty_international_letterbox']);

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
     * @param $product
     * @param $qty
     *
     * Based on the product attribute postnl_max_qty_letterbox, a percentage
     * is calculated for each product. If, for example, the attribute is set
     * to 4, each product will weight 25%. If the products in the cart
     * have a total weight of over 100%, the order will not fit as a letterbox.
     */
    public function fitsLetterboxPackage($product, $qty)
    {
        $maximumQtyLetterbox = floatval($product->getPostnlMaxQtyLetterbox());

        if ($maximumQtyLetterbox === 0.0) {
            $this->hasMaximumQty = false;
            return;
        }

        $this->totalVolume += 1 / $maximumQtyLetterbox * $qty;
        $this->getTotalWeight($product, $qty);
    }

    /**
     * @param $product
     * @param $orderedQty
     */
    public function getTotalWeight($product, $orderedQty)
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
     * @param \TIG\PostNL\Model\Order $order
     *
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function isPossibleLetterboxPackage($order)
    {
        $magentoOrder = $this->orderRepository->get($order->getQuoteId());
        $products = $magentoOrder->getAllItems();
        $shippingAddress = $order->getShippingAddress();

        if ($order->getProductCode() == '3085' &&
            $this->isLetterboxPackage($products, true) &&
            $shippingAddress->getCountryId() == 'NL') {
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
}
