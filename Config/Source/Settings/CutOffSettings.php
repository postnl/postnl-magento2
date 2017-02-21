<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Config\Source\Settings;

use \Magento\Framework\Option\ArrayInterface;

/**
 * Class CutOffSettings
 *
 * @package TIG\PostNL\Config\Source\Settings
 */
class CutOffSettings implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            // @codingStandardsIgnoreLine
            ['value' => '', 'label' => __('No cut-off time')],
        ];

        for ($hour = 0; $hour < 24; $hour++) {
            $options = array_merge($options, $this->addHour($hour));
        }

        return $options;
    }

    /**
     * @param $hour
     *
     * @return array
     */
    private function addHour($hour)
    {
        $hour = str_pad($hour, 2, '0', STR_PAD_LEFT);

        $options = [];
        if ($hour !== '00') {
            // @codingStandardsIgnoreLine
            $options[] = ['value' => $hour . ':00:00', 'label' => __($hour . ':00')];
        }

        // @codingStandardsIgnoreStart
        $options[] = ['value' => $hour . ':15:00', 'label' => __($hour . ':15')];
        $options[] = ['value' => $hour . ':30:00', 'label' => __($hour . ':30')];
        $options[] = ['value' => $hour . ':45:00', 'label' => __($hour . ':45')];
        // @codingStandardsIgnoreEnd

        return $options;
    }
}
