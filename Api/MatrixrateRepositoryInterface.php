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

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use TIG\PostNL\Api\Data\MatrixrateInterface;
use TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate\Collection;

interface MatrixrateRepositoryInterface
{
    /**
     * Save a Matrixrate rule
     * @param MatrixrateInterface $matrixrate
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     * @api
     *
     */
    public function save(MatrixrateInterface $matrixrate);

    /**
     * Retrieve a list of Matrixrates.
     * @api
     * @param SearchCriteriaInterface $criteria
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * Delete a specific Matrixrate.
     * @param MatrixrateInterface $matrixrate
     * @return bool
     * @api
     *
     */
    public function delete(MatrixrateInterface $matrixrate);

    /**
     * Create a Matrixrate rule.
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     * @api
     */
    public function create();

    /**
     * @param string $field
     * @param string $value
     * @return MatrixrateInterface
     */
    public function getByFieldWithValue($field, $value);

    /**
     * @param int $websiteId
     * @return Collection
     */
    public function getByWebsiteId($websiteId);

    /**
     * @param $entityId
     * @return MatrixrateInterface
     */
    public function getById($entityId);
}
