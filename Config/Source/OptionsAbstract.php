<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */

namespace TIG\PostNL\Config\Source;

/**
 * Class OptionsAbstract
 *
 * @package TIG\PostNL\Config\Source
 */
abstract class OptionsAbstract
{
    /**
     * Array for the possible product options matched by account type and flags.
     * @var array
     */
    protected $availableOptions = [];

    /**
     * Array of filterd product options.
     * @var array
     */
    public $filterdOptions   = [];

    /**
     * Group options by group types
     * @var array
     */
    public $groupedOptions = [];

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $productOptionsConfig;

    /**
     * @param \TIG\PostNL\Config\Provider\ProductOptions $config
     */
    public function __construct(
        \TIG\PostNL\Config\Provider\ProductOptions $config
    ) {
        $this->productOptionsConfig = $config;
    }

    /**
     * @param bool|Array $flags
     * @param bool       $checkAvailable
     *
     * @return array $availableOptions
     */
    public function getProductoptions($flags = false, $checkAvailable = false)
    {
        if (false !== $flags && is_array($flags)) {
            $this->filterdOptions = [];
            // Filter availableOptions on flags
            foreach($this->availableOptions as $key => $option) {
                $this->setOptionsByFlagFilters($flags, $option, $key);
            }
        }

        if (false !== $checkAvailable) {
            $this->filterdOptions = $this->setOptionsBySupportedType();
        }

        return $this->getOptionArrayUsableForConfiguration();
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
        foreach ($flags as $key => $value) {

            if (!isset($option[$key]) || $option[$key] !== $value) {
                continue;
            }

            $this->filterdOptions[$productCode] = $this->availableOptions[$productCode];
        }
    }

    /**
     * Check by supported configuration options.
     * @return array
     */
    public function setOptionsBySupportedType()
    {
        $supportedTypes      = $this->productOptionsConfig->getSupportedProductOptions();
        $supportedTypesArray = explode(',', $supportedTypes);

        if (empty($supportedTypesArray)) {
            return $this->availableOptions;
        }

        $supportedOptions = [];
        foreach ($supportedTypesArray as $type) {
            if (!isset($this->availableOptions[$type])) {
                continue;
            }
            $supportedOptions[] = $this->availableOptions[$type];
        }

        return $supportedOptions;
    }

    /**
     * @return array
     */
    public function getOptionArrayUsableForConfiguration()
    {
        $options = [];
        foreach ($this->filterdOptions as $key => $option) {
            $options[] = [
                'value' => $option['value'],
                'label' => __($option['label'])
            ];
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
        $optionsSorted = [];
        foreach ($options as $key => $option) {
            if (!array_key_exists('group', $option)) {
                continue;
            }
            $optionsSorted[$option['group']][] = [
                'value' => $option['value'],
                'label' => __($option['label'])
            ];
        }

        $optionsGrouped = [];
        foreach ($groups as $group => $label) {
            if (!array_key_exists($group, $optionsSorted)) {
                continue;
            }
            $optionsGrouped[] = [
                'label' => __($label),
                'value' => $optionsSorted[$group]
            ];
        }

        $this->groupedOptions = $optionsGrouped;
    }

    /**
     * @return array
     */
    public function getGroupedOptions()
    {
        return $this->groupedOptions;
    }

}
