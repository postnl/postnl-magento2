<?php

namespace TIG\PostNL\Model\Carrier;

use Magento\Framework\Api\SearchResultsInterfaceFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use TIG\PostNL\Api\Data\MatrixrateInterface;
use TIG\PostNL\Api\MatrixrateRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use TIG\PostNL\Model\AbstractRepository;
use TIG\PostNL\Model\Carrier\MatrixrateFactory;
use TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate\Collection;
use TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate\CollectionFactory;

class MatrixrateRepository extends AbstractRepository implements MatrixrateRepositoryInterface
{
    /**
     * @var MatrixrateFactory
     */
    private $matrixrateFactory;

    /**
     * MatrixrateRepository constructor.
     *
     * @param SearchResultsInterfaceFactory               $searchResultsFactory
     * @param SearchCriteriaBuilder                       $searchCriteriaBuilder
     * @param \TIG\PostNL\Model\Carrier\MatrixrateFactory $matrixrateFactory
     * @param CollectionFactory                           $collectionFactory
     */
    public function __construct(
        SearchResultsInterfaceFactory $searchResultsFactory,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        MatrixrateFactory $matrixrateFactory,
        CollectionFactory $collectionFactory
    ) {
        parent::__construct($searchResultsFactory, $searchCriteriaBuilder);

        $this->matrixrateFactory = $matrixrateFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Save a Matrixrate rule
     *
     * @param MatrixrateInterface $matrixrate
     *
     * @return MatrixrateInterface
     * @throws CouldNotSaveException
     */
    public function save(MatrixrateInterface $matrixrate)
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
     * @param MatrixrateInterface $matrixrate
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(MatrixrateInterface $matrixrate)
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
     * @return MatrixrateInterface
     */
    public function create(array $data = [])
    {
        return $this->matrixrateFactory->create($data);
    }

    /**
     * @param string $field
     * @param string $value
     *
     * @return MatrixrateInterface
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

    /**
     * @param $websiteId
     *
     * @return Collection
     */
    public function getByWebsiteId($websiteId)
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();

        $collection->addFieldToFilter('website_id', $websiteId);
        $collection->setOrder('subtotal', $collection::SORT_ORDER_DESC);

        return $collection;
    }

    /**
     * @param $identifier
     *
     * @return MatrixrateInterface
     * @throws NoSuchEntityException
     */
    public function getById($identifier)
    {
        $matrixRate = $this->matrixrateFactory->create();
        $matrixRate->load($identifier);

        if (!$matrixRate->getId()) {
            // @codingStandardsIgnoreLine
            throw new NoSuchEntityException(__('Matrixrate with entity id "%1" does not exist.', $identifier));
        }

        return $matrixRate;
    }
}
