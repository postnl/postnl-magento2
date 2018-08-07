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

class BarcodeType implements ArrayInterface
{
    /**
     * These are the only Zend Barcode types
     * @see \Zend_Barcode_Object_ObjectAbstract
     * which support full length increment ID's.
     *
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => 'code25', 'label' => __('Code 25')],
            ['value' => 'code39', 'label' => __('Code 39')],
            ['value' => 'code128', 'label' => __('Code 128')],
            ['value' => 'royalmail', 'label' => __('Royalmail')],
        ];
        // @codingStandardsIgnoreEnd

        return $options;
    }
}
