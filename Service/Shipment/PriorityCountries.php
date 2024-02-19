<?php

namespace TIG\PostNL\Service\Shipment;

class PriorityCountries
{
    /**
     * Priority uses his own EPS and Globalpack countries. Which is different than
     * \TIG\PostNL\Service\Shipment\EpsCountries
     * If country does not exist in both arrays a fallback to Regular Globalpack
     * is provided.
     *
     * https://jira.tig.nl/browse/POSTNLM2-741
     */

    // ROW
    const GLOBALPACK = [
        'AU',
        'BR',
        'BY',
        'CA',
        'CH',
        'HK',
        'ID',
        'IL',
        'IS',
        'JP',
        'KR',
        'LB',
        'MY',
        'NO',
        'NZ',
        'RU',
        'SA',
        'SG',
        'TH',
        'TR',
        'US',
        'GB'
    ];

    // NOT ROW
    /**
     * Belgium does't have to be enlisted in priority, because regular EPS
     * can already deliver to BE in one day.
     */
    const EPS = [
        'AT',
        'CY',
        'DE',
        'DK',
        'EE',
        'ES',
        'FI',
        'FR',
        'GR',
        'HR',
        'HU',
        'IE',
        'IT',
        'LT',
        'LU',
        'LV',
        'MT',
        'PL',
        'PT',
        'RS',
        'SE',
        'SI',
        'SK'
    ];
}
