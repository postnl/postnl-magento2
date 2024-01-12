<?php

namespace TIG\PostNL\Service\Wrapper;

use Magento\Quote\Model\Quote as MagentoQuote;

class Quote implements QuoteInterface
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var MagentoQuote
     */
    private $quote;

    /**
     * @param CheckoutSession $checkoutSession
     */
    public function __construct(
        CheckoutSession $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @param MagentoQuote $quote
     *
     * @return mixed
     */
    // @codingStandardsIgnoreLine
    public function setQuote(MagentoQuote $quote)
    {
        $this->quote = $quote;
    }

    /**
     * @return MagentoQuote
     */
    public function getQuote()
    {
        if ($this->quote === null) {
            $this->quote = $this->checkoutSession->getQuote();
        }

        return $this->quote;
    }

    /**
     * @return MagentoQuote
     */
    public function getQuoteId()
    {
        $this->getQuote();

        return $this->quote->getId();
    }

    /**
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getShippingAddress()
    {
        $quote = $this->getQuote();

        return $quote->getShippingAddress();
    }

    /**
     * @return \Magento\Quote\Model\Quote\Address
     */
    public function getBillingAddress()
    {
        $quote = $this->getQuote();

        return $quote->getBillingAddress();
    }

    /**
     * @return array
     */
    public function getAllItems()
    {
        return $this->getQuote()->getAllItems();
    }

    /**
     * @return int
     */
    public function getStoreId()
    {
        return $this->getQuote()->getStoreId();
    }
}
