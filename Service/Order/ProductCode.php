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

use TIG\PostNL\Config\Provider\ProductOptions;

class ProductCode
{
    const TYPE_PICKUP = 'pickup';
    const TYPE_DELIVERY = 'delivery';
    const OPTION_DAYTIME = 'daytime';
    const OPTION_EVENING = 'evening';
    const OPTION_SUNDAY = 'sunday';
    const OPTION_PG = 'pg';
    const OPTION_PGE = 'pge';

    /**
     * @var ProductOptions
     */
    private $productOptions;

    /**
     * @param ProductOptions $productOptions
     */
    public function __construct(
        ProductOptions $productOptions
    ) {
        $this->productOptions = $productOptions;
    }

    /**
     * This function translates the chosen option to the correct product code for the shipment.
     *
     * @param string $type
     * @param string $option
     * @return int
     */
    public function get($type = '', $option = '')
    {
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
            return $this->productOptions->getDefaultEveningProductOption();
        }

        if ($option == static::OPTION_SUNDAY) {
            return $this->productOptions->getDefaultSundayProductOption();
        }

        return $this->productOptions->getDefaultProductOption();
    }

    /**
     * @param string $option
     * @return int
     */
    private function getPakjegemakProductOption($option)
    {
        if ($option == static::OPTION_PGE) {
            return $this->productOptions->getDefaultPakjeGemakEarlyProductOption();
        }

        return $this->productOptions->getDefaultPakjeGemakProductOption();
    }
}
