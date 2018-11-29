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

// @codingStandardsIgnoreFile
abstract class OptionsAbstract
{
    /**
     * All product options.
     * @var array
     */
    protected $availableOptions = [
        // Standard Options
        '3085' => [
            'value'             => '3085',
            'label'             => 'Standard shipment',
            'isExtraCover'      => false,
            'isEvening'         => false,
            'isSunday'          => false,
            'countryLimitation' => 'NL',
            'group'             => 'standard_options',
        ],
        '3087' => [
            'value'             => '3087',
            'label'             => 'Extra Cover',
            'isExtraCover'      => true,
            'isEvening'         => true,
            'isSunday'          => true,
            'countryLimitation' => 'NL',
            'group'             => 'standard_options',
        ],
        '3094' => [
            'value'             => '3094',
            'label'             => 'Extra Cover + Return when not home',
            'isExtraCover'      => true,
            'isEvening'         => true,
            'isSunday'          => true,
            'countryLimitation' => 'NL',
            'group'             => 'standard_options',
        ],
        '3189' => [
            'value'             => '3189',
            'label'             => 'Signature on delivery',
            'isExtraCover'      => false,
            'isEvening'         => false,
            'isSunday'          => false,
            'countryLimitation' => 'NL',
            'group'             => 'standard_options',
        ],
        '3089' => [
            'value'             => '3089',
            'label'             => 'Signature on delivery + Delivery to stated address only',
            'isExtraCover'      => false,
            'isEvening'         => true,
            'isSunday'          => true,
            'isSameDay'         => true,
            'statedAddressOnly' => true,
            'isBelgiumOnly'     => false,
            'countryLimitation' => 'NL',
            'group'             => 'standard_options',
        ],
        '3389' => [
            'value'             => '3389',
            'label'             => 'Signature on delivery + Return when not home',
            'isExtraCover'      => false,
            'isEvening'         => false,
            'isSunday'          => false,
            'countryLimitation' => 'NL',
            'group'             => 'standard_options',
        ],
        '3096' => [
            'value'             => '3096',
            'label'             => 'Signature on delivery + Deliver to stated address only + Return when not home',
            'isExtraCover'      => false,
            'isEvening'         => true,
            'isSunday'          => true,
            'isSameDay'         => true,
            'statedAddressOnly' => true,
            'isBelgiumOnly'     => false,
            'countryLimitation' => 'NL',
            'group'             => 'standard_options',
        ],
        '3090' => [
            'value'             => '3090',
            'label'             => 'Delivery to neighbour + Return when not home',
            'isExtraCover'      => false,
            'isEvening'         => true,
            'isSunday'          => false,
            'countryLimitation' => 'NL',
            'group'             => 'standard_options',
        ],
        '3385' => [
            'value'             => '3385',
            'label'             => 'Deliver to stated address only',
            'isExtraCover'      => false,
            'isEvening'         => true,
            'isSunday'          => true,
            'isSameDay'         => true,
            'statedAddressOnly' => true,
            'countryLimitation' => 'NL',
            'group'             => 'standard_options',
        ],
        // Pakjegemak Options
        '3534' => [
            'value'             => '3534',
            'label'             => 'Post Office + Extra Cover',
            'isExtraCover'      => true,
            'isExtraEarly'      => false,
            'isSunday'          => false,
            'countryLimitation' => 'NL',
            'pge'               => false,
            'group'             => 'pakjegemak_options',
        ],
        '3544' => [
            'value'             => '3544',
            'label'             => 'Post Office + Extra Cover + Notification',
            'isExtraCover'      => true,
            'isExtraEarly'      => true,
            'isSunday'          => false,
            'countryLimitation' => 'NL',
            'pge'               => true,
            'group'             => 'pakjegemak_options',
        ],
        '3533' => [
            'value'             => '3533',
            'label'             => 'Post Office + Signature on Delivery',
            'isExtraCover'      => false,
            'isExtraEarly'      => false,
            'isSunday'          => false,
            'countryLimitation' => 'NL',
            'pge'               => false,
            'group'             => 'pakjegemak_options',
        ],
        '3543' => [
            'value'             => '3543',
            'label'             => 'Post Office + Signature on Delivery + Notification',
            'isExtraCover'      => false,
            'isSunday'          => false,
            'isExtraEarly'      => true,
            'countryLimitation' => 'NL',
            'pge'               => true,
            'group'             => 'pakjegemak_options',
        ],
        // EU Options
        '4950' => [
            'value'             => '4950',
            'label'             => 'EU Pack Special',
            'isExtraCover'      => false,
            'isSunday'          => false,
            'countryLimitation' => false,
            'group'             => 'eu_options',
        ],
        '4952' => [
            'value'             => '4952',
            'label'             => 'EU Pack Special Consumer (incl. signature)',
            'isExtraCover'      => false,
            'isSunday'          => false,
            'countryLimitation' => false,
            'group'             => 'eu_options',
        ],
        '4938' => [
            'value'             => '4938',
            'label'             => 'EU Pack Special evening',
            'isEvening'         => true,
            'isExtraCover'      => false,
            'isSunday'          => false,
            'countryLimitation' => 'BE',
            'group'             => 'eu_options',
        ],
        '4941' => [
            'value'             => '4941',
            'label'             => 'EU Pack Standard evening',
            'isEvening'         => true,
            'isExtraCover'      => false,
            'isSunday'          => false,
            'countryLimitation' => 'BE',
            'group'             => 'eu_options',
        ],
        // GlobalPack
        '4945'=> [
            'value'             => '4945',
            'label'             => 'GlobalPack',
            'isExtraCover'      => false,
            'isSunday'          => false,
            'countryLimitation' => false,
            'group'             => 'global_options',
        ],
        // Brievenbuspakje Options
        '2928' => [
            'value'             => '2928',
            'label'             => 'Letter Box Parcel Extra',
            'isExtraCover'      => false,
            'isSunday'          => false,
            'countryLimitation' => 'NL',
            'group'             => 'buspakje_options',
        ],
        // Extra@Home Options
        '3628' => [
            'value'             => '3628',
            'label'             => 'Extra@Home Top service 2 person delivery NL',
            'isExtraCover'      => false,
            'isSunday'          => false,
            'countryLimitation' => 'NL',
            'group'             => 'extra_at_home_options',
        ],
        '3629' => [
            'value'             => '3629',
            'label'             => 'Extra@Home Top service Btl 2 person delivery',
            'isExtraCover'      => false,
            'isSunday'          => false,
            'countryLimitation' => 'BE',
            'group'             => 'extra_at_home_options',
        ],
        '3653' => [
            'value'             => '3653',
            'label'             => 'Extra@Home Top service 1 person delivery NL',
            'isExtraCover'      => false,
            'isSunday'          => false,
            'countryLimitation' => 'NL',
            'group'             => 'extra_at_home_options',
        ],
        '3783' => [
            'value'             => '3783',
            'label'             => 'Extra@Home Top service Btl 1 person delivery',
            'isExtraCover'      => false,
            'isSunday'          => false,
            'countryLimitation' => 'BE',
            'group'             => 'extra_at_home_options',
        ],
        '3790' => [
            'value'             => '3790',
            'label'             => 'Extra@Home Drempelservice 1 person delivery NL',
            'isExtraCover'      => false,
            'isSunday'          => false,
            'countryLimitation' => 'NL',
            'group'             => 'extra_at_home_options',
        ],
        '3791' => [
            'value'             => '3791',
            'label'             => 'Extra@Home Drempelservice 2 person delivery NL',
            'isExtraCover'      => false,
            'isSunday'          => false,
            'countryLimitation' => 'NL',
            'group'             => 'extra_at_home_options',
        ],
        '3792' => [
            'value'             => '3792',
            'label'             => 'Extra@Home Drempelservice Btl 1 person delivery',
            'isExtraCover'      => false,
            'isSunday'          => false,
            'countryLimitation' => 'BE',
            'group'             => 'extra_at_home_options',
        ],
        '3793' => [
            'value'             => '3793',
            'label'             => 'Extra@Home Drempelservice Btl 2 person delivery',
            'isExtraCover'      => false,
            'isSunday'          => false,
            'countryLimitation' => 'BE',
            'group'             => 'extra_at_home_options',
        ],
        //ID Check
        '3437' => [
            'value'             => '3437',
            'label'             => 'Parcel with Agecheck 18+ Neighbors',
            'isExtraCover'      => false,
            'isEvening'         => true,
            'isSunday'          => true,
            'isSameDay'         => true,
            'statedAddressOnly' => false,
            'countryLimitation' => 'NL',
            'group'             => 'id_check_options',
        ],
        '3438' => [
            'value'             => '3438',
            'label'             => 'Parcel with Agecheck 18+',
            'isExtraCover'      => false,
            'isEvening'         => true,
            'isSunday'          => true,
            'isSameDay'         => true,
            'statedAddressOnly' => false,
            'countryLimitation' => 'NL',
            'group'             => 'id_check_options',
        ],
        '3443' => [
            'value'             => '3443',
            'label'             => 'Parcel with Extra Cover + Agecheck 18+',
            'isExtraCover'      => true,
            'isEvening'         => true,
            'isSunday'          => true,
            'isSameDay'         => true,
            'statedAddressOnly' => false,
            'countryLimitation' => 'NL',
            'group'             => 'id_check_options',
        ],
        '3446' => [
            'value'             => '3446',
            'label'             => 'Parcel with Extra Cover + Agecheck 18+ Return when not home',
            'isExtraCover'      => true,
            'isEvening'         => true,
            'isSunday'          => true,
            'isSameDay'         => true,
            'statedAddressOnly' => false,
            'countryLimitation' => 'NL',
            'group'             => 'id_check_options',
        ],
        '3449' => [
            'value'             => '3449',
            'label'             => 'Parcel with Agecheck 18+ Return when not home',
            'isExtraCover'      => false,
            'isEvening'         => true,
            'isSunday'          => true,
            'isSameDay'         => true,
            'statedAddressOnly' => false,
            'countryLimitation' => 'NL',
            'group'             => 'id_check_options',
        ],
        // ID Check Pakje gemak
        '3571' => [
            'value'             => '3571',
            'label'             => 'Post Office + Agecheck 18+',
            'isExtraCover'      => false,
            'pge'               => false,
            'statedAddressOnly' => false,
            'countryLimitation' => 'NL',
            'group'             => 'id_check_pakjegemak_options',
        ],
        '3574' => [
            'value'             => '3574',
            'label'             => 'Post Office + Notification + Agecheck 18+',
            'isExtraCover'      => false,
            'pge'               => true,
            'statedAddressOnly' => false,
            'countryLimitation' => 'NL',
            'group'             => 'id_check_pakjegemak_options',
        ],
        '3581' => [
            'value'             => '3581',
            'label'             => 'Post Office + Extra Cover + Agecheck 18+',
            'isExtraCover'      => true,
            'pge'               => false,
            'statedAddressOnly' => false,
            'countryLimitation' => 'NL',
            'group'             => 'id_check_pakjegemak_options',
        ],
        '3584' => [
            'value'             => '3584',
            'label'             => 'Post Office + Extra Cover + Notification + Agecheck 18+',
            'isExtraCover'      => true,
            'pge'               => true,
            'statedAddressOnly' => false,
            'isSunday'          => false,
            'countryLimitation' => 'NL',
            'group'             => 'id_check_pakjegemak_options',
        ]
    ];

