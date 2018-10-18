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
namespace TIG\PostNL\Config\Source\Options;

use Magento\Framework\Option\ArrayInterface;
use TIG\PostNL\Config\Provider\ShippingOptions;

class DefaultOptions implements ArrayInterface
{
    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var ProductOptions
     */
    private $productOptions;

    /**
     * DefaultOptions constructor.
     *
     * @param ShippingOptions $shippingOptions
     * @param ProductOptions  $productOptions
     */
    public function __construct(
        ShippingOptions $shippingOptions,
        ProductOptions $productOptions
    ) {
        $this->shippingOptions = $shippingOptions;
        $this->productOptions = $productOptions;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $flags = [];
        $flags['groups'][] = ['group' => 'standard_options'];
        if ($this->shippingOptions->isIDCheckActive()) {
            $flags['groups'][] = ['group' => 'id_check_options'];
        }

        if ($this->shippingOptions->canUseCargoProducts()) {
            $flags['groups'][] = ['group' => 'cargo_options'];
        }

        return $this->productOptions->getProductoptions($flags);
    }

    /**
     * @return array
     */
    public function getBeProducts()
    {
        return $this->productOptions->getProductoptions(['countryLimitation' => 'BE']);
    }

    public function getEpsProducts()
    {
        if ($this->shippingOptions->canUseEpsBusinessProducts()) {
            $flags['groups'][] = ['group' => 'eps_package_options'];
        }

        $flags['groups'][] = ['group' => 'eu_options'];

        return $this->productOptions->getProductoptions($flags);
    }
}
