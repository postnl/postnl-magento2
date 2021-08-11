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
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Config\Source\Options;

use \Magento\Framework\Option\ArrayInterface;

class FirstLabelPosition implements ArrayInterface
{
    const BOTTOM_RIGHT  = '0';
    const BOTTOM_LEFT = '1';
    const TOP_RIGHT = '2';
    const TOP_LEFT = '3';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        return [
            ['value' => static::BOTTOM_RIGHT, 'label' => __('Bottom Right')],
            ['value' => static::BOTTOM_LEFT, 'label' => __('Bottom Left')],
            ['value' => static::TOP_RIGHT, 'label' => __('Top Right')],
            ['value' => static::TOP_LEFT, 'label' => __('Top Left')],
        ];
        // @codingStandardsIgnoreEnd
    }
}
