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

namespace TIG\PostNL\Service\Order;

use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Sales\Model\Order\Address as SalesAddress;
use TIG\PostNL\Config\Provider\ProductOptions as ProductOptionsConfiguration;
use TIG\PostNL\Config\Source\Options\ProductOptions as ProductOptionsFinder;
use TIG\PostNL\Service\Shipment\EpsCountries;
use TIG\PostNL\Service\Shipment\PriorityCountries;
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

    const SHIPMENT_TYPE_PG                = 'PG';

    const SHIPMENT_TYPE_PGE               = 'PGE';

    const SHIPMENT_TYPE_EPS               = 'EPS';

    const SHIPMENT_TYPE_GP                = 'GP';

    const SHIPMENT_TYPE_SUNDAY            = 'Sunday';

    const SHIPMENT_TYPE_TODAY            = 'Today';

    const SHIPMENT_TYPE_EVENING           = 'Evening';

    const SHIPMENT_TYPE_DAYTIME           = 'Daytime';

    const SHIPMENT_TYPE_EXTRAATHOME       = 'Extra@Home';

    const SHIPMENT_TYPE_LETTERBOX_PACKAGE = 'Letterbox Package';

    /** @var ProductOptionsConfiguration */
    private $productOptionsConfiguration;

    /** @var ProductOptionsFinder */
    private $productOptionsFinder;

    /** @var CountryShipping */
    private $countryShipping;

    /** @var QuoteInterface */
    private $quote;
    /**
     * @var ProductOptionsFinder
     */
    private $productOptions;

    /**
     * @param ProductOptionsConfiguration $productOptionsConfiguration
     * @param ProductOptionsFinder        $productOptionsFinder
     * @param CountryShipping             $countryShipping
     * @param QuoteInterface              $quote
     */
    public function __construct(
        ProductOptionsConfiguration $productOptionsConfiguration,
        ProductOptionsFinder $productOptionsFinder,
        CountryShipping $countryShipping,
        QuoteInterface $quote
    ) {
        $this->productOptionsConfiguration = $productOptionsConfiguration;
        $this->productOptionsFinder        = $productOptionsFinder;
        $this->countryShipping             = $countryShipping;
        $this->quote                       = $quote;
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

        // Check if the country is not an ESP country or BE/NL and if it is Global Pack
        if (!in_array($country, EpsCountries::ALL)
            && !in_array($country, ['BE', 'NL']) || $type === strtolower(static::SHIPMENT_TYPE_GP)) {
            $this->setGlobalPackOption($country);

            return $this->getInfo();
        }

        // EPS also uses delivery options in some cases. For Daytime there is no default EPS option.
        if ((empty($type) || $option == static::OPTION_DAYTIME)
            && !in_array($country, ['BE', 'NL']) || $type === strtolower(static::SHIPMENT_TYPE_EPS)) {
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
        $this->code = $this->productOptionsConfiguration->getDefaultGlobalpackOption();

        if ($this->makeExceptionForEUPriority($country)) {
            $this->type = static::SHIPMENT_TYPE_EPS;
            $this->code = $this->productOptionsConfiguration->getDefaultEpsProductOption();

            return;
        }

        if (in_array($country, PriorityCountries::GLOBALPACK)
            && $this->isPriorityProduct($this->code)
        ) {
            return;
        }

        $this->code = $this->productOptionsFinder->getDefaultGPOption()['value'];
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

        $this->code = $this->productOptionsConfiguration->getDefaultEpsProductOption();
        if (in_array($country, PriorityCountries::EPS)
            && $this->isPriorityProduct($this->code)
        ) {
            return;
        }

        $this->code = $this->productOptionsFinder->getDefaultEUOption()['value'];
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
            return;
        }

        $this->code = $this->productOptionsConfiguration->getDefaultPakjeGemakProductOption();
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
            default: $this->setDefaultProductOption($country);
        }
    }

    /**
     * @param $country
     */
    private function setDefaultProductOption($country)
    {
        $this->code = $this->productOptionsConfiguration->getDefaultProductOption();

        if ($this->countryShipping->isShippingNLtoBE($country)) {
            $this->code = $this->productOptionsConfiguration->getDefaultBeProductOption();
        }

        if ($this->countryShipping->isShippingBEDomestic($country)) {
            $this->code = $this->productOptionsConfiguration->getDefaultBeDomesticProductOption();
        }

        $this->type = static::SHIPMENT_TYPE_DAYTIME;

        if ($country !== 'NL' && $country !== 'BE') {
            return;
        }

        /** @var Quote $magentoQuote */
        $magentoQuote         = $this->quote->getQuote();
        $quoteTotal           = $magentoQuote->getBaseGrandTotal();
        $alternativeActive    = $this->productOptionsConfiguration->getUseAlternativeDefault();
        $alternativeMinAmount = $this->productOptionsConfiguration->getAlternativeDefaultMinAmount();

        if ($alternativeActive && $quoteTotal >= $alternativeMinAmount) {
            $this->code = $this->productOptionsConfiguration->getAlternativeDefaultProductOption();
        }
    }

    /**
     * @return array
     */
    private function getInfo()
    {
        return ['code' => $this->code, 'type' => $this->type];
    }
}
