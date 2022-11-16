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

use Magento\Framework\Option\ArrayInterface;

class CutOffSettings implements ArrayInterface
{
    /**
     * Returns options for 0:00 till 23:45
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];

        for ($hour = 0; $hour < 24; $hour++) {
            // @codingStandardsIgnoreLine
            $options = array_merge($options, $this->addHour($hour));
        }

        return $options;
    }

    /**
     * Returns options for 0:00 till 10:00
     *
     * @return array
     */
    public function getTodayOptions()
    {
        $options = [];

        for ($hour = 0; $hour < 10; $hour++) {
            // @codingStandardsIgnoreLine
            $options = array_merge($options, $this->addHour($hour));
        }

        // addHour() adds till x:45, but we want to end at an x:00, so add 10:00 separately.
        // @codingStandardsIgnoreLine
        $options[] = ['value' => '10:00:00', 'label' => __('10:00')];

        return $options;
    }

    /**
     * @param $hour
     *
     * @return array
     */
    private function addHour($hour)
    {
        $hour    = str_pad($hour, 2, '0', STR_PAD_LEFT);
        $options = [];

        // @codingStandardsIgnoreStart
        $options[] = ['value' => $hour . ':00:00', 'label' => __($hour . ':00')];
        $options[] = ['value' => $hour . ':15:00', 'label' => __($hour . ':15')];
        $options[] = ['value' => $hour . ':30:00', 'label' => __($hour . ':30')];
        $options[] = ['value' => $hour . ':45:00', 'label' => __($hour . ':45')];
        // @codingStandardsIgnoreEnd

        return $options;
    }
}
