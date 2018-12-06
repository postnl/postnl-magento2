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

class GuaranteedOptions
{
    const GUARANTEED_TYPE_CARGO   = 'cargo';
    const GUARANTEED_TYPE_PACKAGE = 'package';

    private $availableProductOptions = [
        '1000' => [
            'Characteristic' => '118',
            'Option'         => '007',
        ],
        '1200' => [
            'Characteristic' => '118',
            'Option'         => '008',
        ],
        '1400' => [
            'Characteristic' => '118',
            'Option'         => '013',
        ],
        '1700' => [
            'Characteristic' => '118',
            'Option'         => '012',
        ]
    ];

    /**
     * @param      $time
     * @param bool $flat
     *
     * @return array|null
     */
    public function get($time, $flat = false)
    {
        if (!array_key_exists($time, $this->availableProductOptions)) {
            return null;
        }

        if ($flat) {
            return $this->availableProductOptions[$time];
        }

        return ['ProductOption' => $this->availableProductOptions[$time]];
    }
}
