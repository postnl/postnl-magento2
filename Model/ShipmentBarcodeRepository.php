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

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchResultsInterface;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Api\ShipmentBarcodeRepositoryInterface;
use TIG\PostNL\Api\Data\ShipmentBarcodeInterface;
use TIG\PostNL\Model\ResourceModel\ShipmentBarcode\CollectionFactory;
use TIG\PostNL\Service\Filter\SearchResults;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\CouldNotDeleteException;

class ShipmentBarcodeRepository implements ShipmentBarcodeRepositoryInterface
{
    /**
     * @var ShipmentBarcodeFactory
     */
    private $shipmentBarcodeFactory;

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
     * @param ShipmentBarcodeFactory $objectFactory
     * @param CollectionFactory      $collectionFactory
     * @param SearchResults          $searchResults
     * @param SearchCriteriaBuilder  $searchCriteriaBuilder
     */
    public function __construct(
        ShipmentBarcodeFactory $objectFactory,
        CollectionFactory $collectionFactory,
        SearchResults $searchResults,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->shipmentBarcodeFactory = $objectFactory;
        $this->collectionFactory      = $collectionFactory;
        $this->searchResults          = $searchResults;
        $this->searchCriteriaBuilder  = $searchCriteriaBuilder;
    }

    /**
     * @param ShipmentBarcodeInterface $shipmentBarcode
     *
     * @return ShipmentBarcodeInterface
     * @throws CouldNotSaveException
     */
    public function save(ShipmentBarcodeInterface $shipmentBarcode)
    {
        try {
            $shipmentBarcode->save();
        } catch (\Exception $exception) {
            // @codingStandardsIgnoreLine
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $shipmentBarcode;
    }

    /**
     * @param $identifier
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getById($identifier)
    {
        $object = $this->shipmentBarcodeFactory->create();
        $object->load($identifier);

        if (!$object->getId()) {
            // @codingStandardsIgnoreLine
            throw new NoSuchEntityException(__('Object with id "%1" does not exist.', $identifier));
        }

        return $object;
    }

    /**
     * @param ShipmentBarcodeInterface $shipmentBarcode
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ShipmentBarcodeInterface $shipmentBarcode)
    {
        try {
            $shipmentBarcode->delete();
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
     * Create a PostNL Shipment Barcode.
     *
     * @api
     * @return \TIG\PostNL\Api\Data\ShipmentBarcodeInterface
     */
    public function create()
    {
        return $this->shipmentBarcodeFactory->create();
    }

    /**
     * @param ShipmentInterface $shipment
     * @param int               $number
     * @param                   $type
     *
     * @return \Magento\Framework\Api\ExtensibleDataInterface|ShipmentBarcodeInterface|null
     */
    public function getForShipment(ShipmentInterface $shipment, $number, $type)
    {
        $shipmentId = $shipment->getId();
        $this->searchCriteriaBuilder->addFilter('parent_id', $shipmentId);
        $this->searchCriteriaBuilder->addFilter('number', $number);
        $this->searchCriteriaBuilder->addFilter('type', $type);
        $searchCriteria = $this->searchCriteriaBuilder->create();

        $items = $this->getList($searchCriteria);

        if ($items->getTotalCount()) {
            return $items->getItems()[0];
        }

        return null;
    }
}
