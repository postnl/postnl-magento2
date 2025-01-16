<?php

namespace TIG\PostNL\Service\Order;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Sales\Model\Order\Address as SalesAddress;
use TIG\PostNL\Config\Provider\ProductOptions as ProductOptionsConfiguration;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Config\Source\Options\ProductOptions as ProductOptionsFinder;
use TIG\PostNL\Service\Shipment\EpsCountries;
use TIG\PostNL\Service\Shipment\PriorityCountries;
use TIG\PostNL\Service\Validation\AlternativeDelivery;
use TIG\PostNL\Service\Validation\CountryShipping;
use TIG\PostNL\Service\Wrapper\QuoteInterface;

// @codingStandardsIgnoreFile
class ProductInfo
{
    /** @var int */
    private $code = null;

    /** @var string */
    private $type = null;

    const TYPE_PICKUP                     = 'pickup';

    const TYPE_DELIVERY                   = 'delivery';

    const OPTION_PG                       = 'pg';

    const OPTION_PGE                      = 'pge';

    const OPTION_SUNDAY                   = 'sunday';

    const OPTION_TODAY                    = 'today';

    const OPTION_DAYTIME                  = 'daytime';

    const OPTION_EVENING                  = 'evening';

    const OPTION_EXTRAATHOME              = 'extra@home';

    const OPTION_LETTERBOX_PACKAGE        = 'letterbox_package';

    const OPTION_BOXABLE_PACKETS          = 'boxable_packets';

    const SHIPMENT_TYPE_PG                = 'PG';

    const SHIPMENT_TYPE_PGE               = 'PGE';

    const SHIPMENT_TYPE_EPS               = 'EPS';

    const SHIPMENT_TYPE_GP                = 'GP';

    public const SHIPMENT_TYPE_AUTO       = 'auto';

    const SHIPMENT_TYPE_SUNDAY            = 'Sunday';

    const SHIPMENT_TYPE_TODAY            = 'Today';

    const SHIPMENT_TYPE_EVENING           = 'Evening';

    const SHIPMENT_TYPE_DAYTIME           = 'Daytime';

    const SHIPMENT_TYPE_EXTRAATHOME       = 'Extra@Home';

    const SHIPMENT_TYPE_LETTERBOX_PACKAGE = 'Letterbox Package';

    const SHIPMENT_TYPE_BOXABLE_PACKETS   = 'Boxable Packet';

    private ProductOptionsConfiguration $productOptionsConfiguration;

    private ShippingOptions $shippingOptions;

    private ProductOptionsFinder $productOptionsFinder;

    private CountryShipping $countryShipping;

    private QuoteInterface $quote;

    private AlternativeDelivery $alternativeDelivery;

    /**
     * @param ProductOptionsConfiguration $productOptionsConfiguration
     * @param ShippingOptions             $shippingOptions
     * @param ProductOptionsFinder        $productOptionsFinder
     * @param CountryShipping             $countryShipping
     * @param QuoteInterface              $quote
     */
    public function __construct(
        ProductOptionsConfiguration $productOptionsConfiguration,
        ShippingOptions $shippingOptions,
        ProductOptionsFinder $productOptionsFinder,
        CountryShipping $countryShipping,
        QuoteInterface $quote,
        AlternativeDelivery $alternativeDelivery
    ) {
        $this->productOptionsConfiguration = $productOptionsConfiguration;
        $this->shippingOptions             = $shippingOptions;
        $this->productOptionsFinder        = $productOptionsFinder;
        $this->countryShipping             = $countryShipping;
        $this->quote                       = $quote;
        $this->alternativeDelivery = $alternativeDelivery;
    }

    /**
     * This function translates the chosen option to the correct product code for the shipment.
     *
     * @param string                    $type
     * @param string                    $option
     * @param SalesAddress|QuoteAddress $address
     *
     * @return array
     */
    public function get($type = '', $option = '', $address = null)
    {
        $country = $this->getCountryCode($address);
        $type    = $type ? strtolower($type) : '';
        $option  = $option ? strtolower($option) : '';

        // Check if the country is not an ESP country or BE/NL and if it is Boxable Packets
        if (!in_array($country, EpsCountries::ALL)
            && !in_array($country, ['NL']) && $type === strtolower(static::SHIPMENT_TYPE_BOXABLE_PACKETS)) {
            $this->setProductCode($option, $country);

            return $this->getInfo();
        }

        // Check if the country is not an ESP country or BE/NL and if it is Global Pack
        if (!in_array($country, EpsCountries::ALL)
            && !in_array($country, ['BE', 'NL']) && (
                $type === strtolower(static::SHIPMENT_TYPE_GP) || $type === static::SHIPMENT_TYPE_AUTO
            )) {
            $this->setGlobalPackOption($country);

            return $this->getInfo();
        }
        // Disable auto mode
        if ($type === static::SHIPMENT_TYPE_AUTO) {
            $type = '';
        }

        // EPS also uses delivery options in some cases. For Daytime there is no default EPS option.
        if ((empty($type) || $type === strtolower(static::SHIPMENT_TYPE_EPS) || $option == static::OPTION_DAYTIME)
            && !in_array($country, ['BE', 'NL'])) {
            $this->setEpsOption($address, $country);

            return $this->getInfo();
        }

        if ($type == static::TYPE_PICKUP) {
            $this->setPakjegemakProductOption($option, $country);

            return $this->getInfo();
        }

        $this->setProductCode($option, $country);

        return $this->getInfo();
    }

