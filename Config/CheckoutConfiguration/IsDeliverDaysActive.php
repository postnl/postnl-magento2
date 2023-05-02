<?php

namespace TIG\PostNL\Config\CheckoutConfiguration;

use TIG\PostNL\Config\Provider\ShippingOptions;
use Magento\Checkout\Model\Session;

class IsDeliverDaysActive implements CheckoutConfigurationInterface
{
    /** @var ShippingOptions */
    private $shippingOptions;

    /** @var Session */
    private $checkoutSession;

    /**
     * @param ShippingOptions $shippingOptions
     */
    public function __construct(
        ShippingOptions $shippingOptions,
        Session $checkoutSession
    ) {
        $this->shippingOptions = $shippingOptions;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return bool
     */
    // @codingStandardsIgnoreStart
    public function getValue()
    {
        $quote = $this->checkoutSession->getQuote();

        if (!$quote) {
            return (bool) $this->shippingOptions->isDeliverydaysActive();
        }

        $items = $quote->getItems();

        if ($items === null) {
            return false;
        }

        foreach ($items as $item) {
            $product = $item->getProduct();

            if ($product->getPostnlDisableDeliveryDays()) {
                return false;
            }
        }

        return (bool) $this->shippingOptions->isDeliverydaysActive();
    }
    // @codingStandardsIgnoreEnd
}
