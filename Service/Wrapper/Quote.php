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
