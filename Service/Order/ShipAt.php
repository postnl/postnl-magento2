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

use Magento\Framework\Stdlib\DateTime\DateTime;
use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Service\Timeframe\Options;
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

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @param QuoteInterface $quote
     * @param SentDate       $endpoint
     * @param DateTime       $dateTime
     */
    public function __construct(
        QuoteInterface $quote,
        SentDate $endpoint,
        DateTime $dateTime
    ) {
        $this->quote    = $quote;
        $this->sentDate = $endpoint;
        $this->dateTime = $dateTime;
    }

    /**
     * GetSentDate calls could break during holidays, but this variable is only used to inform merchants.
     * It shouldn't break the shipping flow. This is the reason why $sentDate is set to null on failure. #POSTNLM2-1012
     *
     * @param OrderInterface $order
     *
     * @return OrderInterface|null
     */
    public function set(OrderInterface $order)
    {
        $address = $this->quote->getShippingAddress();

        if (!$address || !$order) {
            return null;
        }

        $storeId = $this->quote->getStoreId();
        $this->sentDate->updateParameters($address, $storeId, $order);

        try {
            $sentDate = $this->sentDate->call();
        } catch (\Exception $exception) {
            $sentDate = null;
        }

        if ($order->getType() == Options::TODAY_DELIVERY_OPTION && $sentDate !== null) {
            $sentDate = $this->dateTime->date('d-m-Y', $sentDate . ' +1 day');
        }

        $order->setShipAt($sentDate);

        return $order;
    }
}
