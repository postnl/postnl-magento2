<?php

namespace TIG\PostNL\Service\Shipment;

class EpsCountries
{
    /**
     * Array of countries to which PostNL ships using EPS. Other EU countries are shipped, using Global Pack.
     * https://developer.postnl.nl/browse-apis/send-and-track/products/ => Destination EU
     *
     * Parcels to the Channel Islands and the Canary Islands can only be sent with the Global Pack product.
     *
     * ==== Great Britain ====
     * 'GG', // Guernsey
     * 'JE', // Jersy
     * 'IM', // Isle of Man
     * 'GI', // Gibraltar
     * 'MT', // Malta
     *
     * ==== Denmark ====
     * 'FO', // Faeröer (Faroe Islands)
     * 'GL', // Greenland
     *
     * ==== SPAIN ====
     * 'IC', // Las Palmas, Santa Cruz and Melilla
     *
     * ==== Italy ====
     * 'VA', // Vatican City
     * 'SM', // San Marino
     *
     * ==== France ====
     * 'AD', // Andorra
     *
     * @var array
     */
    const ALL = [
        'AT', // Austria
        'BE', // Belgium
        'BG', // Bulgaria
        'CZ', // Czech Republic
        'DK', // Denmark (Excluding Faroe Islands and GL: Greenland)
        'EE', // Estonia
        'FI', // Finland
        'FR', // France (Including orsica. Excluding Andorra)
        'DE', // Germany
        'GR', // Greece
        'HU', // Hungary
        'IE', // Ireland
        'IT', // Italy (Excluding (SM: San Marino and Vatican City)
        'LV', // Latvia
        'LT', // Lithuania
        'LU', // Luxembourg
        'NL', // Netherlands
        'PL', // Poland
        'PT', // Portugal (Including Azores and Madeira)
        'RO', // Romania
        'SK', // Slovakia
        'SI', // Slovenia
        'ES', // Spain (Including Balearic Islands. Excluding Canary Islands, Melilla and Ceuta)
        'SE', // Sweden
        'MC', // Monaco (Is its own country, Is EPS because France is EPS)
        'MT', // Malta
        'HR', // Croatia
        'CY'  // Cyprus
    ];
}
