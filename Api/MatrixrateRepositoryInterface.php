<?php

namespace TIG\PostNL\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use TIG\PostNL\Api\Data\MatrixrateInterface;

interface MatrixrateRepositoryInterface
{
    /**
     * Save a Matrixrate rule
     * @param \TIG\PostNL\Api\Data\MatrixrateInterface $matrixrate
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     * @api
     *
     */
    public function save(MatrixrateInterface $matrixrate);

    /**
     * Retrieve a list of Matrixrates.
     * @api
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \TIG\PostNL\Api\Data\MatrixrateInterfaceSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $criteria);

    /**
     * Delete a specific Matrixrate.
     * @param \TIG\PostNL\Api\Data\MatrixrateInterface $matrixrate
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
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function getByFieldWithValue($field, $value);

    /**
     * @param int $websiteId
     * @return \TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate\Collection
     */
    public function getByWebsiteId($websiteId);

    /**
     * @param int $entityId
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function getById($entityId);
}
