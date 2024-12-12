<?php

namespace TIG\PostNL\Service\Carrier\Price;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Store\Model\ScopeInterface;
use Magento\Tax\Helper\Data;
use Magento\Tax\Model\Config;
use TIG\PostNL\Config\Source\Carrier\RateType;
use TIG\PostNL\Service\Carrier\ParcelTypeFinder;
use TIG\PostNL\Service\Shipping\GetFreeBoxes;

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
     * @param Data                 $taxHelper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        GetFreeBoxes         $getFreeBoxes,
        Matrixrate           $matrixratePrice,
        Tablerate            $tablerateShippingPrice,
        ParcelTypeFinder     $parcelTypeFinder,
        Data                 $taxHelper
    ) {
        $this->scopeConfig            = $scopeConfig;
        $this->getFreeBoxes           = $getFreeBoxes;
        $this->matrixratePrice        = $matrixratePrice;
        $this->tablerateShippingPrice = $tablerateShippingPrice;
        $this->parcelTypeFinder       = $parcelTypeFinder;
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
                if ($parcelType === 'pakjegemak' && $this->getConfigData('is_other_price_for_pickup')) {
                    $price = $this->getConfigData('pickup_price');
                }

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
        $includeVat = $this->taxHelper->getShippingPriceDisplayType();
        $includeVat = ($includeVat === Config::DISPLAY_TYPE_INCLUDING_TAX || $includeVat === Config::DISPLAY_TYPE_BOTH);

        $price = $this->price($request, $parcelType);
        $shippingAddress = $request->getShippingAddress();

        if (isset($price['price'])) {
            $price['price'] = $this->taxHelper->getShippingPrice($price['price'], $includeVat, $shippingAddress);
        }

        return $price;
    }
}
