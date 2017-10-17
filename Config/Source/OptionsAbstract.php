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
namespace TIG\PostNL\Config\Source;

abstract class OptionsAbstract
{
    /**
     * Property for the possible product options.
     */
    // @codingStandardsIgnoreLine
    protected $availableOptions;

    /**
     * Property for filterd product options matched by account type and flags.
     */
    private $filterdOptions;

    /**
     * Group options by group types
     */
    private $groupedOptions;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $productOptionsConfig;

    /**
     * @param \TIG\PostNL\Config\Provider\ProductOptions $config
     */
    public function __construct(
        \TIG\PostNL\Config\Provider\ProductOptions $config
    ) {
        $this->productOptionsConfig = $config;
    }

    /**
     * @param bool|array $flags
     * @param bool       $checkAvailable
     *
     * @return array $availableOptions
     */
    public function getProductoptions($flags = false, $checkAvailable = true)
    {
        if (false !== $checkAvailable) {
            $this->setOptionsBySupportedType();
        }

        if (false !== $flags && is_array($flags)) {
            $this->setFilterdOptions($flags);
        }

        return $this->getOptionArrayUsableForConfiguration();
    }

    /**
     * @param $flags
     *
     * @codingStandardsIgnoreLine
     * @todo: Should be able to filter on multipleoptions: eg isSunday & group
     */
    public function setFilterdOptions($flags)
    {
        $this->filterdOptions = [];
        // Filter availableOptions on flags
        foreach ($this->availableOptions as $key => $option) {
            $this->setOptionsByFlagFilters($flags, $option, $key);
        }
    }

    /**
     * @param $flags => [
     *               'isAvond' => true,
     *               'isSunday => false,
     *               etc.. ]
     * @param $option
     * @param $productCode
     */
    public function setOptionsByFlagFilters($flags, $option, $productCode)
    {
        $filterFlags = array_filter($flags, function ($value, $key) use ($option) {
            return isset($option[$key]) && $option[$key] == $value;
        }, \Zend\Stdlib\ArrayUtils::ARRAY_FILTER_USE_BOTH);

        if (!empty($filterFlags)) {
            $this->filterdOptions[$productCode] = $this->availableOptions[$productCode];
        }
    }

    /**
     * Check by supported configuration options.
     * @return void
     */
    public function setOptionsBySupportedType()
    {
        $supportedTypes = false;
        if ($this->productOptionsConfig->getSupportedProductOptions()) {
            $supportedTypes = explode(',', $this->productOptionsConfig->getSupportedProductOptions());
        }

        if (!$supportedTypes) {
            return;
        }

        $supportedOptions = array_filter($supportedTypes, function ($type) {
            return isset($this->availableOptions[$type]);
        });

        $this->availableOptions = array_filter($this->availableOptions, function ($code) use ($supportedOptions) {
            return in_array($code, $supportedOptions);
        }, \Zend\Stdlib\ArrayUtils::ARRAY_FILTER_USE_KEY);
    }

    /**
     * @return array
     */
    public function getOptionArrayUsableForConfiguration()
    {
        if (count($this->filterdOptions) == 0) {
            // @codingStandardsIgnoreLine
            return [['value' => 0, 'label' => __('There are no available options')]];
        }

        $options = [];
        foreach ($this->filterdOptions as $key => $option) {
            // @codingStandardsIgnoreLine
            $options[] = ['value' => $option['value'], 'label' => __($option['label'])];
        }

        return $options;
    }

    /**
     * Set Options sorted by group type.
     * @param array $options
     * @param array $groups
     *
     */
    public function setGroupedOptions($options, $groups)
    {
        $optionsSorted = $this->getOptionsArrayForGrouped($options);
        $optionsGroupChecked = array_filter($groups, function ($key) use ($optionsSorted) {
            return array_key_exists($key, $optionsSorted);
        }, \Zend\Stdlib\ArrayUtils::ARRAY_FILTER_USE_KEY);

        foreach ($optionsGroupChecked as $group => $label) {
            $this->groupedOptions[] = [
                'label' => __($label),
                'value' => $optionsSorted[$group]
            ];
        }
    }

    /**
     * @param $code
     *
     * @return array|null
     */
    public function getOptionsByCode($code)
    {
        return isset($this->availableOptions[$code]) ? $this->availableOptions[$code] : null;
    }

    /**
     * @return array
     */
    public function getGroupedOptions()
    {
        return $this->groupedOptions;
    }

    /**
     * This sets the array of options, so it can be used for the grouped configurations list.
     *
     * @param $options
     * @return array
     */
    private function getOptionsArrayForGrouped($options)
    {
        $optionsChecked = array_filter($options, function ($value) {
            return array_key_exists('group', $value);
        });

        $optionsSorted = [];
        foreach ($optionsChecked as $key => $option) {
            $optionsSorted[$option['group']][] = [
                'value' => $option['value'],
                'label' => __($option['label'])
            ];
        }

        return $optionsSorted;
    }
}
