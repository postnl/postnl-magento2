<?php

namespace TIG\PostNL\Config\CheckoutConfiguration;

use Magento\Checkout\Model\Session;
use Magento\Checkout\Model\Session\Proxy as CheckoutSession;
use Magento\Framework\App\Config\ScopeConfigInterface;
use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Config\Provider\ProductOptions;
use TIG\PostNL\Service\Quote\CheckIfQuoteItemsAreInStock;
use TIG\PostNL\Service\Quote\CheckIfQuoteItemsCanBackorder;
use \TIG\PostNL\Service\Quote\CheckIfQuoteHasOption;
use TIG\PostNL\Service\Order\ProductInfo;

// @codingStandardsIgnoreFile
// TODO: See if class can be simplified (less properties)
class IsShippingOptionsActive implements CheckoutConfigurationInterface
{
    const POSTNL_LETTERBOX_PARCEL_CODE = '2928';

    /** @var ShippingOptions */
    private $shippingOptions;

    /** @var ProductOptions */
    private $productOptions;

    /** @var CheckIfQuoteItemsAreInStock */
    private $quoteItemsAreInStock;

    /** @var AccountConfiguration */
    private $accountConfiguration;

    /** @var CheckIfQuoteHasOption */
    private $quoteHasOption;

    /** @var CheckIfQuoteItemsCanBackorder */
    private $quoteItemsCanBackorder;

    /** @var Session */
    private $checkoutSession;

    /**
     * IsShippingOptionsActive constructor.
     *
     * @param ShippingOptions               $shippingOptions
     * @param ProductOptions                $productOptions
     * @param AccountConfiguration          $accountConfiguration
     * @param CheckIfQuoteItemsAreInStock   $quoteItemsAreInStock
     * @param CheckIfQuoteHasOption         $quoteHasOption
     * @param CheckIfQuoteItemsCanBackorder $quoteItemsCanBackorder
     * @param CheckoutSession               $checkoutSession
     */
    public function __construct(
        ShippingOptions $shippingOptions,
        ProductOptions $productOptions,
        AccountConfiguration $accountConfiguration,
        CheckIfQuoteItemsAreInStock $quoteItemsAreInStock,
        CheckIfQuoteHasOption $quoteHasOption,
        CheckIfQuoteItemsCanBackorder $quoteItemsCanBackorder,
        CheckoutSession $checkoutSession
    ) {
        $this->shippingOptions        = $shippingOptions;
        $this->productOptions         = $productOptions;
        $this->quoteItemsAreInStock   = $quoteItemsAreInStock;
        $this->accountConfiguration   = $accountConfiguration;
        $this->quoteHasOption         = $quoteHasOption;
        $this->quoteItemsCanBackorder = $quoteItemsCanBackorder;
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @return bool|string
     */
    public function getValue()
    {
        if (!$this->shippingOptions->isShippingoptionsActive()
            || $this->accountConfiguration->isModusOff()
            || !$this->hasValidApiSettings()
            || $this->quoteHasOption->get(ProductInfo::OPTION_EXTRAATHOME)
            || $this->productOptions->getDefaultProductOption() == static::POSTNL_LETTERBOX_PARCEL_CODE
        ) {
            return false;
        }

        return $this->validateStockOptions();
    }

    /**
     * @return bool
     */
    private function validateStockOptions()
    {
        $manageStock = $this->shippingOptions->getManageStock();
        $quote = $this->checkoutSession->getQuote();

        if ($manageStock === false || $this->quoteItemsAreInStock->getValue($quote)) {
            return true;
        }

        if ($this->shippingOptions->getShippingStockoptions() == 'in_stock' &&
            !$this->quoteItemsAreInStock->getValue($quote)
        ) {
            return false;
        }

        if ($this->shippingOptions->getShippingStockoptions() !== 'in_stock' &&
            !$this->quoteItemsCanBackorder->getValue()
        ) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function hasValidApiSettings()
    {
        return $this->accountConfiguration->getCustomerCode()
            && $this->accountConfiguration->getCustomerNumber()
            && $this->accountConfiguration->getApiKey();
    }
}
