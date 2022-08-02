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
namespace TIG\PostNL\Config\Source\Matrixrate;

use Magento\Framework\Option\ArrayInterface;

class ParcelType implements ArrayInterface
{
    const PARCEL_TYPE_PAKJEGEMAK        = 'pakjegemak';
    const PARCEL_TYPE_EXTRA_AT_HOME     = 'extra@home';
    const PARCEL_TYPE_REGULAR           = 'regular';
    const PARCEL_TYPE_LETTERBOX_PACKAGE = 'letterbox_package';


    /**
     * @return array
     */
    public function toOptionArray($isMultiselect = false)
    {
        // @codingStandardsIgnoreStart
        $options = [
            [
                'value' => self::PARCEL_TYPE_PAKJEGEMAK,
                'label' => __('Pakjegemak'),
            ],
            [
                'value' => self::PARCEL_TYPE_EXTRA_AT_HOME,
                'label' => __('Extra at home'),
            ],
            [
                'value' => self::PARCEL_TYPE_REGULAR,
                'label' => __('Regular'),
            ],
            [
                'value' => self::PARCEL_TYPE_LETTERBOX_PACKAGE,
                'label' => __('Letterbox package'),
            ],
        ];
        // @codingStandardsIgnoreEnd
        if (!$isMultiselect) {
            array_unshift($options, ['value' => '', 'label' => __('--Please Select--')]);
        }

        return $options;
    }
}
