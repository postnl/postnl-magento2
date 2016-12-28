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
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Sales\Model\Order;

/**
 * Class Data
 *
 * @package TIG\PostNL\Helper
 */
class Data extends AbstractHelper
{
    /**
     * @param Order $order
     *
     * @return bool
     */
    public function isPostNLOrder(Order $order)
    {
        $shippingMethod = $order->getShippingMethod();

        return $shippingMethod == 'tig_postnl_regular';
    }

    /**
     * @return string
     */
    public function getCurrentTimeStamp()
    {
        $stamp = new \DateTime('now', new \DateTimeZone('UTC'));
        return $stamp->format('d-m-Y H:i:s');
    }

    /**
     * @param $date
     * @return \DateTime
     */
    public function getDateYmd($date = false)
    {
        $stamp = new \DateTime('now', new \DateTimeZone('UTC'));
        if ($date) {
            $stamp = new \DateTime($date, new \DateTimeZone('UTC'));
        }

        return $stamp->format('Y-m-d');
    }

    /**
     * @return bool|string
     */
    public function getTommorowsDate()
    {
        $dateTime = new \DateTime('now', new \DateTimeZone('UTC'));
        return date('Y-m-d ' . $dateTime->format('H:i:s'), strtotime('tommorow'));
    }
}
