<?php

namespace TIG\PostNL\Service\Wrapper;

use Magento\Quote\Model\Quote as MagentoQuote;

interface QuoteInterface
{
    /**
     * @param MagentoQuote $quote
     *
     * @return mixed
     */
    // @codingStandardsIgnoreLine
    public function setQuote(MagentoQuote $quote);

    /**
     * @return MagentoQuote
     */
    public function getQuote();

    /**
     * @return int
     */
    public function getQuoteId();

    /**
     * @return MagentoQuote\Address
     */
    public function getShippingAddress();

    /**
     * @return MagentoQuote\Address
     */
    public function getBillingAddress();

    /**
     * @return array
     */
    public function getAllItems();

    /**
     * @return int
     */
    public function getStoreId();
}
