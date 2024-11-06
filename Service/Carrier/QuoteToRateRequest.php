<?php

namespace TIG\PostNL\Service\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateRequestFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Quote\Model\Quote\Item;

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

    public function __construct(
        RateRequestFactory $rateRequestFactory,
        CheckoutSession    $session
    ) {
        $this->rateRequestFactory = $rateRequestFactory;
        $this->quote              = $session->getQuote();
    }

    /**
     * @return RateRequest
     */
    public function get()
    {
        $store   = $this->quote->getStore();
        $address = $this->quote->getShippingAddress();

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
        $rateRequest->setOrderSubtotal($this->quote->getSubtotal());
        $rateRequest->setFreeShipping((bool)$address->getFreeShipping());
        $rateRequest->setPackageValueWithDiscount($address->getBaseSubtotalWithDiscount());
        $rateRequest->setShippingAddress($address);

        return $rateRequest;
    }

    public function getByUpdatedAddress(string $country, string $postcode): RateRequest
    {
        $request = $this->get();
        $request->setDestCountryId($country);
        $request->setDestPostcode($postcode);

        $shippingAddress = $request->getShippingAddress();
        $shippingAddress->setCountryId($country);
        $shippingAddress->setPostcode($postcode);
        $request->setShippingAddress($shippingAddress);
        return $request;
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
