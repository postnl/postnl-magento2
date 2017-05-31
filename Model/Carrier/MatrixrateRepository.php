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

namespace TIG\PostNL\Model\Carrier;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use TIG\PostNL\Api\Data\MatrixRateInterface;
use TIG\PostNL\Api\MatrixrateRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use TIG\PostNL\Model\AbstractRepository;
use TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate\CollectionFactory;

class MatrixrateRepository extends AbstractRepository implements MatrixrateRepositoryInterface
{
    /**
     * @var MatrixRateFactory
     */
    private $matrixRateFactory;

    /**
     * @param MatrixRateFactory     $matrixRateFactory
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CollectionFactory     $collectionFactory
     */
    public function __construct(
        MatrixRateFactory $matrixRateFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CollectionFactory $collectionFactory
    ) {
        $this->matrixRateFactory = $matrixRateFactory;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Save a Matrixrate rule
     *
     * @param MatrixRateInterface $matrixrate
     *
     * @return MatrixRateInterface
     * @throws CouldNotSaveException
     */
    public function save(MatrixRateInterface $matrixrate)
    {
        try {
            $matrixrate->save();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $matrixrate;
    }

    /**
     * Delete a specific Matrixrate.
     *
     * @param MatrixRateInterface $matrixrate
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(MatrixRateInterface $matrixrate)
    {
        try {
            $matrixrate->delete();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * Create a Matrixrate rule.
     *
     * @param array $data
     *
     * @return MatrixRateInterface
     */
    public function create(array $data = [])
    {
        return $this->matrixRateFactory->create($data);
    }

    /**
     * @param string $field
     * @param string $value
     *
     * @return MatrixRateInterface
     */
    public function getByFieldWithValue($field, $value)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter($field, $value);
        $searchCriteria->setPageSize(1);

        /** @var \Magento\Framework\Api\SearchResults $list */
        $list = $this->getList($searchCriteria->create());

        if ($list->getTotalCount()) {
            return $list->getItems()[0];
        }

        return null;
    }
}
