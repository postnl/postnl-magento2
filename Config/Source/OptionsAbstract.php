<?php

namespace TIG\PostNL\Config\Source;

// @codingStandardsIgnoreFile
use Laminas\Stdlib\ArrayUtils;

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
            'isEvening'            => true,
            'isSunday'             => false,
            'isGuaranteedDelivery' => true,
            'isToday'              => true,
            'countryLimitation'    => 'NL',
            'group'                => 'standard_options',
        ],
        '3087' => [
            'value'                => '3087',
            'label'                => 'Extra Cover',
            'isExtraCover'         => true,
            'isEvening'            => true,
            'isSunday'             => true,
            'isToday'              => true,
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
            'isToday'              => true,
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
            'isToday'              => true,
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
            'isToday'              => true,
            'isGuaranteedDelivery' => true,
            'isSameDay'            => true,
            'statedAddressOnly'    => true,
            'isBelgiumOnly'        => false,
            'countryLimitation'    => 'NL',
            'group'                => 'only_stated_address_options',
        ],
        '3389' => [
            'value'                => '3389',
            'label'                => 'Signature on delivery + Return when not home',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isToday'              => true,
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
            'isToday'              => true,
            'isGuaranteedDelivery' => true,
            'isSameDay'            => true,
            'statedAddressOnly'    => true,
            'isBelgiumOnly'        => false,
            'countryLimitation'    => 'NL',
            'group'                => 'only_stated_address_options',
        ],
        '3090' => [
            'value'                => '3090',
            'label'                => 'Delivery to neighbour + Return when not home',
            'isExtraCover'         => false,
            'isEvening'            => true,
            'isSunday'             => false,
            'isToday'              => true,
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
            'isToday'              => true,
            'isGuaranteedDelivery' => true,
            'isSameDay'            => true,
            'statedAddressOnly'    => true,
            'countryLimitation'    => 'NL',
            'group'                => 'only_stated_address_options',
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
        //Standard BE domestic options
        '4960' => [
            'value'                => '4960',
            'label'                => 'Belgium Standard, deliver to stated address only',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'standard_be_options',
        ],
        '4961' => [
            'value'                => '4961',
            'label'                => 'Belgium standard',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'standard_be_options',
        ],
        '4962' => [
            'value'                => '4962',
            'label'                => 'Belgium standard + Deliver to stated address only + Signature on delivery',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'standard_be_options',
        ],
        '4963' => [
            'value'                => '4963',
            'label'                => 'Belgium standard + Signature on delivery',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'standard_be_options',
        ],
        '4965' => [
            'value'                => '4965',
            'label'                => 'Belgium standard + Extra Cover',
            'isExtraCover'         => true,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'standard_be_options',
        ],
        // BE Domestic Pakjegemak Options
        '4878' => [
            'value'                => '4878',
            'label'                => 'Belgium Post Office + Extra Cover',
            'isExtraCover'         => true,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'pakjegemak_be_domestic_options',
        ],
        '4880' => [
            'value'                => '4880',
            'label'                => 'Belgium Post Office',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'pakjegemak_be_domestic_options',
        ],
        // BE Pakjegemak options
        '4936' => [
            'value'                => '4936',
            'label'                => 'Post Office Belgium',
            'isExtraCover'         => false,
            'isExtraEarly'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'pge'                  => false,
            'group'                => 'pakjegemak_be_options',
        ],
        // BENL options
        '4890' => [
            'value'                => '4890',
            'label'                => 'Delivery to neighbour',
            'isExtraCover'         => false,
            'isExtraEarly'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'NL',
            'countryOrigin'        => 'BE',
            'pge'                  => false,
            'group'                => 'be_nl_options',
        ],
        '4891' => [
            'value'                => '4891',
            'label'                => 'Delivery to neighbour + Signature on delivery',
            'isExtraCover'         => false,
            'isExtraEarly'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'NL',
            'countryOrigin'        => 'BE',
            'pge'                  => false,
            'group'                => 'be_nl_options',
        ],
        '4893' => [
            'value'                => '4893',
            'label'                => 'Delivery to stated address',
            'isExtraCover'         => false,
            'isExtraEarly'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'statedAddressOnly'    => true,
            'countryLimitation'    => 'NL',
            'countryOrigin'        => 'BE',
            'pge'                  => false,
            'group'                => 'be_nl_options',
        ],
        '4894' => [
            'value'                => '4894',
            'label'                => 'Delivery to stated address + Signature on delivery',
            'isExtraCover'         => false,
            'isExtraEarly'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'statedAddressOnly'    => true,
            'countryLimitation'    => 'NL',
            'countryOrigin'        => 'BE',
            'pge'                  => false,
            'group'                => 'be_nl_options',
        ],
        '4895' => [
            'value'                => '4895',
            'label'                => 'Delivery to stated address + Signature on delivery + Age check 18+',
            'isExtraCover'         => false,
            'isExtraEarly'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'statedAddressOnly'    => true,
            'countryLimitation'    => 'NL',
            'countryOrigin'        => 'BE',
            'pge'                  => false,
            'group'                => 'be_nl_options',
        ],
        '4896' => [
            'value'                => '4896',
            'label'                => 'Delivery to stated address + Signature on deliver + Return when not home',
            'isExtraCover'         => false,
            'isExtraEarly'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'statedAddressOnly'    => true,
            'countryLimitation'    => 'NL',
            'countryOrigin'        => 'BE',
            'pge'                  => false,
            'group'                => 'be_nl_options',
        ],
        '4897' => [
            'value'                => '4897',
            'label'                => 'Delivery to stated address + Signature on delivery + Extra cover',
            'isExtraCover'         => true,
            'isExtraEarly'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'statedAddressOnly'    => true,
            'countryLimitation'    => 'NL',
            'countryOrigin'        => 'BE',
            'pge'                  => false,
            'group'                => 'be_nl_options',
        ],
        '4898' => [
            'value'                => '4898',
            'label'                => 'Delivery to PostNL location + Signature on pickup',
            'isExtraCover'         => false,
            'isExtraEarly'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'statedAddressOnly'    => false,
            'countryLimitation'    => 'NL',
            'countryOrigin'        => 'BE',
            'pge'                  => false,
            'group'                => 'pakjegemak_be_nl_options',
        ],
        // EU Options
        '14907' => [
            'value'                => '14907',
            'label'                => 'Parcel EU to Consumer Track & Trace',
            'isDefault'            => 1,
            'isEvening'            => false,
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'eu_options',
        ],
        '24907' => [
            'value'                => '24907',
            'label'                => 'Parcel EU to Consumer Track & Trace Insured',
            'isEvening'            => false,
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'eu_options',
        ],
        '34907' => [
            'value'                => '34907',
            'label'                => 'Parcel EU to Consumer Track & Trace Insured Plus',
            'isEvening'            => false,
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'eu_options',
        ],
        '4941' => [
            'value'                => '4941',
            'label'                => 'Belgium Standard, deliver to stated address only',
            'isEvening'            => true,
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'be_options',
        ],
        '4946' => [
            'value'                => '4946',
            'label'                => 'Belgium standard',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'be_options',
        ],
        '4986' => [
            'value'                => '4986',
            'label'                => 'Belgium standard (Mon/Fri)',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'be_options',
        ],
        '4912' => [
            'value'                => '4912',
            'label'                => 'Belgium standard + Signature on delivery',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'be_options',
        ],
        '4914' => [
            'value'                => '4914',
            'label'                => 'Belgium standard + Signature on delivery + Extra Cover',
            'isExtraCover'         => true,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => 'BE',
            'group'                => 'be_options',
        ],
        // GlobalPack
        '14909' => [
            'value'                => '14909',
            'label'                => 'Parcel non-EU Track & Trace',
            'isDefault'            => 1,
            'isEvening'            => false,
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'global_options',
        ],
        '24909' => [
            'value'                => '24909',
            'label'                => 'Parcel non-EU Track & Trace Insured',
            'isEvening'            => false,
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'global_options',
        ],
        '34909' => [
            'value'                => '34909',
            'label'                => 'Parcel non-EU Track & Trace Insured Plus',
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
        // Package EPS B2B
        '44907' => [
            'value'                => '44907',
            'label'                => 'Parcel EU to Business Track & Trace',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'eps_package_options',
        ],
        '54907' => [
            'value'                => '54907',
            'label'                => 'Parcel EU to Business Track & Trace Insured',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'eps_package_options',
        ],
        '64907' => [
            'value'                => '64907',
            'label'                => 'Parcel EU to Business Track & Trace Insured Plus',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'eps_package_options',
        ],
        // Priority Products
        '6405' => [
            'value'                => '6405',
            'label'                => 'International Packet',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'priority_options',
        ],
        '6350' => [
            'value'                => '6350',
            'label'                => 'International Packet Track & Trace',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'priority_options',
        ],
        '6906' => [
            'value'                => '6906',
            'label'                => 'International Packet Track & Trace Insured',
            'isExtraCover'         => false,
            'isEvening'            => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'priority_options',
        ],
        '6440' => [
            'value'                => '6440',
            'label'                => 'International Boxable Packet',
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'boxable_packets',
        ],
        '6972' => [
            'value'                => '6972',
            'label'                => 'International Boxable Packet Track & Trace',
            'isExtraCover'         => false,
            'isSunday'             => false,
            'isGuaranteedDelivery' => false,
            'countryLimitation'    => false,
            'group'                => 'boxable_packets',
        ]
    ];

    protected $groups = [
	    'standard_options'            => 'Domestic options',
	    'standard_be_options'         => 'Domestic BE options',
	    'be_nl_options'               => 'BE to NL options',
	    'pakjegemak_options'          => 'Post Office options',
	    'pakjegemak_be_nl_options'    => 'Post Office BE-NL options ',
	    'pakjegemak_be_options'       => 'Post Office BE options',
	    'pakjegemak_be_domestic_options' => 'Post Office BE options',
	    'eu_options'                  => 'EU options',
	    'be_options'                  => 'BE options',
	    'global_options'              => 'Non-EU options',
	    'buspakje_options'            => 'Letter Box Parcel options',
	    'extra_at_home_options'       => 'Extra@Home options',
	    'id_check_options'            => 'ID Check options',
	    'id_check_pakjegemak_options' => 'ID Check Post Office options',
	    'cargo_options'               => 'Cargo options',
	    'eps_package_options'         => 'Package options',
	    'priority_options'            => 'International Packet',
        'boxable_packets'             => 'International Boxable Packets',
        'only_stated_address_options' => 'Only Stated Address options',

    ];

	protected $groupToLabel = [
		'standard_options'            => 'Domestic',
		'standard_be_options'         => 'Domestic BE',
		'be_nl_options'               => 'BE to NL',
		'pakjegemak_options'          => 'Post Office',
		'pakjegemak_be_nl_options'    => 'Post Office',
		'pakjegemak_be_options'       => 'Post Office Belgium',
		'pakjegemak_be_domestic_options' => 'Post Office Belgium',
		'eu_options'                  => 'EU Parcel',
		'be_options'                  => 'EPS BE',
		'global_options'              => 'Non-EU Parcel',
		'buspakje_options'            => 'Letter Box',
		'extra_at_home_options'       => 'Extra@Home',
		'id_check_options'            => 'ID Check',
		'id_check_pakjegemak_options' => 'ID Check Post Office',
		'cargo_options'               => 'Cargo',
		'eps_package_options'         => 'Package',
		'priority_options'            => 'International Packet',
        'boxable_packets'             => 'International Boxable Packets',
        'only_stated_address_options' => 'Delivery to stated address only'
    ];

    protected $typeToComment = [
        'Daytime'           => '',
        'Evening'           => 'Evening',
        'ExtraAtHome'       => '',
        'Extra@Home'        => '',
        'Sunday'            => 'Sunday',
        'Today'             => 'Today',
        'Noon'              => 'Morning Delivery',
        'PG'                => '',
        'EPS'               => '',
        'GP'                => '',
        'Letterbox Package' => '',
        'letterbox_package' => '',
        'Boxable Packets'   => '',
        'boxable_packets'   => '',
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
        }, ArrayUtils::ARRAY_FILTER_USE_BOTH);

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
            }, ArrayUtils::ARRAY_FILTER_USE_BOTH);

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
            $options[] = [
                'value' => $option['value'],
                'label' => '[' . $this->getBaseCodeFromKey($option['value']) . '] ' . __($option['label'])
            ];
        }

        return $options;
    }

    public function getBaseCodeFromKey(string $key): string
    {
        return strlen($key) < 5 ? $key : substr($key, 1);
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
        }, ArrayUtils::ARRAY_FILTER_USE_KEY);

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
