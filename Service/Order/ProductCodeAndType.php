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
use TIG\PostNL\Config\Provider\ProductOptions as ProductOptionsConfiguration;
use TIG\PostNL\Config\Source\Options\ProductOptions as ProductOptionsFinder;
use TIG\PostNL\Service\Wrapper\QuoteInterface;
use TIG\PostNL\Service\Shipment\EpsCountries;

class ProductCodeAndType
{
    /** @var int */
    private $code = null;

    /** @var string */
    private $type = null;

    const TYPE_PICKUP = 'pickup';
    const TYPE_DELIVERY = 'delivery';
    const OPTION_PG = 'pg';
    const OPTION_PGE = 'pge';
    const OPTION_SUNDAY = 'sunday';
    const OPTION_DAYTIME = 'daytime';
    const OPTION_EVENING = 'evening';
    const OPTION_EXTRAATHOME = 'extra@home';
    const SHIPMENT_TYPE_PG = 'PG';
    const SHIPMENT_TYPE_PGE = 'PGE';
    const SHIPMENT_TYPE_EPS = 'EPS';
    const SHIPMENT_TYPE_GP = 'GP';
    const SHIPMENT_TYPE_SUNDAY = 'Sunday';
    const SHIPMENT_TYPE_EVENING = 'Evening';
    const SHIPMENT_TYPE_DAYTIME = 'Daytime';
    const SHIPMENT_TYPE_EXTRAATHOME = 'Extra@Home';

    /** @var ProductOptionsConfiguration */
    private $productOptionsConfiguration;

    /** @var ProductOptionsFinder */
    private $productOptionsFinder;

    /** @var QuoteInterface */
    private $quote;

    /**
     * @param ProductOptionsConfiguration $productOptionsConfiguration
     * @param ProductOptionsFinder        $productOptionsFinder
     * @param QuoteInterface              $quote
     */
    public function __construct(
        ProductOptionsConfiguration $productOptionsConfiguration,
        ProductOptionsFinder $productOptionsFinder,
        QuoteInterface $quote
    ) {
        $this->productOptionsConfiguration = $productOptionsConfiguration;
        $this->productOptionsFinder = $productOptionsFinder;
        $this->quote = $quote;
    }

    /**
     * This function translates the chosen option to the correct product code for the shipment.
     *
     * @param string $type
     * @param string $option
     * @param string $country
     *
     * @return array
     */
    // @codingStandardsIgnoreStart
    public function get($type = '', $option = '', $country = null)
    {
        $country = $country ?: $this->getCountryCode();
        $type    = strtolower($type);
        $option  = strtolower($option);

        if (!in_array($country, EpsCountries::ALL) && $country != 'NL') {
            $this->getGlobalPackOption();
            return $this->response();
        }

        // EPS also uses delivery options in some cases. For Daytime there is no default EPS option.
        if ((empty($type) || $option == static::OPTION_DAYTIME) && !in_array($country, ['BE', 'NL'])) {
            $this->getEpsOption();
            return $this->response();
        }

        if ($type == static::TYPE_PICKUP) {
            $this->getPakjegemakProductOption($option);
            return $this->response();
        }

        $this->getProductCode($option, $country);
        return $this->response();
    }
    // @codingStandardsIgnoreEnd

    /**
     * Get the product code for the delivery options.
     *
     * @param string $option
     * @param string $country
     */
    private function getProductCode($option, $country)
    {
        if ($option == static::OPTION_EVENING) {
            $this->code = $this->getDefaultEveningProductOption($country);
            $this->type = static::SHIPMENT_TYPE_EVENING;
            return;
        }

        if ($option == static::OPTION_SUNDAY) {
            $this->code = $this->productOptionsConfiguration->getDefaultSundayProductOption();
            $this->type = static::SHIPMENT_TYPE_SUNDAY;
            return;
        }

        if ($option == static::OPTION_EXTRAATHOME) {
            $this->code = $this->productOptionsConfiguration->getDefaultExtraAtHomeProductOption();
            $this->type = static::SHIPMENT_TYPE_EXTRAATHOME;
            return;
        }

        $this->getDefaultProductOption($country);
    }

    private function getDefaultProductOption($country)
    {
        $this->code = $this->productOptionsConfiguration->getDefaultProductOption();
        if ($country == 'BE') {
            $this->code = $this->productOptionsConfiguration->getDefaultBeProductOption();
        }

        $this->type = static::SHIPMENT_TYPE_DAYTIME;

        /** @var Quote $magentoQuote */
        $magentoQuote = $this->quote->getQuote();
        $quoteTotal = $magentoQuote->getBaseGrandTotal();
        $alternativeActive = $this->productOptionsConfiguration->getUseAlternativeDefault();
        $alternativeMinAmount = $this->productOptionsConfiguration->getAlternativeDefaultMinAmount();

        if ($alternativeActive && $quoteTotal >= $alternativeMinAmount) {
            $this->code = $this->productOptionsConfiguration->getAlternativeDefaultProductOption();
        }
    }

    /**
     * @param string $option
     */
    private function getPakjegemakProductOption($option)
    {
        if ($option == static::OPTION_PGE) {
            $this->code = $this->productOptionsConfiguration->getDefaultPakjeGemakEarlyProductOption();
            $this->type = static::SHIPMENT_TYPE_PGE;
            return;
        }

        $this->code = $this->productOptionsConfiguration->getDefaultPakjeGemakProductOption();
        $this->type = static::SHIPMENT_TYPE_PG;
    }

    /**
     */
    private function getEpsOption()
    {
        $this->code = $this->productOptionsConfiguration->getDefaultEpsProductOption();
        $this->type = static::SHIPMENT_TYPE_EPS;
    }

    /**
     *
     */
    private function getGlobalPackOption()
    {
        $options = $this->productOptionsFinder->getGlobalPackOptions();
        $firstOption = array_shift($options);

        $this->code = $firstOption['value'];
        $this->type = static::SHIPMENT_TYPE_GP;
    }

    /**
     * @return array
     */
    private function response()
    {
        return [
            'code' => $this->code,
            'type' => $this->type,
        ];
    }

    /**
     * @param $country
     * @return int
     */
    private function getDefaultEveningProductOption($country)
    {
        if ($country === 'BE') {
            return $this->productOptionsConfiguration->getDefaultEveningBeProductOption();
        }

        return $this->productOptionsConfiguration->getDefaultEveningProductOption();
    }

    /**
     * @return string
     */
    private function getCountryCode()
    {
        $address = $this->quote->getShippingAddress();
        return $address->getCountryId();
    }
}
