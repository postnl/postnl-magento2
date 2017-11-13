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
namespace TIG\PostNL\Config\Source\Carrier;

use Magento\Framework\Option\ArrayInterface;

class RateType implements ArrayInterface
{
    const CARRIER_RATE_TYPE_FLAT = 'flat';
    const CARRIER_RATE_TYPE_TABLE = 'table';
    const CARRIER_RATE_TYPE_MATRIX = 'matrix';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            [
                'value' => self::CARRIER_RATE_TYPE_FLAT,
                'label' => __('Flat'),
            ],
            [
                'value' => self::CARRIER_RATE_TYPE_TABLE,
                'label' => __('Table'),
            ],
            [
                'value' => self::CARRIER_RATE_TYPE_MATRIX,
                'label' => __('Matrix'),
            ],
        ];
        // @codingStandardsIgnoreEnd

        return $options;
    }
}
