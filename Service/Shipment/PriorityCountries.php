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

class PriorityCountries
{
    /**
     * PEPS uses his own EPS and Globalpack countries. Which is different than
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
        'US'
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
        'GB',
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
