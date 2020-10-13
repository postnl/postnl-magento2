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

use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Service\Carrier\ParcelTypeFinder;
use TIG\PostNL\Service\Shipping\GetFreeBoxes;
use TIG\PostNL\Config\Source\Carrier\RateType;
use TIG\PostNL\Service\Shipping\LetterboxPackage;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;

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
     * Calculator constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param GetFreeBoxes         $getFreeBoxes
     * @param Matrixrate           $matrixratePrice
     * @param Tablerate            $tablerateShippingPrice
     * @param ParcelTypeFinder     $parcelTypeFinder
     * @param LetterboxPackage    $letterboxPackage
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        GetFreeBoxes $getFreeBoxes,
        Matrixrate $matrixratePrice,
        Tablerate $tablerateShippingPrice,
        ParcelTypeFinder $parcelTypeFinder,
        LetterboxPackage $letterboxPackage
    ) {
        $this->scopeConfig            = $scopeConfig;
        $this->getFreeBoxes           = $getFreeBoxes;
        $this->matrixratePrice        = $matrixratePrice;
        $this->tablerateShippingPrice = $tablerateShippingPrice;
        $this->parcelTypeFinder       = $parcelTypeFinder;
        $this->letterboxPackage       = $letterboxPackage;
    }

    /**
     * @param RateRequest $request
     * @param null        $parcelType
     * @param             $store
     * @param bool        $includeVat
     *
     * @return array
     */
    public function price(RateRequest $request, $parcelType = null, $store = null, $includeVat = false)
    {
        $this->store = $store;
        $price       = $this->getConfigData('price');

        if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes->get($request)) {
            return $this->priceResponse('0.00', '0.00');
        }

        $ratePrice = $this->getRatePrice($this->getConfigData('rate_type'), $request, $parcelType, $includeVat);

        if ($ratePrice) {
            return $ratePrice;
        }

        return $this->priceResponse($price, $price);
    }

    /**
     * @param $rateType
     * @param $request
     * @param $parcelType
     * @param $includeVat
     *
     * @return array|bool
     */
    private function getRatePrice($rateType, $request, $parcelType, $includeVat)
    {
        if ($rateType == RateType::CARRIER_RATE_TYPE_TABLE) {
            $ratePrice = $this->getTableratePrice($request);

            return $this->priceResponse($ratePrice['price'], $ratePrice['cost']);
        }

        if (!$parcelType) {
            try {
                $parcelType = $this->parcelTypeFinder->get();
            } catch (LocalizedException $exception) {
                $parcelType = ParcelTypeFinder::DEFAULT_TYPE;
            }
        }

        $ratePrice = $this->matrixratePrice->getRate(
            $request,
            $parcelType,
            $this->store,
            $includeVat
        );
        if ($this->getConfigData('rate_type') == RateType::CARRIER_RATE_TYPE_MATRIX && $ratePrice !== false) {
            return $this->priceResponse($ratePrice['price'], $ratePrice['cost']);
        }

        return false;
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
            'cost' => $cost,
        ];
    }

    /**
     * @param RateRequest $request
     *
     * @return array
     */
    private function getTableratePrice(RateRequest $request)
    {
        $request->setConditionName($this->getConfigData('condition_name'));

        $includeVirtualPrice = $this->getConfigFlag('include_virtual_price');
        $ratePrice = $this->tablerateShippingPrice->getTableratePrice($request, $includeVirtualPrice);

        return [
            'price' => $ratePrice['price'],
            'cost' => $ratePrice['price'],
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
}
