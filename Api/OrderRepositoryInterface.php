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
     * @param int $identifier
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function getById($identifier);

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
     * @param int $identifier
     * @return bool
     */
    public function deleteById($identifier);

    /**
     * Create a PostNL order.
     *
     * @api
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function create();

    /**
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
