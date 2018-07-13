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
