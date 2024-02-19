<?php

namespace TIG\PostNL\Plugin\Quote\Address\Total;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total\Shipping as TotalShipping;
use TIG\PostNL\Service\Order\CurrentPostNLOrder;

class Shipping
{
    /**
     * @var PriceCurrencyInterface
     */
    private $priceCurrency;

    /**
     * @var CurrentPostNLOrder
     */
    private $currentPostNLOrder;

    /**
     * @var TotalShipping
     */
    private $subject;

    /**
     * @param CurrentPostNLOrder     $currentPostNLOrder
     * @param PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        CurrentPostNLOrder $currentPostNLOrder,
        PriceCurrencyInterface $priceCurrency
    ) {
        $this->currentPostNLOrder = $currentPostNLOrder;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @param TotalShipping               $subject
     * @param callable                    $proceed
     * @param Quote                       $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Quote\Address\Total         $total
     *
     * @return $this
     */
    public function aroundCollect(
        TotalShipping $subject,
        callable $proceed,
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Quote\Address\Total $total
    ) {
        $this->subject = $subject;
        $result        = $proceed($quote, $shippingAssignment, $total);
        $shipping      = $shippingAssignment->getShipping();
        $address       = $shipping->getAddress();
        $rate          = $this->getRate($shipping->getMethod(), $address);

        if (!$rate) {
            return $result;
        }

        $this->processTotal($quote, $total, $rate, $address);
    }

    /**
     * @param                  $method
     * @param AddressInterface $address
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
     * @param AddressInterface    $address
     */
    private function processTotal(Quote $quote, Quote\Address\Total $total, $rate, $address)
    {
        $fee         = $this->getFee($quote);
        $store       = $quote->getStore();
        $amountPrice = $this->priceCurrency->convert(
            $rate->getPrice() + $fee,
            $store
        );

        $total->setTotalAmount($this->subject->getCode(), $amountPrice);
        $total->setBaseTotalAmount($this->subject->getCode(), $rate->getPrice() + $fee);
        $address->setShippingDescription($rate->getCarrierTitle());
        $total->setBaseShippingAmount($rate->getPrice() + $fee);
        $total->setShippingAmount($amountPrice);
        $total->setShippingDescription($address->getShippingDescription());
    }

    /**
     * @param Quote $quote
     * @return float
     */
    private function getFee($quote)
    {
        $order = $this->currentPostNLOrder->get($quote->getId());

        if (!$order) {
            return 0;
        }

        return $order->getFee();
    }
}