    protected $groups = [
        'standard_options'              => 'Domestic options',
        'pakjegemak_options'            => 'Post Office options',
        'eu_options'                    => 'EU options',
        'global_options'                => 'Global options',
        'buspakje_options'              => 'Letter Box Parcel options',
        'extra_at_home_options'         => 'Extra@Home options',
        'id_check_options'              => 'ID Check options',
        'id_check_pakjegemak_options'   => 'ID Check Post Office options',
    ];

    protected $groupToLabel = [
        'standard_options'              => 'Domestic',
        'pakjegemak_options'            => 'Post Office',
        'eu_options'                    => 'EPS',
        'global_options'                => 'Global Pack',
        'buspakje_options'              => 'Letter Box',
        'extra_at_home_options'         => 'Extra@Home',
        'id_check_options'              => 'ID Check',
        'id_check_pakjegemak_options'   => 'ID Check Post Office',
    ];

    protected $typeToComment = [
        'Daytime'     => '',
        'Evening'     => 'Evening',
        'ExtraAtHome' => '',
        'Extra@Home'  => '',
        'Sunday'      => 'Sunday',
        'PG'          => '',
        'PGE'         => 'Early morning pickup',
        'EPS'         => '',
        'GP'          => '',
    ];

    /**
     * Property for filterd product options matched by account type and flags.
     */
    private $filterdOptions;

    /**
     * Group options by group types
     */
    private $groupedOptions;

    /**
     * @return array
     */
    public function get()
    {
        return $this->availableOptions;
    }

    /**
     * @param bool|array $flags
     *
     * @return array $availableOptions
     */
    public function getProductoptions($flags = false)
    {
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
            if (isset($flags['groups'])) {
                $this->setOptionsByMultipleFlagFilters($flags, $option, $key);
            } else {
                $this->setOptionsByFlagFilters($flags, $option, $key);
            }
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

        if (count($filterFlags) == count($flags)) {
            $this->filterdOptions[$productCode] = $this->availableOptions[$productCode];
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
    public function setOptionsByMultipleFlagFilters($flags, $option, $productCode)
    {
        foreach ($flags['groups'] as $flag) {
            $filterFlags = array_filter($flag, function ($value, $key) use ($option) {
                return isset($option[$key]) && $option[$key] == $value;
            }, \Zend\Stdlib\ArrayUtils::ARRAY_FILTER_USE_BOTH);

            if (count($filterFlags) == count($flags)) {
                $this->filterdOptions[$productCode] = $this->availableOptions[$productCode];
            }
        }
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
