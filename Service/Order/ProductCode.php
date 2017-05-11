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

use TIG\PostNL\Config\Provider\ProductOptions as ProductOptionsConfiguration;
use TIG\PostNL\Config\Source\Options\ProductOptions as ProductOptionsFinder;

class ProductCode
{
    const TYPE_PICKUP        = 'pickup';
    const TYPE_DELIVERY      = 'delivery';
    const OPTION_DAYTIME     = 'daytime';
    const OPTION_EVENING     = 'evening';
    const OPTION_EXTRAATHOME = 'extraathome';
    const OPTION_SUNDAY      = 'sunday';
    const OPTION_PG          = 'pg';
    const OPTION_PGE         = 'pge';

    /**
     * @var ProductOptionsConfiguration
     */
    private $productOptionsConfiguration;

    /**
     * @var ProductOptionsFinder
     */
    private $productOptionsFinder;

    /**
     * @param ProductOptionsConfiguration $productOptionsConfiguration
     * @param ProductOptionsFinder        $productOptionsFinder
     */
    public function __construct(
        ProductOptionsConfiguration $productOptionsConfiguration,
        ProductOptionsFinder $productOptionsFinder
    ) {
        $this->productOptionsConfiguration = $productOptionsConfiguration;
        $this->productOptionsFinder = $productOptionsFinder;
    }

    /**
     * This function translates the chosen option to the correct product code for the shipment.
     *
     * @param string $type
     * @param string $option
     * @param string $country
     *
     * @return int
     */
    public function get($type = '', $option = '', $country = 'NL')
    {
        if (empty($type) && $country != 'NL') {
            return $this->getEpsOption();
        }

        $type = strtolower($type);
        $option = strtolower($option);
        if ($type == static::TYPE_PICKUP) {
            return $this->getPakjegemakProductOption($option);
        }

        return $this->getProductCode($option);
    }

    /**
     * Get the product code for the delivery options.
     *
     * @param string $option
     * @return int
     */
    private function getProductCode($option)
    {
        if ($option == static::OPTION_EVENING) {
            return $this->productOptionsConfiguration->getDefaultEveningProductOption();
        }

        if ($option == static::OPTION_SUNDAY) {
            return $this->productOptionsConfiguration->getDefaultSundayProductOption();
        }

        if ($option == static::OPTION_EXTRAATHOME) {
            return $this->productOptionsConfiguration->getDefaultExtraAtHomeProductOption();
        }

        return $this->productOptionsConfiguration->getDefaultProductOption();
    }

    /**
     * @param string $option
     * @return int
     */
    private function getPakjegemakProductOption($option)
    {
        if ($option == static::OPTION_PGE) {
            return $this->productOptionsConfiguration->getDefaultPakjeGemakEarlyProductOption();
        }

        return $this->productOptionsConfiguration->getDefaultPakjeGemakProductOption();
    }

    private function getEpsOption()
    {
        $options = $this->productOptionsFinder->getEpsProductOptions();
        $firstOption = array_shift($options);

        return $firstOption['value'];
    }
}
