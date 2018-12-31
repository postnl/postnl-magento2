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
        $epsBusinessOptions = [];
        if ($this->shippingOptions->canUseEpsBusinessProducts()) {
            $epsBusinessOptions = $this->productOptions->getProductoptions(
                ['isEvening' => false, 'group' => 'eps_package_options']
            );
        }

        $cargoProducts = [];
        if ($this->shippingOptions->canUseCargoProducts()) {
            $cargoProducts = $this->productOptions->getProductoptions(
                ['countryLimitation' => 'BE', 'group' => 'cargo_options']
            );
        }

        $epsBusinessOptions = array_merge($epsBusinessOptions, $cargoProducts);
        return array_merge($this->productOptions->getProductoptions(['group' => 'eu_options']), $epsBusinessOptions);
    }

    /**
     * @return array
     */
    public function getEpsProducts()
    {
        $epsOptions = $this->productOptions->getProductoptions(
            ['isEvening' => false, 'countryLimitation' => false, 'group' => 'eu_options']
        );

        if ($this->shippingOptions->canUsePepsProducts()) {
            $pepsOptions = $this->productOptions->getProductoptions(['group' => 'peps_options']);
            $epsOptions = array_merge($epsOptions, $pepsOptions);
        }

        if ($this->shippingOptions->canUseEpsBusinessProducts()) {
            $epsBusinessOptions = $this->productOptions->getProductoptions(
                ['isEvening' => false, 'countryLimitation' => false, 'group' => 'eps_package_options']
            );
            $epsOptions = array_merge($epsOptions, $epsBusinessOptions);
        }

        return $epsOptions;
    }

    /**
     * @return array
     */
    public function getGlobalProducts()
    {
        $globalOptions = $this->productOptions->getProductoptions(['group' => 'global_options']);
        if ($this->shippingOptions->canUsePepsProducts()) {
            $pepsOptions = $this->productOptions->getProductoptions(['group' => 'peps_options']);
            $globalOptions = array_merge($globalOptions, $pepsOptions);
        }

        return $globalOptions;
    }

    /**
     * @return array
     */
    public function getEveningOptionsNL()
    {
        return $this->getEveningOptions('NL');
    }

    /**
     * @return array
     */
    public function getEveningOptionsBE()
    {
        return $this->getEveningOptions('BE');
    }

    /**
     * @param string $country
     *
     * @return array
     */
    public function getEveningOptions($country = 'NL')
    {
        $options = $this->productOptions->getProductoptions(['isEvening' => true, 'countryLimitation' => $country]);
        if ($this->shippingOptions->isIDCheckActive()) {
            return $options;
        }

        $idOptions = $this->productOptions->getProductoptions(
            ['group' => 'id_check_options', 'countryLimitation' => $country]
        );

        $idKeys = array_map(function ($option) {
             return $option['value'];
        }, $idOptions);

        $options = array_filter($options, function ($option) use ($idKeys) {
            return !in_array($option['value'], $idKeys);
        });

        return $options;
    }
}
