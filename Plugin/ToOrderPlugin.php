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
namespace TIG\PostNL\Plugin;

use \Magento\Checkout\Model\Session;
use \Magento\Quote\Model\Quote\Address\ToOrder as QuoteAddressToOrder;
use \Magento\Sales\Api\Data\OrderInterface;
use \Magento\Quote\Model\Quote\Address\ToOrderAddress;
use \TIG\PostNL\Helper\DeliveryOptions\PickupAddress;

/**
 * Class ToOrderPlugin
 *
 * @package TIG\PostNL\Plugin
 */
class ToOrderPlugin
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var ToOrderAddress
     */
    private $quoteAddressToOrderAddress;
    /**
     * @var PickupAddress
     */
    private $pickupAddressHelper;

    /**
     * @param Session        $session
     * @param ToOrderAddress $toOrderAddress
     * @param PickupAddress  $pickupAddress
     */
    public function __construct(
        Session $session,
        ToOrderAddress $toOrderAddress,
        PickupAddress $pickupAddress
    ) {
        $this->checkoutSession = $session;
        $this->quoteAddressToOrderAddress = $toOrderAddress;
        $this->pickupAddressHelper = $pickupAddress;
    }

    /**
     * @param QuoteAddressToOrder $subject
     * @param OrderInterface      $order
     *
     * @return OrderInterface
     */
    public function afterConvert(
        QuoteAddressToOrder $subject,
        OrderInterface $order
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote         = $this->checkoutSession->getQuote();
        $quotePgAddres = $this->pickupAddressHelper->getPakjeGemakAddressInQuote($quote);

        $orderPgAddress = false;
        if ($quotePgAddres->getId()) {
            /** @var \Magento\Sales\Api\Data\OrderAddressInterface $orderPgAddress */
            $orderPgAddress = $this->quoteAddressToOrderAddress->convert($quotePgAddres);
        }

        if ($orderPgAddress->getEntityId()) {
            /** @noinspection PhpUndefinedMethodInspection */
            $order->setShippingAddress($orderPgAddress);
        }

        return $order;
    }
}
