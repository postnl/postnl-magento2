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

use TIG\PostNL\Config\Source\OptionsAbstract;
use Magento\Framework\Option\ArrayInterface;

/**
 * As this class holds all the methods to retrieve correct product codes, it is too long for Code Sniffer to check.
 */
// @codingStandardsIgnoreFile
class ProductOptions extends OptionsAbstract implements ArrayInterface
{
    /**
     * @param $code
     * @param $type
     *
     * @return array
     */
    public function getLabel($code, $type)
    {
        if (!array_key_exists($code, $this->availableOptions) || !array_key_exists($type, $this->typeToComment)) {
            return ['label' => '', 'type' => '', 'comment' => ''];
        }

        $group = $this->availableOptions[$code]['group'];

        return [
            'label'   => $this->groupToLabel[$group],
            'type'    => $this->typeToComment[$type],
            'comment' => $this->availableOptions[$code]['label']
        ];
    }

    /**
     * @return array
     */
    public function getAllProductCodes()
    {
        return array_keys($this->availableOptions);
    }

    /**
     * Returns option array
     * @return array
     */
    public function toOptionArray()
    {
        $this->setGroupedOptions($this->availableOptions, $this->groups);

        return $this->getGroupedOptions();
    }

    /**
     * Returns options if sunday is true
     * @return array
     */
    public function getIsSundayOptions()
    {
        return $this->getProductoptions(['isSunday' => true]);
    }

    /**
     * Returns options if evening is true
     * @return array
     */
    public function getIsEveningOptions()
    {
        return $this->getProductoptions(['isEvening' => true, 'countryLimitation' => 'NL']);
    }

    /**
     * Returns options if evening is true
     * @return array
     */
    public function getIsEveningOptionsBe()
    {
        return $this->getProductoptions(['isEvening' => true, 'countryLimitation' => 'BE']);
    }

    /**
     * Returns options if group equals pakjegemak_options
     * @return array
     */
    public function getPakjeGemakOptions()
    {
        $flags = [];
        $flags['groups'][] = ['group' => 'pakjegemak_options'];
        $flags['groups'][] = ['group' => 'id_check_pakjegemak_options'];
        return $this->getProductoptions($flags);
    }

    /**
     * Returns options if pge equals true
     * @return array
     */
    public function getPakjeGemakEarlyDeliveryOptions()
    {
        return $this->getProductoptions(['pge' => true]);
    }

    /**
     * Returns options if group equals standard_options
     * @return array
     */
    public function getDefaultOptions()
    {
        $flags = [];
        $flags['groups'][] = ['group' => 'standard_options'];
        $flags['groups'][] = ['group' => 'id_check_options'];
        return $this->getProductoptions($flags);
    }

    /**
     * @return array
     */
    public function getExtraCoverProductOptions()
    {
        return $this->getProductoptions(['isExtraCover' => true]);
    }

    /**
     * @return array
     */
    public function getEpsProductOptions()
    {
        return $this->getProductoptions(['group' => 'eu_options']);
    }

    /**
     * @return array
     */
    public function getGlobalPackOptions()
    {
        return $this->getProductoptions(['group' => 'global_options']);
    }

    /**
     * @return array
     */
    public function getExtraAtHomeOptions()
    {
        return $this->getProductoptions(['group' => 'extra_at_home_options']);
    }
}