    /**
     * @param SalesAddress|QuoteAddress|string $address
     *
     * @return string
     */
    private function getCountryCode($address)
    {
        if ($address && is_object($address)) {
            return $address->getCountryId();
        }

        /**
         * \TIG\PostNL\Helper\DeliveryOptions\OrderParams::formatParamData
         * Request is done with country code only.
         */
        if (is_string($address)) {
            return $address;
        }

        if (is_array($address) && isset($address['Countrycode'])) {
            return $address['Countrycode'];
        }

        if (is_array($address) && isset($address['country'])) {
            return $address['country'];
        }

        $address = $this->quote->getShippingAddress();

        return $address->getCountryId();
    }

    /**
     * @param null $country
     */
    private function setGlobalPackOption($country = null)
    {
        $this->type = static::SHIPMENT_TYPE_GP;

        if ($this->makeExceptionForEUPriority($country)) {
            $this->type = static::SHIPMENT_TYPE_EPS;
            $this->code = $this->productOptionsConfiguration->getDefaultEpsProductOption();

            return;
        }

        $pepsCode = $this->productOptionsConfiguration->getDefaultPepsProductOption();
        if (in_array($country, PriorityCountries::GLOBALPACK)
            && $this->shippingOptions->canUsePriority()
            && $this->isPriorityProduct($pepsCode)
        ) {
            $this->code = $pepsCode;
            return;
        }

        $this->code = $this->productOptionsConfiguration->getDefaultGlobalpackOption();
        $this->validateAlternativeMap(AlternativeDelivery::CONFIG_GLOBALPACK, $country);
    }

