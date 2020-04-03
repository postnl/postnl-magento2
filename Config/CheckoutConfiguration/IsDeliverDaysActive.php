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

            // @codingStandardsIgnoreLine
            if ($product->getPostnlDisableDeliveryDays()) {
                return false;
            }
        }

        return (bool) $this->shippingOptions->isDeliverydaysActive();
    }
}
