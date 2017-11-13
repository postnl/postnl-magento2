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

interface MatrixrateRepositoryInterface
{
    /**
     * Save a Matrixrate rule
     *
     * @api
     *
     * @param \TIG\PostNL\Api\Data\MatrixrateInterface $matrixrate
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function save(\TIG\PostNL\Api\Data\MatrixrateInterface $matrixrate);

    /**
     * Retrieve a list of Matrixrates.
     *
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria);

    /**
     * Delete a specific Matrixrate.
     *
     * @api
     *
     * @param \TIG\PostNL\Api\Data\MatrixrateInterface $matrixrate
     *
     * @return bool
     */
    public function delete(\TIG\PostNL\Api\Data\MatrixrateInterface $matrixrate);

    /**
     * Create a Matrixrate rule.
     *
     * @api
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function create();

    /**
     * @param string $field
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function getByFieldWithValue($field, $value);

    /**
     * @param int $websiteId
     *
     * @return \TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate\Collection
     */
    public function getByWebsiteId($websiteId);
}
