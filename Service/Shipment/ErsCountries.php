<?php

namespace TIG\PostNL\Service\Shipment;

class ErsCountries
{
    /**
     * Array of countries to which PostNL supports EasyReturnService EPS.
     * @var array
     */
    public const ALL = [
        'AT', // Austria
        'BE', // Belgium
        'BG', // Bulgaria
        'HR', // Croatia
        'CZ', // Czech Republic
        'DK', // Denmark
        'EE', // Estonia
        'FI', // Finland
        'FR', // France
        'DE', // Germany
        'GR', // Greece
        'HU', // Hungary
        'IS', // Iceland
        'IE', // Ireland
        'IT', // Italy
        'LV', // Latvia
        'LT', // Lithuania
        'LU', // Luxembourg
        'MT', // Malta
        'NZ', // New Zealand
        'NO', // Norway
        'PL', // Poland
        'PT', // Portugal (Including Azores and Madeira)
        'RO', // Romania
        'SK', // Slovakia
        'SI', // Slovenia
        'ES', // Spain (Including Balearic Islands. Excluding Canary Islands, Melilla and Ceuta)
        'SE', // Sweden,
        'CH', // Switzerland,
        'UK' // United Kingdom
    ];

    public static function isIncluded(string $countryId): bool
    {
        return in_array($countryId, self::ALL, true);
    }
}
