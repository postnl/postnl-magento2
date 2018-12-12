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

        if ($this->shippingOptions->canUseEpsBusinessProducts()) {
            $flags['groups'][] = ['group' => 'eps_package_options'];
        }

        return $this->productOptions->getProductoptions($flags);
    }

    /**
     * @return array
     */
    public function getBeProducts()
    {
        $epsOptions = $this->productOptions->getProductoptions(
            ['isEvening' => false, 'group' => 'eu_options']
        );

        $epsBusinessOptions = [];
        if ($this->shippingOptions->canUseEpsBusinessProducts()) {
            $epsBusinessOptions = $this->productOptions->getProductoptions(
                ['isEvening' => false, 'group' => 'eps_package_options']
            );
        }

        $options   = array_merge($epsOptions, $epsBusinessOptions);
        $beOptions = $this->productOptions->getProductoptions(['isEvening' => false, 'countryLimitation' => 'BE']);

        return array_merge($options, $beOptions);
    }

    /**
     * @return array
     */
    public function getEpsProducts()
    {
        $epsOptions = $this->productOptions->getProductoptions(
            ['isEvening' => false, 'countryLimitation' => false, 'group' => 'eu_options']
        );

        $epsBusinessOptions = [];
        if ($this->shippingOptions->canUseEpsBusinessProducts()) {
            $epsBusinessOptions = $this->productOptions->getProductoptions(
                ['isEvening' => false, 'countryLimitation' => false, 'group' => 'eps_package_options']
            );
        }

        return array_merge($epsOptions, $epsBusinessOptions);
    }
}
