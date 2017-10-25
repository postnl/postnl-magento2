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

use TIG\PostNL\Api\ShipmentLabelRepositoryInterface;
use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Model\ResourceModel\ShipmentLabel\CollectionFactory;
use TIG\PostNL\Service\Filter\SearchResults;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class ShipmentLabelRepository implements ShipmentLabelRepositoryInterface
{
    /**
     * @var ShipmentLabelFactory
     */
    private $shipmentLabelFactory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SearchResults
     */
    private $searchResults;

    /**
     * @param ShipmentLabelFactory  $objectFactory
     * @param CollectionFactory     $collectionFactory
     * @param SearchResults         $searchResults
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ShipmentLabelFactory $objectFactory,
        CollectionFactory $collectionFactory,
        SearchResults $searchResults,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->shipmentLabelFactory  = $objectFactory;
        $this->collectionFactory     = $collectionFactory;
        $this->searchResults         = $searchResults;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param ShipmentLabelInterface $shipmentLabel
     *
     * @return ShipmentLabelInterface
     * @throws CouldNotSaveException
     */
    public function save(ShipmentLabelInterface $shipmentLabel)
    {
        try {
            $shipmentLabel->save();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $shipmentLabel;
    }

    /**
     * @param $identifier
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($identifier)
    {
        $object = $this->shipmentLabelFactory->create();
        $object->load($identifier);

        if (!$object->getId()) {
            // @codingStandardsIgnoreLine
            throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $identifier));
        }

        return $object;
    }

    /**
     * @param ShipmentLabelInterface $shipmentLabel
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ShipmentLabelInterface $shipmentLabel)
    {
        try {
            $shipmentLabel->delete();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotDeleteException(__($exception->getMessage()));
        }

        return true;
    }

    /**
     * @param SearchCriteriaInterface $criteria
     *
     * @return SearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $criteria)
    {
        $collection = $this->collectionFactory->create();
        $searchResults = $this->searchResults->getCollectionItems($criteria, $collection);

        return $searchResults;
    }

    /**
     * Create a PostNL Shipment Label.
     *
     * @api
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function create()
    {
        return $this->shipmentLabelFactory->create();
    }

    /**
     * Return a label that belongs to a shipment.
     *
     * @param \TIG\PostNL\Api\Data\ShipmentInterface $shipment
     *
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface[]|null|\Magento\Framework\Api\ExtensibleDataInterface[]
     */
    public function getByShipment(\TIG\PostNL\Api\Data\ShipmentInterface $shipment)
    {
        $this->searchCriteriaBuilder->addFilter('parent_id', $shipment->getId());

        $searchCriteria = $this->searchCriteriaBuilder->create();
        $list = $this->getList($searchCriteria);

        if (!$list->getTotalCount()) {
            return null;
        }

        return $list->getItems();
    }
}
