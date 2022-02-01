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

// @codingStandardsIgnoreFile

namespace TIG\PostNL\Model;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Api\OrderRepositoryInterface;
use TIG\PostNL\Model\ResourceModel\Order\CollectionFactory;
use TIG\PostNL\Service\Wrapper\QuoteInterface;

class OrderRepository extends AbstractRepository implements OrderRepositoryInterface
{
    /**
     * @var OrderFactory
     */
    private $orderFactory;

    /**
     * @var QuoteInterface
     */
    private $quoteWrapper;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * OrderRepository constructor.
     *
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param SearchCriteriaBuilder         $searchCriteriaBuilder
     * @param OrderFactory                  $orderFactory
     * @param CollectionFactory             $collectionFactory
     * @param QuoteInterface                $quote
     * @param FilterBuilder                 $filterBuilder
     */
    public function __construct(
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderFactory $orderFactory,
        CollectionFactory $collectionFactory,
        QuoteInterface $quote,
        FilterBuilder $filterBuilder
    ) {
        $this->quoteWrapper      = $quote;
        $this->orderFactory      = $orderFactory;
        $this->collectionFactory = $collectionFactory;
        $this->filterBuilder     = $filterBuilder;

        parent::__construct($searchResultsFactory, $searchCriteriaBuilder);
    }

    /**
     * @param OrderInterface $order
     *
     * @return OrderInterface
     * @throws CouldNotSaveException
     */
    public function save(OrderInterface $order)
    {
        try {
            $order->save();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $order;
    }

    /**
     * @param $identifier
     *
     * @return OrderInterface
     * @throws NoSuchEntityException
     */
    public function getById($identifier)
    {
        $order = $this->orderFactory->create();
        $order->load($identifier);

        if (!$order->getId()) {
            // @codingStandardsIgnoreLine
            throw new NoSuchEntityException(__('Order with id "%1" does not exist.', $identifier));
        }

        return $order;
    }

    /**
     * @param array $data
     *
     * @return Order
     */
    public function create(array $data = [])
    {
        return $this->orderFactory->create($data);
    }

    /**
     * @param int $identifier
     *
     * @return null|Order
     */
    public function getByOrderId($identifier)
    {
        return $this->getByFieldWithValue('order_id', $identifier);
    }

    /**
     * @param OrderInterface $order
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(OrderInterface $order)
    {
        try {
            $order->delete();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * Delete a PostNL order.
     *
     * @param int $identifier
     * @return bool
     */
    public function deleteById($identifier)
    {
        $order = $this->getById($identifier);

        return $this->delete($order);
    }

    /**
     * @param null $quoteId
     *
     * @return null|AbstractModel
     */
    public function getByQuoteId($quoteId = null)
    {
        if ($quoteId === null) {
            $quoteId = $this->quoteWrapper->getQuoteId();
        }

        return $this->getByFieldWithValue('quote_id', $quoteId);
    }

    /**
     * @param null $quoteId
     *
     * @return \Magento\Framework\Api\ExtensibleDataInterface[]|null
     */
    public function getByQuoteWhereOrderIdIsNull($quoteId = null)
    {
        if ($quoteId === null) {
            $quoteId = $this->quoteWrapper->getQuoteId();
        }

        if ($quoteId === null) {
            return null;
        }

        $filter = $this->filterBuilder->setField('quote_id');
        $filter->setValue($quoteId);
        $this->searchCriteriaBuilder->addFilters([$filter->create()]);

        $list = $this->getList($this->searchCriteriaBuilder->create());
        if (!$list->getTotalCount()) {
            return null;
        }

        $orders = array_filter(array_values($list->getItems()), function ($order) {
            /** @var OrderInterface $order */
            return !$order->getOrderId();
        });

        return array_pop($orders);
    }

    /**
     * Retrieve the most recent order record for a quote
     *
     * @param $quoteId
     *
     * @return mixed|null
     * @throws \Magento\Framework\Exception\InputException
     */
    public function retrieveCurrentPostNLOrder($quoteId)
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('quote_id', $quoteId);
        $collection->setOrder('entity_id', $collection::SORT_ORDER_DESC);

        if ($collection->getSize() > 0) {
            return $collection->getFirstItem();
        }

        return null;
    }
}
