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
     * @return PostNLOrder|null
     */
    public function get()
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('quote_id', $this->getQuoteId());
        $searchCriteria->setPageSize(1);

        /** @var \Magento\Framework\Api\SearchResults $list */
        $list = $this->postNLOrderRepository->getList($searchCriteria->create());

        if ($list->getTotalCount()) {
            return array_values($list->getItems())[0];
        }

        return null;
    }

    /**
     * @return int
     */
    private function getQuoteId()
    {
        return $this->quote->getQuoteId();
    }
}
