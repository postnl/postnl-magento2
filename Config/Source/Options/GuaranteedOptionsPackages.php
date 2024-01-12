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
namespace TIG\PostNL\Config\Source\Options;

use Magento\Framework\Data\OptionSourceInterface;

class GuaranteedOptionsPackages implements OptionSourceInterface
{
    /**
     * @see \TIG\PostNL\Service\Shipment\GuaranteedOptions
     * @return array
     */
    // @codingStandardsIgnoreStart
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Delivery before 10:00'),
                'value' => '1000'
            ],
            [
                'label' => __('Delivery before 12:00'),
                'value' => '1200'
            ],
            [
                'label' => __('Delivery before 17:00'),
                'value' => '1700'
            ]
        ];
    }
    // @codingStandardsIgnoreEnd
}
