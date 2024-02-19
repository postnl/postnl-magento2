<?php

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
        $quoteId = $quote === null ? $this->checkoutSession->getQuoteId() : $quote->getId();
        $quote   = $quote === null ? $this->quoteRepository->get($quoteId) : $quote;

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
