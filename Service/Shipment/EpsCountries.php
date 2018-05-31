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
namespace TIG\PostNL\Service\Shipment;

class EpsCountries
{
    /**
     * Array of countries to which PostNL ships using EPS. Other EU countries are shipped to using Global Pack
     * https://developer.postnl.nl/browse-apis/send-and-track/products/ => Destination EU
     *
     * Parcels to the Channel Islands, the Canary Islands and Malta can only be sent with the Global Pack product.
     * ==== Channel Islands ====
     * 'GG', // Guernsey
     * 'JE', // Jersy
     * 'IM', // Isle of Man
     *
     * ==== Canary Islands ====
     * 'IC' // Las Palmas, Santa Cruz and Melilla
     *
     * @var array
     */
    const ALL = [
        'AT', // Austria
        'BE', // Belgium
        'BG', // Bulgaria
        'CZ', // Czech Republic
        'DK', // Denmark (Excluding Faroe Islands and Greenland)
        'EE', // Estonia
        'FI', // Finland
        'FR', // France (Including Monaco and Corsica. Excluding Andorra)
        'DE', // Germany
        'GB', // Great Britain (Including Northern Ireland. Excluding Gibraltar and the Channel Islands)
        'GR', // Greece
        'HU', // Hungary
        'IE', // Ireland
        'IT', // Italy (Excluding San Marino and Vatican City)
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
    ];
}
