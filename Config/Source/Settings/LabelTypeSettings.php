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
namespace TIG\PostNL\Config\Source\Settings;

use Magento\Framework\Data\OptionSourceInterface;

class LabelTypeSettings implements OptionSourceInterface
{
    const TYPE_PDF = 'PDF';
    const TYPE_GIF = 'GIF';
    const TYPE_JPG = 'JPG';
    const TYPE_ZPL = 'ZPL';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        // @codingStandardsIgnoreStart
        $options = [
            ['value' => static::TYPE_PDF, 'label' => __('PDF')],
            ['value' => static::TYPE_GIF, 'label' => __('GIF')],
            ['value' => static::TYPE_JPG, 'label' => __('JPG')],
            ['value' => static::TYPE_ZPL, 'label' => __('ZPL (Zebra)')],
        ];
        // @codingStandardsIgnoreEnd
        return $options;
    }
}
