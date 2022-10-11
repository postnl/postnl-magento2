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

namespace TIG\PostNL\Service\Carrier\Price;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Store\Model\ScopeInterface;
use Magento\Tax\Helper\Data;
use TIG\PostNL\Config\Source\Carrier\RateType;
use TIG\PostNL\Service\Carrier\ParcelTypeFinder;
use TIG\PostNL\Service\Shipping\GetFreeBoxes;
use TIG\PostNL\Service\Shipping\LetterboxPackage;

// @codingStandardsIgnoreFile
class Calculator
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var GetFreeBoxes
     */
    private $getFreeBoxes;

    /**
     * @var Matrixrate
     */
    private $matrixratePrice;

    /**
     * @var Tablerate
     */
    private $tablerateShippingPrice;

    /**
     * @var string
     */
    private $store;

    /**
     * @var ParcelTypeFinder
     */
    private $parcelTypeFinder;

    /**
     * @var LetterboxPackage
     */
    private $letterboxPackage;

    /**
     * @var Data
     */
    private $taxHelper;

    /**
     * Calculator constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param GetFreeBoxes         $getFreeBoxes
     * @param Matrixrate           $matrixratePrice
     * @param Tablerate            $tablerateShippingPrice
     * @param ParcelTypeFinder     $parcelTypeFinder
     * @param LetterboxPackage     $letterboxPackage
     * @param Data                 $taxHelper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        GetFreeBoxes         $getFreeBoxes,
        Matrixrate           $matrixratePrice,
        Tablerate            $tablerateShippingPrice,
        ParcelTypeFinder     $parcelTypeFinder,
        LetterboxPackage     $letterboxPackage,
        Data                 $taxHelper
    ) {
        $this->scopeConfig            = $scopeConfig;
        $this->getFreeBoxes           = $getFreeBoxes;
        $this->matrixratePrice        = $matrixratePrice;
        $this->tablerateShippingPrice = $tablerateShippingPrice;
        $this->parcelTypeFinder       = $parcelTypeFinder;
        $this->letterboxPackage       = $letterboxPackage;
        $this->taxHelper              = $taxHelper;
    }

    /**
     * @param RateRequest $request
     * @param null        $parcelType
     * @param             $store
     *
     * @return array | bool
     */
    public function price(RateRequest $request, $parcelType = null, $store = null)
    {
        $this->store = $store;

        if ((bool)$request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes->get($request)) {
            return $this->priceResponse('0.00', '0.00');
        }

        $ratePrice = $this->getRatePrice($this->getConfigData('rate_type'), $request, $parcelType);

        if ($ratePrice) {
            return $ratePrice;
        }

        return false;
    }

    /**
     * @param $rateType
     * @param $request
     * @param $parcelType
     *
     * @return array|bool
     */
    private function getRatePrice($rateType, $request, $parcelType)
    {
        if (!$parcelType) {
            $quote        = null;
            $requestItems = $request->getAllItems();

            if ($requestItems) {
                $requestItem = reset($requestItems);
                $quote       = $requestItem->getQuote();
            }

            try {
                $parcelType = $this->parcelTypeFinder->get($quote);
            } catch (LocalizedException $exception) {
                $parcelType = ParcelTypeFinder::DEFAULT_TYPE;
            }
        }

        if ((bool)$request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes->get($request)) {
            $rateType = 'free';
        }

        switch ($rateType) {
            case 'free':
                return $this->priceResponse('0.00', '0.00');
            case RateType::CARRIER_RATE_TYPE_MATRIX:
                $ratePrice = $this->matrixratePrice->getRate($request, $parcelType, $this->store);
                if ($ratePrice !== false) {
                    return $this->priceResponse($ratePrice['price'], $ratePrice['cost']);
                }

                return false;
            case RateType::CARRIER_RATE_TYPE_TABLE:
                $ratePrice = $this->getTableratePrice($request);
                if ($ratePrice !== false) {
                    return $this->priceResponse($ratePrice['price'], $ratePrice['cost']);
                }

                return false;
            case RateType::CARRIER_RATE_TYPE_FLAT:
            default:
                $price = $this->getConfigData('price');

                return $this->priceResponse($price, $price);
        }
    }

    /**
     * @param $price
     * @param $cost
     *
     * @return array
     */
    private function priceResponse($price, $cost)
    {
        return [
            'price' => $price,
            'cost'  => $cost,
        ];
    }

    /**
     * @param RateRequest $request
     *
     * @return array|bool
     */
    private function getTableratePrice(RateRequest $request)
    {
        $request->setConditionName($this->getConfigData('condition_name'));

        $includeVirtualPrice = $this->getConfigFlag('include_virtual_price');
        $ratePrice           = $this->tablerateShippingPrice->getTableratePrice($request, $includeVirtualPrice);

        if (!$ratePrice) {
            return false;
        }

        return [
            'price' => $ratePrice['price'],
            'cost'  => $ratePrice['price'],
        ];
    }

    /**
     * @param $string
     *
     * @return string
     */
    private function getConfigData($string)
    {
        return $this->scopeConfig->getValue(
            'carriers/tig_postnl/' . $string,
            ScopeInterface::SCOPE_STORE,
            $this->store
        );
    }

    /**
     * @param $string
     *
     * @return bool
     */
    private function getConfigFlag($string)
    {
        return $this->scopeConfig->isSetFlag(
            'carriers/tig_postnl/' . $string,
            ScopeInterface::SCOPE_STORE,
            $this->store
        );
    }

    /**
     * Calculate the price including or excluding tax
     *
     * @param RateRequest $request
     * @param             $parcelType
     *
     * @return mixed
     */
    public function getPriceWithTax(RateRequest $request, $parcelType = null)
    {
        $price = $this->price($request, $parcelType);

        if (isset($price['price'])) {
            $price['price'] = $this->taxHelper->getShippingPrice($price['price'], true);
        }

        return $price;
    }
}
