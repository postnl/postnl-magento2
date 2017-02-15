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
namespace TIG\PostNL\Plugin\Order;

/**
 * Class ShipmentPlugin
 *
 * @package TIG\PostNL\Plugin\Order
 */
class ShipmentPlugin
{
    /**
     * The default getShippingMethod does a explode with the underscore '_' as delimeter.
     * And that will return the wrong carrier.
     *
     * @param \Magento\Sales\Model\Order $subject
     * @param string|\Magento\Framework\DataObject $result
     *
     * @return string|\Magento\Framework\DataObject
     */
    // @codingStandardsIgnoreLine
    public function afterGetShippingMethod($subject, $result)
    {
        if (is_string($result)) {
            return $result;
        }

        $carrierCode = $result->getData('carrier_code');
        $method      = $result->getData('method');

        if ($carrierCode == 'tig' && $method == 'postnl_regular') {
            $result->setData('carrier_code', 'tig_postnl');
            $result->setData('method', 'regular');
        }

        return $result;
    }
}
