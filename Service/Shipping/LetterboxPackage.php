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
use Magento\Store\Model\ScopeInterface;

class LetterboxPackage
{
    public $result = false;
    public $totalVolume = 0;
    public $totalWeight = 0;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * LetterboxPackage constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param $products
     *
     * @return bool
     */
    public function isLetterboxPackage($products)
    {
        $calculationMode = $this->scopeConfig->getValue(
            'tig_postnl/letterbox_package/letterbox_package_calculation_mode',
            ScopeInterface::SCOPE_STORE
        );

        if ($calculationMode === 'manually') {
            return false;
        }

        foreach ($products as $product) {
            $this->fitsLetterboxPackage($product);

            if ($this->totalVolume <= 1 && $this->totalWeight <= 2) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $product
     */
    public function fitsLetterboxPackage($product)
    {
        $maximumQtyLetterbox = floatval($product->getProduct()->getPostnlMaxQtyLetterbox());

        if ($maximumQtyLetterbox === 0.0) {
            return;
        }

        $orderedQty = $product->getQty();
        $this->totalVolume += 1 / $maximumQtyLetterbox * $orderedQty;
        $this->getTotalWeightInKg($product, $orderedQty);
    }

    /**
     * @param $product
     * @param $orderedQty
     */
    public function getTotalWeightInKg($product, $orderedQty)
    {
        $weightUnit = $this->scopeConfig->getValue(
            'general/locale/weight_unit',
            ScopeInterface::SCOPE_STORE
        );

        if ($weightUnit === 'lbs') {
            $this->totalWeight += $product->getWeight() * $orderedQty / 2.2046;

            return;
        }

        $this->totalWeight += $product->getWeight() * $orderedQty;
    }
}