    /**
     * It's possible that a country is not considered EPS, but does fall in the EU PEPS country list. That's why
     * we need a method specifically to switch back to PEPS if it is enabled for EPS.
     *
     * @param null $country
     *
     * @return bool
     */
    private function makeExceptionForEUPriority($country = null)
    {
        $epsCode = $this->productOptionsConfiguration->getDefaultEpsProductOption();
        $EUPriorityCountries = array_diff(PriorityCountries::EPS, EpsCountries::ALL);

        if (in_array($country, $EUPriorityCountries)
            && $this->isPriorityProduct($epsCode)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param $address
     * @param $country
     */
    private function setEpsOption($address, $country)
    {
        $this->type = static::SHIPMENT_TYPE_EPS;

        // Force type Global Pack (mainly used for Canary Islands)
        $options          = $this->productOptionsFinder->getEpsProductOptions($address);
        $firstOption      = array_shift($options);
        $globalPackOption = $this->productOptionsFinder->getDefaultGPOption()['value'];
        if (in_array($globalPackOption, $firstOption)) {
            $this->setGlobalPackOption();

            return;
        }

        $pepsCode = $this->productOptionsConfiguration->getDefaultPepsProductOption();
        if (in_array($country, PriorityCountries::EPS)
            && $this->shippingOptions->canUsePriority()
            && $this->isPriorityProduct($pepsCode)
        ) {
            $this->code = $pepsCode;
            return;
        }

        if ($this->isEpsCountry($country) && !$this->shippingOptions->canUseEpsBusinessProducts()) {
            $this->code = $this->productOptionsConfiguration->getDefaultEpsProductOption();
            $this->validateAlternativeMap(AlternativeDelivery::CONFIG_EPS, $country);
            return;
        }

        if ($this->isEpsCountry($country) && $this->shippingOptions->canUseEpsBusinessProducts()) {
            $this->code = $this->productOptionsConfiguration->getDefaultEpsBusinessProductOption();
            return;
        }

        $this->code = $this->productOptionsFinder->getDefaultEUOption()['value'];
    }


    private function isEpsCountry($country)
    {
        if (!in_array($country, EpsCountries::ALL)) {
            return false;
        }

        // NL to BE/NL shipments are not EPS shipments
        if ($this->countryShipping->isShippingNLToEps($country)) {
            return true;
        }

        // BE to BE shipments is not EPS, but BE to NL is
        if ($this->countryShipping->isShippingBEToEps($country)) {
            return true;
        }

        return false;
    }

    /**
     * Check whether current product code is a Priority (GlobalPack|EPS) Product
     *
     * @param $code
     *
     * @return bool|null
     */
    private function isPriorityProduct($code)
    {
        return $this->productOptionsConfiguration->checkProductByFlags($code, 'group', 'priority_options');
    }

    /**
     * @param string $option
     * @param null $country
     */
    private function setPakjegemakProductOption($option, $country = 'NL')
    {
        if ($option == static::OPTION_PGE) {
            $this->code = $this->productOptionsConfiguration->getDefaultPakjeGemakEarlyProductOption();
            $this->type = static::SHIPMENT_TYPE_PGE;

            return;
        }

        $this->type = static::SHIPMENT_TYPE_PG;

        if ($this->countryShipping->isShippingNLtoBE($country)) {
            $this->code = $this->productOptionsConfiguration->getDefaultPakjeGemakBeProductOption();
            return;
        }

        if ($this->countryShipping->isShippingBEDomestic($country)) {
            $this->code = $this->productOptionsConfiguration->getDefaultPakjeGemakBeDomesticProductOption();
            $this->validateAlternativeMap(AlternativeDelivery::CONFIG_PAKGEGEMAK_BE_DOMESTIC);
            return;
        }

        if ($this->countryShipping->isShippingBEtoNL($country)) {
            $this->code = $this->productOptionsConfiguration->getDefaultPakjeGemakBeNlProductOption();
            return;
        }

        $this->code = $this->productOptionsConfiguration->getDefaultPakjeGemakProductOption();
        $this->validateAlternativeMap(AlternativeDelivery::CONFIG_PAKGEGEMAK);
    }

    /**
     * Set the product code for the delivery options.
     *
     * @param string $option
     * @param string $country
     */
    private function setProductCode($option, $country)
    {
        switch ($option) {
            case static::OPTION_EVENING:
                $this->code = $this->productOptionsConfiguration->getDefaultEveningProductOption($country);
                $this->type = static::SHIPMENT_TYPE_EVENING;

                break;
            case static::OPTION_SUNDAY:
                $this->code = $this->productOptionsConfiguration->getDefaultSundayProductOption();
                $this->type = static::SHIPMENT_TYPE_SUNDAY;

                break;
            case static::OPTION_TODAY:
                $this->code = $this->productOptionsConfiguration->getDefaultTodayProductOption();
                $this->type = static::SHIPMENT_TYPE_TODAY;

                break;
            case static::OPTION_EXTRAATHOME:
                $this->code = $this->productOptionsConfiguration->getDefaultExtraAtHomeProductOption();
                $this->type = static::SHIPMENT_TYPE_EXTRAATHOME;

                break;
            case static::OPTION_LETTERBOX_PACKAGE:
                $this->code = $this->productOptionsConfiguration->getDefaultLetterboxPackageProductOption();
                $this->type = static::SHIPMENT_TYPE_LETTERBOX_PACKAGE;

                break;
            case static::OPTION_BOXABLE_PACKETS:
                $this->code = $this->productOptionsConfiguration->getDefaultBoxablePacketsProductOption();
                $this->type = static::SHIPMENT_TYPE_BOXABLE_PACKETS;

                break;
            default: $this->setDefaultProductOption($country);
        }
    }

    /**
     * @param $country
     */
    private function setDefaultProductOption($country)
    {
        $this->type = static::SHIPMENT_TYPE_DAYTIME;
        $this->code = $this->productOptionsConfiguration->getDefaultProductOption();

        if ($this->countryShipping->isShippingNLtoBE($country)) {
            $this->code = $this->productOptionsConfiguration->getDefaultBeProductOption();
            $this->validateAlternativeMap(AlternativeDelivery::CONFIG_BE);
        }

        if ($this->countryShipping->isShippingBEtoNL($country)) {
            $this->code = $this->productOptionsConfiguration->getDefaultBeNlProductOption();
        }

        if ($this->countryShipping->isShippingNLtoBE($country) && $this->shippingOptions->canUsePriority()) {
            $this->type = static::SHIPMENT_TYPE_EPS;
            $this->code = $this->productOptionsConfiguration->getDefaultPepsProductOption();
        }

        if ($this->countryShipping->isShippingBEDomestic($country)) {
            $this->code = $this->productOptionsConfiguration->getDefaultBeDomesticProductOption();
        }

        if ($country !== 'NL') {
            return;
        }

        $this->validateAlternativeMap(AlternativeDelivery::CONFIG_DELIVERY);
    }

    /**
     * @return array
     */
    private function getInfo()
    {
        return ['code' => $this->code, 'type' => $this->type];
    }

    private function validateAlternativeMap(string $configKey, $country = null): void
    {
        $quoteTotal = $this->quote->getQuote()->getBaseGrandTotal();

        if ($quoteTotal > 0 && $this->alternativeDelivery->isEnabled($configKey)) {
            $code = $this->alternativeDelivery->getMappedCode($configKey, $quoteTotal, $country);
            if ($code) {
                $this->code = $code;
            }
        }
    }
}
