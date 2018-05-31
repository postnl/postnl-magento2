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
namespace TIG\PostNL\Config\Source\LabelAndPackingslip;

use Magento\Framework\Option\ArrayInterface;

class ReferenceType implements ArrayInterface
{
    const REFEENCE_TYPE_NONE        = 'none';
    const REFEENCE_TYPE_SHIPMENT_ID = 'shipment_increment_id';
    const REFEENCE_TYPE_ORDER_ID    = 'order_increment_id';
    const REFEENCE_TYPE_CUSTOM      = 'custom';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => self::REFEENCE_TYPE_NONE, 'label' => __('None')],
            ['value' => self::REFEENCE_TYPE_SHIPMENT_ID, 'label' => __('Shipment ID')],
            ['value' => self::REFEENCE_TYPE_ORDER_ID, 'label' => __('Order ID')],
            ['value' => self::REFEENCE_TYPE_CUSTOM, 'label' => __('Use a custom value')]
        ];
        // @codingStandardsIgnoreEnd

        return $options;
    }
}
