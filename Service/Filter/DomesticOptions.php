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
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Service\Filter;

use TIG\PostNL\Config\Provider\AddressConfiguration;
use TIG\PostNL\Config\Source\Options\ProductOptions;

class DomesticOptions
{
    /** @var ProductOptions */
    private $productOptions;

    /** @var AddressConfiguration */
    private $addressConfiguration;

    /**
     * @param ProductOptions       $productOptions
     * @param AddressConfiguration $addressConfiguration
     */
    public function __construct(
        ProductOptions $productOptions,
        AddressConfiguration $addressConfiguration
    ) {
        $this->productOptions = $productOptions;
        $this->addressConfiguration = $addressConfiguration;
    }

    /**
     * @param array $productOptions
     *
     * @return array
     */
    public function filter($productOptions)
    {
        $country = $this->addressConfiguration->getCountry();

        if ($country == 'NL') {
            $productOptions = array_filter($productOptions, [$this, 'filterBeDomesticOption']);
        }

        if ($country == 'BE') {
            $productOptions = array_filter($productOptions, [$this, 'filterNlDomesticOption']);
        }

        return $productOptions;
    }

    /**
     * @param $productOption
     *
     * @return bool
     */
    private function filterNlDomesticOption($productOption)
    {
        // countryLimitation is used for country destination.
        // Since all BE > NL shipments are EPS, most NL domestic product codes can be filtered right away via this check
        if ($productOption['countryLimitation'] == 'NL') {
            return false;
        }

        // These are NL > BE shipments, which aren't possible since the shipments come from BE.
        $flags['groups'][] = ['group' => 'be_options'];
        $flags['groups'][] = ['group' => 'pakjegemak_be_options'];

        $domesticOptions = $this->productOptions->getProductOptions($flags);

        $isNotDomestic = $this->isNotDomestic($productOption, $domesticOptions);

        return $isNotDomestic;
    }

    /**
     * @param $productOption
     *
     * @return bool
     */
    private function filterBeDomesticOption($productOption)
    {
        $beDomesticOptions[] = $this->productOptions->getBeDomesticOptions();
        $beDomesticOptions[] = $this->productOptions->getPakjeGemakBeDomesticOptions();

        // @codingStandardsIgnoreLine
        $beDomesticOptions = call_user_func_array("array_merge", $beDomesticOptions);

        $isNotDomestic = $this->isNotDomestic($productOption, $beDomesticOptions);

        return $isNotDomestic;
    }

    /**
     * @param $option
     * @param $domesticOptions
     *
     * @return bool
     */
    private function isNotDomestic($option, $domesticOptions)
    {
        $isNotDomestic = true;

        foreach ($domesticOptions as $domesticOption) {
            if ($option['value'] === $domesticOption['value']) {
                $isNotDomestic = false;
                break;
            }
        }

        return $isNotDomestic;
    }
}
