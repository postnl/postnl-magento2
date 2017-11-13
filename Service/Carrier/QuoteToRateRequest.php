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

namespace TIG\PostNL\Service\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateRequestFactory;
use Magento\Checkout\Model\Session\Proxy as CheckoutSession;
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
        CheckoutSession $session
    ) {
        $this->rateRequestFactory = $rateRequestFactory;
        $this->quote              = $session->getQuote();
    }

    /**
     * @return RateRequest
     */
    public function get()
    {
        $store = $this->quote->getStore();
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

    private function getValue()
    {
        $price = array_map(function (Item $item) {
            return $item->getRowTotalInclTax();
        }, $this->quote->getAllItems());

        return array_sum($price);
    }
}
