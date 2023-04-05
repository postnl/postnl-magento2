<?php

namespace TIG\PostNL\Api;

interface OrderRepositoryInterface
{
    /**
     * Save a PostNL order
     *
     * @api
     * @param \TIG\PostNL\Api\Data\OrderInterface $order
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function save(\TIG\PostNL\Api\Data\OrderInterface $order);

    /**
     * Return a specific PostNL order.
     *
     * @api
     * @param int $id
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    // @codingStandardsIgnoreLine
    public function getById($id);

    /**
     * Retrieve a list of PostNL orders.
     *
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria);

    /**
     * Delete a specific PostNL order.
     *
     * @api
     * @param \TIG\PostNL\Api\Data\OrderInterface $order
     * @return bool
     */
    public function delete(\TIG\PostNL\Api\Data\OrderInterface $order);

    /**
     * Delete a PostNL order.
     *
     * @api
     * @param int $id
     * @return bool
     */
    // @codingStandardsIgnoreLine
    public function deleteById($id);

    /**
     * Create a PostNL order.
     *
     * @api
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function create();

    /**
     * Get by field with value
     *
     * @api
     * @param string $field
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function getByFieldWithValue($field, $value);

    /**
     * @param int $id
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    // @codingStandardsIgnoreLine
    public function getByOrderId($id);

    /**
     * @param $quoteId
     *
     * @return null|\TIG\PostNL\Api\Data\OrderInterface
     */
    public function getByQuoteId($quoteId = null);
}
