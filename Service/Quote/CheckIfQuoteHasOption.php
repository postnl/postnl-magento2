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
