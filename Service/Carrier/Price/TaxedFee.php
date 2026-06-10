<?php

namespace TIG\PostNL\Service\Carrier\Price;

use Exception;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Tax\Helper\Data as TaxHelper;
use Magento\Tax\Model\Config as TaxConfig;

class TaxedFee
{
    private TaxHelper $taxHelper;

    private CheckoutSession $checkoutSession;

    public function __construct(TaxHelper $taxHelper, CheckoutSession $checkoutSession)
    {
        $this->taxHelper = $taxHelper;
        $this->checkoutSession = $checkoutSession;
    }

    public function get(float $fee): float
    {
        $displayType = $this->taxHelper->getShippingPriceDisplayType();
        $includeVat = $displayType === TaxConfig::DISPLAY_TYPE_INCLUDING_TAX || $displayType === TaxConfig::DISPLAY_TYPE_BOTH;

        try {
            $shippingAddress = $this->checkoutSession->getQuote()->getShippingAddress();
        } catch (Exception $e) {
            $shippingAddress = null;
        }

        return (float) $this->taxHelper->getShippingPrice($fee, $includeVat, $shippingAddress);
    }
}
