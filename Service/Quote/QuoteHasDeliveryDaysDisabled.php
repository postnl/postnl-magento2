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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
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

class QuoteHasDeliveryDaysDisabled
{
    /**
     * @var bool $disabledDeliveryDays
     */
    private $disabledDeliveryDays = false;

    /**
     * @param $checkoutSession
     *
     * @return bool
     */
    public function shouldDisableDeliveryDays($checkoutSession)
    {
        $quote = $checkoutSession->getQuote();
        $items = $quote->getItems();

        if($items === null) {
            return;
        }

        foreach ($items as $item) {
            $this->disableDeliveryDays($item->getProduct());
        }

        return $this->disabledDeliveryDays;
    }

    /**
     * @param $product
     */
    private function disableDeliveryDays($product)
    {
        if ($product->getPostnlDisableDeliveryDays()) {
            $this->disabledDeliveryDays = true;
        }
    }
}
