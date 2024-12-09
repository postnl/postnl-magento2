<?php

namespace TIG\PostNL\Service\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateRequestFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\ScopeInterface;

class QuoteToRateRequest
{
    /**
     * @var RateRequestFactory
     */
    private $rateRequestFactory;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;
    private ScopeConfigInterface $scopeConfig;

    public function __construct(
        RateRequestFactory $rateRequestFactory,
        CheckoutSession    $session,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->rateRequestFactory = $rateRequestFactory;
        $this->quote              = $session->getQuote();
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return RateRequest
     */
    public function get()
    {
        $store   = $this->quote->getStore();
        $address = $this->quote->getShippingAddress();
        $taxInclude = $this->scopeConfig->getValue(
            'tax/calculation/price_includes_tax',
            ScopeInterface::SCOPE_STORE
        );

        /** @var RateRequest $rateRequest */
        $rateRequest = $this->rateRequestFactory->create();
        $rateRequest->setWebsiteId($store->getWebsiteId());
        $rateRequest->setDestStreet($address->getStreetFull());
        $rateRequest->setDestPostcode($address->getPostcode());
        $rateRequest->setDestCity($address->getCity());
        $rateRequest->setDestCountryId($address->getCountryId());
        $rateRequest->setPackageQty($this->getQuantity());
        $rateRequest->setPackageWeight($this->getWeight());
        $rateRequest->setPackageValue($this->getValue());
        $rateRequest->setDestRegionId($address->getRegionId());
        $address = $this->quote->getShippingAddress();
        if ($address) {
            $baseSubtotal = $taxInclude ? $address->getSubtotalInclTax() : $address->getSubtotal();
            $packageValue = $taxInclude ? $address->getBaseSubtotalTotalInclTax() + $address->getBaseDiscountAmount() :
                $address->getBaseSubtotal() + $address->getBaseDiscountAmount();
        } else {
            $baseSubtotal = $this->quote->getSubtotal();
            $packageValue = $this->quote->getBaseSubtotalWithDiscount();
        }

        $rateRequest->setOrderSubtotal($baseSubtotal);
        $rateRequest->setFreeShipping((bool)$address->getFreeShipping());
        $rateRequest->setPackageValueWithDiscount($packageValue);
        $rateRequest->setShippingAddress($address);

        return $rateRequest;
    }

    /**
     * @return int
     */
    private function getQuantity()
    {
        $quantities = array_map(function (Item $item) {
            return $item->getQty();
        }, $this->quote->getAllItems());

        return array_sum($quantities);
    }

    /**
     * @return float|int
     */
    private function getWeight()
    {
        $weight = array_map(function (Item $item) {
            return $item->getRowWeight();
        }, $this->quote->getAllItems());

        return array_sum($weight);
    }

    /**
     * @return float|int
     */
    private function getValue()
    {
        $price = array_map(function (Item $item) {
            return $item->getRowTotalInclTax();
        }, $this->quote->getAllItems());

        return array_sum($price);
    }
}
