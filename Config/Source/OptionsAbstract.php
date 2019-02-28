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
     *
     * @var array
     */
    protected $availableOptions = [
        // Standard Options
        '3085' => [
            'value'                => '3085',
            'label'                => 'Standard shipment',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => true,
            'countryLimitation'    => 'NL',
            'group'                => 'standard_options',
        ],
        '3087' => [
            'value'                => '3087',
            'label'                => 'Extra Cover',
            'isExtraCover'         => true,
            'isEvening'            => true,
            'isSunday'             => true,
            'isGuaranteedDelivery' => true,
            'countryLimitation'    => 'NL',
            'group'                => 'standard_options',
        ],
        '3094' => [
            'value'                => '3094',
            'label'                => 'Extra Cover + Return when not home',
            'isExtraCover'         => true,
            'isEvening'            => true,
            'isSunday'             => true,
            'isGuaranteedDelivery' => true,
            'countryLimitation'    => 'NL',
            'group'                => 'standard_options',
        ],
        '3189' => [
            'value'                => '3189',
            'label'                => 'Signature on delivery',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => true,
            'countryLimitation'    => 'NL',
            'group'                => 'standard_options',
        ],
        '3089' => [
            'value'                => '3089',
            'label'                => 'Signature on delivery + Delivery to stated address only',
            'isExtraCover'         => false,
            'isEvening'            => true,
            'isSunday'             => true,
            'isGuaranteedDelivery' => true,
            'isSameDay'            => true,
            'statedAddressOnly'    => true,
            'isBelgiumOnly'        => false,
            'countryLimitation'    => 'NL',
            'group'                => 'standard_options',
        ],
        '3389' => [
            'value'                => '3389',
            'label'                => 'Signature on delivery + Return when not home',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => true,
            'countryLimitation'    => 'NL',
            'group'                => 'standard_options',
        ],
        '3096' => [
            'value'                => '3096',
            'label'                => 'Signature on delivery + Deliver to stated address only + Return when not home',
            'isExtraCover'         => false,
            'isEvening'            => true,
            'isSunday'             => true,
            'isGuaranteedDelivery' => true,
            'isSameDay'            => true,
            'statedAddressOnly'    => true,
            'isBelgiumOnly'        => false,
            'countryLimitation'    => 'NL',
            'group'                => 'standard_options',
        ],
        '3090' => [
            'value'                => '3090',
            'label'                => 'Delivery to neighbour + Return when not home',
            'isExtraCover'         => false,
            'isEvening'            => true,
            'isSunday'             => false,
            'isGuaranteedDelivery' => true,
            'countryLimitation'    => 'NL',
            'group'                => 'standard_options',
        ],
        '3385' => [
            'value'                => '3385',
            'label'                => 'Deliver to stated address only',
            'isExtraCover'         => false,
            'isEvening'            => true,
            'isSunday'             => true,
            'isGuaranteedDelivery' => true,
            'isSameDay'            => true,
            'statedAddressOnly'    => true,
            'countryLimitation'    => 'NL',
            'group'                => 'standard_options',
        ],
        // Pakjegemak Options
        '3534' => [
            'value'                => '3534',
            'label'                => 'Post Office + Extra Cover',
            'isExtraCover'         => true,
            'isExtraEarly'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'NL',
            'pge'                  => false,
            'group'                => 'pakjegemak_options',
        ],
        '3544' => [
            'value'                => '3544',
            'label'                => 'Post Office + Extra Cover + Notification',
            'isExtraCover'         => true,
            'isExtraEarly'         => true,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'NL',
            'pge'                  => true,
            'group'                => 'pakjegemak_options',
        ],
        '3533' => [
            'value'                => '3533',
            'label'                => 'Post Office + Signature on Delivery',
            'isExtraCover'         => false,
            'isExtraEarly'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'NL',
            'pge'                  => false,
            'group'                => 'pakjegemak_options',
        ],
        '3543' => [
            'value'                => '3543',
            'label'                => 'Post Office + Signature on Delivery + Notification',
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'isExtraEarly'         => true,
            'countryLimitation'    => 'NL',
            'pge'                  => true,
            'group'                => 'pakjegemak_options',
        ],
        // EU Options
        '4952' => [
            'value'                => '4952',
            'label'                => 'EU Pack Special Consumer',
            'isDefault'            => 1,
            'isEvening'            => false,
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'eu_options',
        ],
        '4938' => [
            'value'                => '4938',
            'label'                => 'Deliver to stated address only + Signature on delivery (BE)',
            'isEvening'            => true,
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'eu_options',
        ],
        '4941' => [
            'value'                => '4941',
            'label'                => 'Deliver to stated address only (BE)',
            'isEvening'            => true,
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'eu_options',
        ],
        '4946' => [
            'value'                => '4946',
            'label'                => 'EPS Standard BE',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'eu_options',
        ],
        '4986' => [
            'value'                => '4986',
            'label'                => 'EPS Standard BE (Mon/Fri)',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'eu_options',
        ],
        // GlobalPack
        '4945' => [
            'value'                => '4945',
            'label'                => 'GlobalPack',
            'isDefault'            => 1,
            'isEvening'            => false,
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'global_options',
        ],
        // Brievenbuspakje Options
        '2928' => [
            'value'                => '2928',
            'label'                => 'Letter Box Parcel Extra',
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'NL',
            'group'                => 'buspakje_options',
        ],
        // Extra@Home Options
        '3628' => [
            'value'                => '3628',
            'label'                => 'Extra@Home Top service 2 person delivery NL',
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'NL',
            'group'                => 'extra_at_home_options',
        ],
        '3629' => [
            'value'                => '3629',
            'label'                => 'Extra@Home Top service Btl 2 person delivery',
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'extra_at_home_options',
        ],
        '3653' => [
            'value'                => '3653',
            'label'                => 'Extra@Home Top service 1 person delivery NL',
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'NL',
            'group'                => 'extra_at_home_options',
        ],
        '3783' => [
            'value'                => '3783',
            'label'                => 'Extra@Home Top service Btl 1 person delivery',
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'extra_at_home_options',
        ],
        '3790' => [
            'value'                => '3790',
            'label'                => 'Extra@Home Drempelservice 1 person delivery NL',
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'NL',
            'group'                => 'extra_at_home_options',
        ],
        '3791' => [
            'value'                => '3791',
            'label'                => 'Extra@Home Drempelservice 2 person delivery NL',
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'NL',
            'group'                => 'extra_at_home_options',
        ],
        '3792' => [
            'value'                => '3792',
            'label'                => 'Extra@Home Drempelservice Btl 1 person delivery',
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'extra_at_home_options',
        ],
        '3793' => [
            'value'                => '3793',
            'label'                => 'Extra@Home Drempelservice Btl 2 person delivery',
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'extra_at_home_options',
        ],
        //ID Check
        '3437' => [
            'value'                => '3437',
            'label'                => 'Parcel with Agecheck 18+ Neighbors',
            'isExtraCover'         => false,
            'isEvening'            => true,
            'isSunday'             => true,
            'isGuaranteedDelivery' => false,
            'isSameDay'            => true,
            'statedAddressOnly'    => false,
            'countryLimitation'    => 'NL',
            'group'                => 'id_check_options',
        ],
        '3438' => [
            'value'                => '3438',
            'label'                => 'Parcel with Agecheck 18+',
            'isExtraCover'         => false,
            'isEvening'            => true,
            'isSunday'             => true,
            'isGuaranteedDelivery' => false,
            'isSameDay'            => true,
            'statedAddressOnly'    => false,
            'countryLimitation'    => 'NL',
            'group'                => 'id_check_options',
        ],
        '3443' => [
            'value'                => '3443',
            'label'                => 'Parcel with Extra Cover + Agecheck 18+',
            'isExtraCover'         => true,
            'isEvening'            => true,
            'isSunday'             => true,
            'isGuaranteedDelivery' => false,
            'isSameDay'            => true,
            'statedAddressOnly'    => false,
            'countryLimitation'    => 'NL',
            'group'                => 'id_check_options',
        ],
        '3446' => [
            'value'                => '3446',
            'label'                => 'Parcel with Extra Cover + Agecheck 18+ Return when not home',
            'isExtraCover'         => true,
            'isEvening'            => true,
            'isSunday'             => true,
            'isGuaranteedDelivery' => false,
            'isSameDay'            => true,
            'statedAddressOnly'    => false,
            'countryLimitation'    => 'NL',
            'group'                => 'id_check_options',
        ],
        '3449' => [
            'value'                => '3449',
            'label'                => 'Parcel with Agecheck 18+ Return when not home',
            'isExtraCover'         => false,
            'isEvening'            => true,
            'isSunday'             => true,
            'isGuaranteedDelivery' => false,
            'isSameDay'            => true,
            'statedAddressOnly'    => false,
            'countryLimitation'    => 'NL',
            'group'                => 'id_check_options',
        ],
        // ID Check Pakje gemak
        '3571' => [
            'value'                => '3571',
            'label'                => 'Post Office + Agecheck 18+',
            'isExtraCover'         => false,
            'pge'                  => false,
            'statedAddressOnly'    => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'NL',
            'group'                => 'id_check_pakjegemak_options',
        ],
        '3574' => [
            'value'                => '3574',
            'label'                => 'Post Office + Notification + Agecheck 18+',
            'isExtraCover'         => false,
            'pge'                  => true,
            'statedAddressOnly'    => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'NL',
            'group'                => 'id_check_pakjegemak_options',
        ],
        '3581' => [
            'value'                => '3581',
            'label'                => 'Post Office + Extra Cover + Agecheck 18+',
            'isExtraCover'         => true,
            'pge'                  => false,
            'statedAddressOnly'    => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'NL',
            'group'                => 'id_check_pakjegemak_options',
        ],
        '3584' => [
            'value'                => '3584',
            'label'                => 'Post Office + Extra Cover + Notification + Agecheck 18+',
            'isExtraCover'         => true,
            'pge'                  => true,
            'statedAddressOnly'    => false,
            'isGuaranteedDelivery' => false,
            'isSunday'             => false,
            'countryLimitation'    => 'NL',
            'group'                => 'id_check_pakjegemak_options',
        ],
        // Cargo
        '3606' => [
            'value'                => '3606',
            'label'                => 'Pharma&Care Pallet 2-8 C (NL)',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => true,
            'countryLimitation'    => 'NL',
            'group'                => 'cargo_options',
        ],
        '3607' => [
            'value'                => '3607',
            'label'                => 'Pharma&Care Pallet 15-25 C (NL)',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => true,
            'countryLimitation'    => 'NL',
            'group'                => 'cargo_options',
        ],
        '3608' => [
            'value'                => '3608',
            'label'                => 'Pharma&Care Stukgoed 2-8 C (NL)',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => true,
            'countryLimitation'    => 'NL',
            'group'                => 'cargo_options',
        ],
        '3609' => [
            'value'                => '3609',
            'label'                => 'Pharma&Care Stukgoed 15-25 C (NL)',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => true,
            'countryLimitation'    => 'NL',
            'group'                => 'cargo_options',
        ],
        '3610' => [
            'value'                => '3610',
            'label'                => 'Cargo Pallet NL',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => true,
            'countryLimitation'    => 'NL',
            'group'                => 'cargo_options',
        ],
        '3630' => [
            'value'                => '3630',
            'label'                => 'Cargo Stukgoed NL',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => true,
            'countryLimitation'    => 'NL',
            'group'                => 'cargo_options',
        ],
        '3657' => [
            'value'                => '3657',
            'label'                => 'Cargo Halve Europallet NL',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => true,
            'countryLimitation'    => 'NL',
            'group'                => 'cargo_options',
        ],
        '3618' => [
            'value'                => '3618',
            'label'                => 'Cargo Pallet BE',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'cargo_options',
        ],
        '3638' => [
            'value'                => '3638',
            'label'                => 'Cargo Stukgoed BE',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'cargo_options',
        ],
        '3658' => [
            'value'                => '3658',
            'label'                => 'Cargo Halve Europallet BE',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'cargo_options',
        ],
        '3622' => [
            'value'                => '3622',
            'label'                => 'Cargo Pallet LU',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'LU',
            'group'                => 'cargo_options',
        ],
        '3642' => [
            'value'                => '3642',
            'label'                => 'Cargo Stukgoed LU',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'LU',
            'group'                => 'cargo_options',
        ],
        '3659' => [
            'value'                => '3659',
            'label'                => 'Cargo Halve Europallet LU',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'LU',
            'group'                => 'cargo_options',
        ],
        // Package EPS B2B
        '4940' => [
            'value'                => '4940',
            'label'                => 'EU Pack Special to business',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'eps_package_options',
        ],
        '4983' => [
            'value'                => '4983',
            'label'                => 'EPS Business delivery EU (Mon/Sat)',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'eps_package_options',
        ],
        '4985' => [
            'value'                => '4985',
            'label'                => 'EPS Business delivery EU (Mon/Fri)',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'eps_package_options',
        ],
        // PEPS Products
        '6350' => [
            'value'                => '6350',
            'label'                => 'Priority packets tracked',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'peps_options',
        ],
        '6550' => [
            'value'                => '6550',
            'label'                => 'Priority packets tracked bulk',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'peps_options',
        ],
        '6940' => [
            'value'                => '6940',
            'label'                => 'Priority packets tracked sorted',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'peps_options',
        ],
        '6942' => [
            'value'                => '6942',
            'label'                => 'Priority packets tracked boxable',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'peps_options',
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
        'cargo_options'                 => 'Cargo options',
        'eps_package_options'           => 'Package options',
        'peps_options'                  => 'Priority EPS'
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
        'cargo_options'                 => 'Cargo',
        'eps_package_options'           => 'Package',
        'peps_options'                  => 'Priority (EPS / Globalpack)'
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
     * Property for filtered product options matched by account type and flags.
     */
    private $filteredOptions;

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
    public function getProductOptions($flags = false)
    {
        if (false !== $flags && is_array($flags)) {
            $this->setFilteredOptions($flags);
        }

        return $this->getOptionArrayUsableForConfiguration();
    }

    /**
     * @param $flags
     *
     * @codingStandardsIgnoreLine
     */
    public function setFilteredOptions($flags)
    {
        $this->filteredOptions = [];

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
            $this->filteredOptions[$productCode] = $this->availableOptions[$productCode];
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
                $this->filteredOptions[$productCode] = $this->availableOptions[$productCode];
            }
        }
    }

    /**
     * @return array
     */
    public function getOptionArrayUsableForConfiguration()
    {
        if (count($this->filteredOptions) == 0) {
            // @codingStandardsIgnoreLine
            return [['value' => 0, 'label' => __('There are no available options')]];
        }

        $options = [];
        foreach ($this->filteredOptions as $key => $option) {
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
