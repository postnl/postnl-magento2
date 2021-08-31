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

namespace TIG\PostNL\Service\Carrier;

use Magento\Checkout\Model\Session;
use Magento\Quote\Api\CartRepositoryInterface;
use TIG\PostNL\Api\OrderRepositoryInterface;
use TIG\PostNL\Service\Options\ItemsToOption;

class ParcelTypeFinder
{
    const DEFAULT_TYPE = 'regular';

    /**
     * @var ItemsToOption
     */
    private $itemsToOption;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var Session
     */
    private $checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    private $quoteRepository;

    /**
     * ParcelTypeFinder constructor.
     *
     * @param ItemsToOption            $itemsToOption
     * @param OrderRepositoryInterface $orderRepository
     * @param Session                  $checkoutSession
     * @param CartRepositoryInterface  $quoteRepository
     */
    public function __construct(
        ItemsToOption $itemsToOption,
        OrderRepositoryInterface $orderRepository,
        Session $checkoutSession,
        CartRepositoryInterface $quoteRepository
    ) {
        $this->itemsToOption = $itemsToOption;
        $this->orderRepository = $orderRepository;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($quote = null)
    {
        if ($quote === null) {
            $quoteId = $this->checkoutSession->getQuoteId();
            $quote   = $this->quoteRepository->get($quoteId);
        }

        $quoteId = $quote->getId();

        $result = $this->itemsToOption->getFromQuote($quote);
        if ($result) {
            return $result;
        }

        $order = $this->orderRepository->getByQuoteId($quoteId);

        if ($order === null) {
            return static::DEFAULT_TYPE;
        }

        return $this->parseDatabaseValue($order->getType());
    }

    /**
     * @param $type
     *
     * @return string
     */
    private function parseDatabaseValue($type)
    {
        switch ($type) {
            case 'PG':
                return 'pakjegemak';
        }

        return static::DEFAULT_TYPE;
    }
}
