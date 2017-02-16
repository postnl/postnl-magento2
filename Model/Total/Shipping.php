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
namespace TIG\PostNL\Model\Total;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\FreeShippingInterface;
use TIG\PostNL\Service\Order\CurrentPostNLOrder;

class Shipping extends Quote\Address\Total\Shipping
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var FreeShippingInterface
     */
    protected $freeShipping;

    /**
     * @var GetCurrentPostNLOrder
     */
    private $currentPostNLOrder;

    /**
     * @param PriceCurrencyInterface $priceCurrency
     * @param FreeShippingInterface  $freeShipping
     * @param CurrentPostNLOrder     $currentPostNLOrder
     */
    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        FreeShippingInterface $freeShipping,
        CurrentPostNLOrder $currentPostNLOrder
    ) {
        $this->setCode('shipping');
        $this->priceCurrency = $priceCurrency;
        $this->freeShipping = $freeShipping;
        $this->currentPostNLOrder = $currentPostNLOrder;
    }

    /**
     * @param Quote                       $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Quote\Address\Total         $total
     *
     * @return $this
     */
    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $shipping = $shippingAssignment->getShipping();
        $address = $shipping->getAddress();
        $rate = $this->getRate($shipping->getMethod(), $address);

        if (!$rate) {
            return $this;
        }

        $this->processTotal($quote, $total, $rate, $address);

        return $this;
    }

    /**
     * Get Shipping label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('PostNL');
    }

    /**
     * @param                                    $method
     * @param \Magento\Quote\Model\Quote\Address $address
     *
     * @return $this
     */
    private function getRate($method, $address)
    {
        if ($method != 'tig_postnl_regular') {
            return null;
        }

        $rate = array_filter($address->getAllShippingRates(), function (Quote\Address\Rate $rate) use ($method) {
            return $rate->getCode() == $method;
        });

        if (!$rate) {
            return null;
        }

        return array_shift($rate);
    }

    /**
     * @param Quote               $quote
     * @param Quote\Address\Total $total
     * @param                     $rate
     * @param Quote\Address       $address
     */
    private function processTotal(Quote $quote, Quote\Address\Total $total, $rate, $address)
    {
        $fee = $this->getFee();
        $store       = $quote->getStore();
        $amountPrice = $this->priceCurrency->convert(
            $rate->getPrice() + $fee,
            $store
        );

        $total->setTotalAmount($this->getCode(), $amountPrice);
        $total->setBaseTotalAmount($this->getCode(), $rate->getPrice());
        $address->setShippingDescription($rate->getCarrierTitle());
        $total->setBaseShippingAmount($rate->getPrice());
        $total->setShippingAmount($amountPrice);
        $total->setShippingDescription($address->getShippingDescription());
    }

    /**
     * @return float
     */
    private function getFee()
    {
        $order = $this->currentPostNLOrder->get();

        if (!$order) {
            return 0;
        }

        return $order->getFee();
    }
}
