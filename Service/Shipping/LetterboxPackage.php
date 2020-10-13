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

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use TIG\PostNL\Config\Provider\LetterBoxPackageConfiguration;

// @codingStandardsIgnoreFile
class LetterboxPackage
{
    public $totalVolume    = 0;
    public $totalWeight    = 0;
    public $hasMaximumQty  = true;
    public $maximumWeight  = 2;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var LetterBoxPackageConfiguration
     */
    private $letterBoxPackageConfiguration;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * LetterboxPackage constructor.
     *
     * @param ScopeConfigInterface          $scopeConfig
     * @param LetterBoxPackageConfiguration $letterBoxPackageConfiguration
     * @param OrderRepositoryInterface      $orderRepository
     * @param ProductRepositoryInterface    $productRepository
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        LetterBoxPackageConfiguration $letterBoxPackageConfiguration,
        OrderRepositoryInterface $orderRepository,
        ProductRepositoryInterface $productRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->letterBoxPackageConfiguration = $letterBoxPackageConfiguration;
        $this->orderRepository = $orderRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * @param $products
     * @param $isPossibleLetterboxPackage
     *
     * @return bool
     */
    public function isLetterboxPackage($products, $isPossibleLetterboxPackage)
    {
        $this->totalVolume    = 0;
        $this->totalWeight    = 0;
        $this->hasMaximumQty  = true;

        $calculationMode = $this->letterBoxPackageConfiguration->getLetterBoxPackageCalculationMode();

        // If the order is not a letterbox package but it could be we want to return true so the shipment type comment is updated on the order grid.
        if ($calculationMode === 'manually' && !$isPossibleLetterboxPackage) {
            return false;
        }

        foreach ($products as $product) {
            $this->fitsLetterboxPackage($product);
        }

        // check if all products fit in a letterbox package and the weight is equal or lower than 2 kilograms.
        if ($this->totalVolume <= 1 && $this->totalWeight <= $this->maximumWeight && $this->hasMaximumQty == true) {
            return true;
        }

        return false;
    }

    /**
     * @param $productItem - Could be either a quote item, order item or shipment item
     *
     * Based on the product attribute postnl_max_qty_letterbox, a percentage
     * is calculated for each product. If, for example, the attribute is set
     * to 4, each product will weight 25%. If the products in the cart
     * have a total weight of over 100%, the order will not fit as a letterbox.
     */
    public function fitsLetterboxPackage($productItem)
    {
        $maximumQtyLetterbox = $this->getMaximumQtyLetterbox($productItem);

        if ($maximumQtyLetterbox === 0.0) {
            $this->hasMaximumQty = false;
            return;
        }

        $orderedQty = $productItem->getQty();
        $this->totalVolume += 1 / $maximumQtyLetterbox * $orderedQty;
        $this->getTotalWeight($productItem, $orderedQty);
    }

    /**
     * @param $productItem
     *
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getMaximumQtyLetterbox($productItem)
    {
        $product = $this->productRepository->getById($productItem->getProductId());

        return floatval($product->getPostnlMaxQtyLetterbox());
    }

    /**
     * @param $orderItem
     * @param $orderedQty
     */
    public function getTotalWeight($orderItem, $orderedQty)
    {
        $weightUnit = $this->scopeConfig->getValue(
            'general/locale/weight_unit',
            ScopeInterface::SCOPE_STORE
        );

        // maximum weight for a letterbox package is 4.4 in lbs
        if ($weightUnit === 'lbs') {
            $this->maximumWeight = 4.4;
        }

        $this->totalWeight += $orderItem->getWeight() * $orderedQty;
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
}
