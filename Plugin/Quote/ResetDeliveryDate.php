<?php

namespace TIG\PostNL\Plugin\Quote;

use \Magento\Quote\Model\Quote;
use \Magento\Checkout\Model\Session;

class ResetDeliveryDate
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * ResetDeliveryDate constructor.
     *
     * @param Session $session
     */
    public function __construct(
        Session $session
    ) {
        $this->checkoutSession = $session;
    }

    //@codingStandardsIgnoreLine
    public function afterAddProduct(Quote $subject, $result)
    {
        // Since 1.4.2 the shipping duration can be configured on individual products.
        // So if deliveryDate is set and new quote items are added to the cart, it needs should be recalculated.
        if ($this->checkoutSession->getPostNLDeliveryDate()) {
            $this->checkoutSession->setPostNLDeliveryDate(false);
        }

        return $result;
    }
}
