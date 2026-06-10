<?php

namespace TIG\PostNL\Service\Carrier\Price;

use Exception;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Store\Model\ScopeInterface;
use Magento\Tax\Helper\Data;
use Magento\Tax\Model\Config;
use TIG\PostNL\Config\Source\Carrier\RateType;
use TIG\PostNL\Config\Source\LetterboxPackage\CalculationMode;
use TIG\PostNL\Config\Source\LetterboxPackage\DefaultProduct;
use TIG\PostNL\Service\Carrier\ParcelTypeFinder;
use TIG\PostNL\Service\Order\CurrentPostNLOrder;
use TIG\PostNL\Service\Order\ProductInfo;
use TIG\PostNL\Service\Shipping\GetFreeBoxes;
use function in_array;
use function reset;

// @codingStandardsIgnoreFile
class Calculator
{
    private ScopeConfigInterface $scopeConfig;

    private GetFreeBoxes $getFreeBoxes;

    private Matrixrate $matrixratePrice;

    private Tablerate $tablerateShippingPrice;

    private $store;

    private ParcelTypeFinder $parcelTypeFinder;

    private CurrentPostNLOrder $currentPostNLOrder;

    private Data $taxHelper;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        GetFreeBoxes $getFreeBoxes,
        Matrixrate $matrixratePrice,
        Tablerate $tablerateShippingPrice,
        ParcelTypeFinder $parcelTypeFinder,
        CurrentPostNLOrder $currentPostNLOrder,
        Data $taxHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->getFreeBoxes = $getFreeBoxes;
        $this->matrixratePrice = $matrixratePrice;
        $this->tablerateShippingPrice = $tablerateShippingPrice;
        $this->parcelTypeFinder = $parcelTypeFinder;
        $this->currentPostNLOrder = $currentPostNLOrder;
        $this->taxHelper = $taxHelper;
    }

    /**
     * @param RateRequest $request
     * @param null $parcelType
     * @param null $store
     *
     * @return bool|array
     */
    public function price(RateRequest $request, $parcelType = null, $store = null): bool|array
    {
        $this->store = $store;

        if (
            (bool) $request->getFreeShipping()
            || $request->getPackageQty() == $this->getFreeBoxes->get($request)
        ) {
            return $this->priceResponse('0.00', '0.00');
        }

        $ratePrice = $this->getRatePrice($this->getConfigData('rate_type'), $request, $parcelType);

        if ($ratePrice) {
            return $ratePrice;
        }

        return false;
    }

    /**
     * Calculate the price including or excluding tax
     *
     * @param RateRequest $request
     * @param null $parcelType
     *
     * @return bool|array
     */
    public function getPriceWithTax(RateRequest $request, $parcelType = null): bool|array
    {
        $price = $this->price($request, $parcelType);

        if (isset($price['price'])) {
            $price['price'] = $this->applyShippingTax((float) $price['price'], $request);
        }

        return $price;
    }

    /**
     * Resolve a configured letterbox alternative price (if available) and convert it to the configured
     * checkout tax display format.
     */
    public function getLetterboxAlternativePriceWithTax(RateRequest $request, string $productCode): ?float
    {
        $price = $this->resolveLetterboxAlternativePriceByProductCode($productCode);

        if ($price !== null) {
            return $this->applyShippingTax($price, $request);
        }

        return null;
    }

    public function getLetterboxPriceForStatedAddress(string $orderType): ?float
    {
        $configMode = $this->scopeConfig->getValue(
            'tig_postnl/letterbox_package/letterbox_package_calculation_mode',
            ScopeInterface::SCOPE_STORE,
            $this->store
        );

        if ($configMode !== CalculationMode::CALCULATION_MODE_AUTOMATIC) {
            return null;
        }

        $productCode = $this->scopeConfig->getValue(
            'tig_postnl/letterbox_package/default_letterbox_package_product',
            ScopeInterface::SCOPE_STORE,
            $this->store
        );

        if ($productCode === DefaultProduct::LETTERBOX_PRODUCT_CUSTOMER_CHOICE) {
            return match ($orderType) {
                ProductInfo::OPTION_LETTERBOX_PACKAGE_24 => $this->getLetterbox24Price(),
                ProductInfo::OPTION_LETTERBOX_PACKAGE_48 => $this->getLetterbox48Price(),
                default => ($p = $this->getConfigData('letterbox_price')) ? (float) $p : null,
            };
        }

        return $this->resolveLetterboxAlternativePriceByProductCode((string) $productCode);
    }

    public function getLetterboxPriceForStatedAddressWithTax(RateRequest $request, string $orderType): ?float
    {
        $price = $this->getLetterboxPriceForStatedAddress($orderType);

        if ($price !== null) {
            return $this->applyShippingTax($price, $request);
        }

        return null;
    }

    /**
     * @param string $rateType
     * @param RateRequest $request
     * @param $parcelType
     *
     * @return array|bool
     */
    private function getRatePrice(string $rateType, RateRequest $request, $parcelType)
    {
        $quote = null;
        if (!$parcelType) {
            $requestItems = $request->getAllItems();

            if ($requestItems) {
                $requestItem = reset($requestItems);
                $quote = $requestItem->getQuote();
            }

            try {
                $parcelType = $this->parcelTypeFinder->get($quote);
            } catch (LocalizedException) {
                $parcelType = ParcelTypeFinder::DEFAULT_TYPE;
            }
        }
        if (!$quote) {
            $requestItems = $request->getAllItems();
            if ($requestItems) {
                $requestItem = reset($requestItems);
                $quote = $requestItem->getQuote();
            }
        }

        switch ($rateType) {
            case 'free':
                return $this->priceResponse('0.00', '0.00');
            case RateType::CARRIER_RATE_TYPE_MATRIX:
                $ratePrice = $this->matrixratePrice->getRate($request, $parcelType, $this->store);

                if ($ratePrice !== false) {
                    if (($letterboxPrice = $this->getLetterboxAlternativePrice($quote)) !== null) {
                        return $this->priceResponse($letterboxPrice, $letterboxPrice);
                    }

                    return $this->priceResponse($ratePrice['price'], $ratePrice['cost']);
                }

                return false;
            case RateType::CARRIER_RATE_TYPE_TABLE:
                $ratePrice = $this->getTableratePrice($request);
                if ($ratePrice !== false) {
                    if (($letterboxPrice = $this->getLetterboxAlternativePrice($quote)) !== null) {
                        return $this->priceResponse($letterboxPrice, $letterboxPrice);
                    }

                    return $this->priceResponse($ratePrice['price'], $ratePrice['cost']);
                }

                return false;
            case RateType::CARRIER_RATE_TYPE_FLAT:
            default:
                $price = $this->getConfigData('price');
                if ($parcelType === 'pakjegemak' && $this->getConfigData('is_other_price_for_pickup')) {
                    $price = $this->getConfigData('pickup_price');
                }
                if (($letterboxPrice = $this->getLetterboxAlternativePrice($quote)) !== null) {
                    $price = $letterboxPrice;
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
            'cost' => $cost,
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
        $ratePrice = $this->tablerateShippingPrice->getTableratePrice($request, $includeVirtualPrice);

        if (!$ratePrice) {
            return false;
        }

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

    private function getLetterboxAlternativePrice(?Quote $quote): ?float
    {
        if (!$quote) {
            return null;
        }

        try {
            $order = $this->currentPostNLOrder->get($quote->getId());
        } catch (Exception) {
            return null;
        }

        if (!$order) {
            return null;
        }

        $productCode = (string) $order->getProductCode();

        if (
            $productCode === DefaultProduct::LETTERBOX_PRODUCT_2928
            || $productCode === DefaultProduct::LETTERBOX_PRODUCT_2948
        ) {
            return $this->resolveLetterboxAlternativePriceByProductCode($productCode);
        }

        if (
            in_array(
                $order->getType(),
                [
                    ProductInfo::OPTION_LETTERBOX_PACKAGE,
                    ProductInfo::OPTION_LETTERBOX_PACKAGE_24,
                    ProductInfo::OPTION_LETTERBOX_PACKAGE_48
                ],
                true
            )
            && $productCode === '3385'
        ) {
            return $this->getLetterboxPriceForStatedAddress($order->getType());
        }

        return null;
    }

    private function applyShippingTax(float $price, RateRequest $request): float
    {
        $displayType = $this->taxHelper->getShippingPriceDisplayType();
        $includeVat = $displayType === Config::DISPLAY_TYPE_INCLUDING_TAX
            || $displayType === Config::DISPLAY_TYPE_BOTH;

        return (float) $this->taxHelper->getShippingPrice($price, $includeVat, $request->getShippingAddress());
    }

    private function resolveLetterboxAlternativePriceByProductCode(string $productCode): ?float
    {
        return match ($productCode) {
            DefaultProduct::LETTERBOX_PRODUCT_2928 => $this->getLetterbox24Price(),
            DefaultProduct::LETTERBOX_PRODUCT_2948 => $this->getLetterbox48Price(),
            default => ($p = $this->getConfigData('letterbox_price')) ? (float) $p : null,
        };
    }

    private function getLetterbox24Price(): ?float
    {
        $price = $this->getConfigData('letterbox_24_price') ?: $this->getConfigData('letterbox_price');

        if ($price) {
            return (float) $price;
        }

        return null;
    }

    private function getLetterbox48Price(): ?float
    {
        $price = $this->getConfigData('letterbox_48_price') ?: $this->getConfigData('letterbox_price');

        if ($price) {
            return (float) $price;
        }

        return null;
    }
}
