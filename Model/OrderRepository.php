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
namespace TIG\PostNL\Model;

use TIG\PostNL\Api\OrderRepositoryInterface;
use TIG\PostNL\Api\Data\OrderInterface;
use TIG\PostNL\Model\ResourceModel\Order\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterfaceFactory;
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
     * OrderRepository constructor.
     *
     * @param SearchResultsInterfaceFactory $searchResultsFactory
     * @param SearchCriteriaBuilder         $searchCriteriaBuilder
     * @param OrderFactory                  $orderFactory
     * @param CollectionFactory             $collectionFactory
     */
    public function __construct(
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OrderFactory $orderFactory,
        CollectionFactory $collectionFactory,
        QuoteInterface $quote
    ) {
        $this->quoteWrapper      = $quote;
        $this->orderFactory      = $orderFactory;
        $this->collectionFactory = $collectionFactory;

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
}
