<?php

namespace TIG\PostNL\Service\Quote;

use Magento\Checkout\Model\Session as CheckoutSession;
use TIG\PostNL\Service\Options\ItemsToOption;

class CheckIfQuoteHasOption
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var ItemsToOption
     */
    private $itemsToOption;

    /**
     * @param CheckoutSession $checkoutSession
     * @param ItemsToOption $itemsToOption
     */
    public function __construct(
        // @codingStandardsIgnoreLine
        CheckoutSession $checkoutSession,
        ItemsToOption $itemsToOption
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->itemsToOption = $itemsToOption;
    }

    /**
     * @param string $productCode
     *
     * @return bool
     */
    public function get($productCode)
    {
        $quote = $this->checkoutSession->getQuote();
        if (!$quote) {
            return false;
        }

        $option = $this->itemsToOption->get($quote->getAllItems());

        return $option == $productCode;
    }
}
