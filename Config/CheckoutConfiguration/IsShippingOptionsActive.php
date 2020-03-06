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

namespace TIG\PostNL\Config\CheckoutConfiguration;

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

    /**
     * IsShippingOptionsActive constructor.
     *
     * @param ShippingOptions               $shippingOptions
     * @param ProductOptions                $productOptions
     * @param AccountConfiguration          $accountConfiguration
     * @param CheckIfQuoteItemsAreInStock   $quoteItemsAreInStock
     * @param CheckIfQuoteHasOption         $quoteHasOption
     * @param CheckIfQuoteItemsCanBackorder $quoteItemsCanBackorder
     */
    public function __construct(
        ShippingOptions $shippingOptions,
        ProductOptions $productOptions,
        AccountConfiguration $accountConfiguration,
        CheckIfQuoteItemsAreInStock $quoteItemsAreInStock,
        CheckIfQuoteHasOption $quoteHasOption,
        CheckIfQuoteItemsCanBackorder $quoteItemsCanBackorder
    ) {
        $this->shippingOptions        = $shippingOptions;
        $this->productOptions         = $productOptions;
        $this->quoteItemsAreInStock   = $quoteItemsAreInStock;
        $this->accountConfiguration   = $accountConfiguration;
        $this->quoteHasOption         = $quoteHasOption;
        $this->quoteItemsCanBackorder = $quoteItemsCanBackorder;
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

        if ($manageStock === false || $this->quoteItemsAreInStock->getValue()) {
            return true;
        }

        if ($this->shippingOptions->getShippingStockoptions() == 'in_stock' &&
            !$this->quoteItemsAreInStock->getValue()
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
