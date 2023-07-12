<?php

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
