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
namespace TIG\PostNL\Service\Shipment;

use TIG\PostNL\Service\Wrapper;

class FeeHandler
{
    /**
     * @var Wrapper\QuoteInterface
     */
    private $quoteWrapper;
    /**
     * @var Wrapper\CheckoutSessionInterface
     */
    private $checkoutSession;

    /**
     * @param Wrapper\QuoteInterface           $quoteWrapper
     * @param Wrapper\CheckoutSessionInterface $checkoutSession
     */
    public function __construct(
        Wrapper\QuoteInterface $quoteWrapper,
        Wrapper\CheckoutSessionInterface $checkoutSession
    ) {
        $this->quoteWrapper = $quoteWrapper;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     */
    public function add($total)
    {
        $shippingAmount = $this->getShippingAmount();

        if ($shippingAmount === null) {
            return;
        }

        $total->setShippingAmount($shippingAmount);
        $total->setBaseShippingAmount($shippingAmount);
        $total->setShippingInclTax($shippingAmount);
        $total->setBaseShippingInclTax($shippingAmount);
    }

    private function getShippingAmount()
    {
        return 13.37;

        $baseAmount = $this->checkoutSession->getValue('tig_postnl_regular_base_amount');
        $feeAmount = $this->checkoutSession->getValue('tig_postnl_regular_fee_amount');

        if ($baseAmount === null || $feeAmount === null) {
            return null;
        }

        return $baseAmount + $feeAmount;
    }
}
