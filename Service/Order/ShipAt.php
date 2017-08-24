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

namespace TIG\PostNL\Service\Order;

use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Service\Wrapper\QuoteInterface;
use TIG\PostNL\Webservices\Endpoints\SentDate;

class ShipAt
{
    /**
     * @var QuoteInterface
     */
    private $quote;

    /**
     * @var SentDate
     */
    private $sentDate;

    public function __construct(
        QuoteInterface $quote,
        SentDate $endpoint
    ) {
        $this->quote = $quote;
        $this->sentDate = $endpoint;
    }

    public function set(OrderInterface $order)
    {
        $address = $this->quote->getShippingAddress();

        if (!$address) {
            return null;
        }

        $storeId = $this->quote->getStoreId();
        $this->sentDate->setParameters($address, $storeId, $order);
        $order->setShipAt($this->sentDate->call());

        return $order;
    }
}
