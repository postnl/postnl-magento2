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

class ProductOptions
{
    /**
     * These shipment types need specific product options.
     *
     * @var array
     */
    private $availableProductOptions = [
            'pge'     => [
                'Characteristic' => '118',
                'Option'         => '002',
            ],
            'evening' => [
                'Characteristic' => '118',
                'Option'         => '006',
            ],
            'sunday'  => [
                'Characteristic' => '101',
                'Option'         => '008',
            ],
            'idcheck' => [
                'Characteristic' => '002',
                'Option'         => '014'
            ],
            'idcheck_pg' => [
                'Characteristic' => '002',
                'Option'         => '014'
            ],
        ];

    /**
     * @param string $type
     * @param bool   $flat
     *
     * @return null
     */
    public function get($type, $flat = false)
    {
        $type = strtolower($type);

        if (!array_key_exists($type, $this->availableProductOptions)) {
            return null;
        }

        if ($flat) {
            return $this->availableProductOptions[$type];
        }

        return ['ProductOption' => $this->availableProductOptions[$type]];
    }
}
