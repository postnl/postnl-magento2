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

use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Service\Quote\CheckIfQuoteItemsAreInStock;
use \TIG\PostNL\Service\Quote\CheckIfQuoteHasOption;
use TIG\PostNL\Service\Order\ProductCodeAndType;

class IsShippingOptionsActive implements CheckoutConfigurationInterface
{
    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var CheckIfQuoteItemsAreInStock
     */
    private $quoteItemsAreInStock;

    /**
     * @var AccountConfiguration
     */
    private $accountConfiguration;

    /**
     * @var CheckIfQuoteHasOption
     */
    private $quoteHasOption;

    /**
     * @param ShippingOptions             $shippingOptions
     * @param AccountConfiguration        $accountConfiguration
     * @param CheckIfQuoteItemsAreInStock $quoteItemsAreInStock
     * @param CheckIfQuoteHasOption   $quoteHasOption
     */
    public function __construct(
        ShippingOptions $shippingOptions,
        AccountConfiguration $accountConfiguration,
        CheckIfQuoteItemsAreInStock $quoteItemsAreInStock,
        CheckIfQuoteHasOption $quoteHasOption
    ) {
        $this->shippingOptions = $shippingOptions;
        $this->quoteItemsAreInStock = $quoteItemsAreInStock;
        $this->accountConfiguration = $accountConfiguration;
        $this->quoteHasOption = $quoteHasOption;
    }

    /**
     * @return bool
     */
    public function getValue()
    {
        if (!$this->shippingOptions->isShippingoptionsActive()) {
            return false;
        }

        if (!$this->hasValidApiSettings()) {
            return false;
        }

        if ($this->quoteHasOption->get(ProductCodeAndType::OPTION_EXTRAATHOME)) {
            return false;
        }

        if ($this->shippingOptions->getShippingStockoptions() == 'backordered' &&
            !$this->quoteItemsAreInStock->getValue()
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
        if (!$this->accountConfiguration->getCustomerCode()) {
            return false;
        }

        if (!$this->accountConfiguration->getCustomerNumber()) {
            return false;
        }

        if (!$this->accountConfiguration->getApiKey()) {
            return false;
        }

        return true;
    }
}
