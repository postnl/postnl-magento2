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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Store\Model\ScopeInterface;
use TIG\PostNL\Config\Provider\LetterBoxPackageConfiguration;

// @codingStandardsIgnoreFile
class LetterboxPackage
{
    public $totalVolume   = 0;
    public $totalWeight   = 0;
    public $result        = true;
    public $maximumWeight = 2;

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
     * LetterboxPackage constructor.
     *
     * @param ScopeConfigInterface          $scopeConfig
     * @param LetterBoxPackageConfiguration $letterBoxPackageConfiguration
     * @param OrderRepositoryInterface      $orderRepository
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        LetterBoxPackageConfiguration $letterBoxPackageConfiguration,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->letterBoxPackageConfiguration = $letterBoxPackageConfiguration;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param $products
     * @param $isPossibleLetterboxPackage
     *
     * @return bool
     */
    public function isLetterboxPackage($products, $isPossibleLetterboxPackage)
    {
        $calculationMode = $this->letterBoxPackageConfiguration->getLetterBoxPackageCalculationMode();

        if ($calculationMode === 'manually' && !$isPossibleLetterboxPackage) {
            return false;
        }

        foreach ($products as $product) {
            $this->fitsLetterboxPackage($product);
        }

        // check if all products fit in a letterbox package and the weight is equal or lower than 2 kilograms.
        if ($this->totalVolume <= 1 && $this->totalWeight <= $this->maximumWeight && $this->result == true) {
            return true;
        }

        return false;
    }

    /**
     * @param $orderItem
     *
     * Based on the product attribute postnl_max_qty_letterbox, a percentage
     * is calculated for each product. If, for example, the attribute is set
     * to 4, each product will weight 25%. If the products in the cart
     * have a total weight of over 100%, the order will not fit as a letterbox.
     */
    public function fitsLetterboxPackage($orderItem)
    {
        $maximumQtyLetterbox = floatval($orderItem->getProduct()->getPostnlMaxQtyLetterbox());

        if ($maximumQtyLetterbox === 0.0) {
            $this->result = false;
            return;
        }

        $orderedQty = $orderItem->getQty();
        $this->totalVolume += 1 / $maximumQtyLetterbox * $orderedQty;
        $this->getTotalWeight($orderItem, $orderedQty);
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
