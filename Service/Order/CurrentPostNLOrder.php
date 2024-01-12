<?php

namespace TIG\PostNL\Service\Order;

use TIG\PostNL\Model\OrderRepository as PostNLOrderRepository;
use TIG\PostNL\Model\Order as PostNLOrder;
use TIG\PostNL\Service\Wrapper\QuoteInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;

class CurrentPostNLOrder
{
    /**
     * @var PostNLOrderRepository
     */
    private $postNLOrderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var QuoteInterface
     */
    private $quote;

    /**
     * @param PostNLOrderRepository $orderRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param QuoteInterface        $quote
     */
    public function __construct(
        PostNLOrderRepository $orderRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        QuoteInterface $quote
    ) {
        $this->postNLOrderRepository = $orderRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->quote = $quote;
    }

    /**
     * Retrieve the current PostNL order
     *
     * @param int $quoteId
     *
     * @return PostNLOrder|null
     * @throws \Magento\Framework\Exception\InputException
     */
    public function get($quoteId = null)
    {
        if (!$quoteId) {
            $quoteId = $this->getQuoteId();
        }

        return $this->postNLOrderRepository->retrieveCurrentPostNLOrder($quoteId);
    }

    /**
     * @return int
     */
    private function getQuoteId()
    {
        return $this->quote->getQuoteId();
    }
}
