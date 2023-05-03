<?php

namespace TIG\PostNL\Config\CheckoutConfiguration;

use Magento\Checkout\Model\Session;

/** This class adds an array to the window checkout config to check if the
 * current products have the postnl delivery days disabled
 */
class IsDeliveryDaysDisabled implements CheckoutConfigurationInterface
{
    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * IsDeliveryDaysDisabled constructor.
     *
     * @param Session $checkoutSession
     */
    public function __construct(
        Session $checkoutSession
    ) {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return array|void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getValue()
    {
        $data = [];
        $quote = $this->checkoutSession->getQuote();
        $items = $quote->getItems();

        if ($items === null) {
            return;
        }

        foreach ($items as $item) {
            $product = $item->getProduct();
            $data[$item->getName()] = $product->getPostnlDisableDeliveryDays();
        }

        return $data;
    }
}
