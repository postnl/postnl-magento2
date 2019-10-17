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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
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
namespace TIG\PostNL\Service\Shipment\Barcode;

use Magento\Sales\Model\Order\Shipment\TrackFactory;
use TIG\PostNL\Model\ResourceModel\ShipmentBarcode\CollectionFactory;
use TIG\PostNL\Model\ShipmentBarcodeRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\Order\ShipmentRepository;

class AddReturnTracks
{
    /** @var TrackFactory */
    private $trackFactory;

    /** @var CollectionFactory */
    private $collectionFactory;

    /** @var ShipmentBarcodeRepository */
    private $shipmentBarcodeRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /** @var ShipmentRepository */
    private $shipmentRepository;

    /**
     * @param TrackFactory              $trackFactory
     * @param CollectionFactory         $collectionFactory
     * @param ShipmentBarcodeRepository $shipmentBarcodeRepository
     * @param SearchCriteriaBuilder     $searchCriteriaBuilder
     * @param ShipmentRepository        $shipmentRepository
     */
    public function __construct(
        TrackFactory $trackFactory,
        CollectionFactory $collectionFactory,
        ShipmentBarcodeRepository $shipmentBarcodeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ShipmentRepository $shipmentRepository
    ) {
        $this->trackFactory = $trackFactory;
        $this->collectionFactory = $collectionFactory;
        $this->shipmentBarcodeRepository = $shipmentBarcodeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * @param $shipment
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function addReturnTrack($shipment)
    {
        $returnItems = $this->getReturnItems($shipment);
        $shipment = $shipment->getShipment();

        foreach ($returnItems as $item) {
            $track = $this->trackFactory->create();
            $track->setNumber($item->getValue());
            $track->setCarrierCode('tig_postnl');
            $track->setTitle('PostNL Return');
            $shipment->addTrack($track);
        }

        /**
         * @codingStandardsIgnoreLine
         * @todo : Recalculate packages and set correct data.
         */
        $shipment->setPackages([]);
        $this->shipmentRepository->save($shipment->getShipment());
    }

    /**
     * @param $shipment
     *
     * @return \Magento\Framework\Api\ExtensibleDataInterface[]
     */
    public function getReturnItems($shipment)
    {
        $list = $this->getList($shipment);
        $items = $list->getItems();
        $mainBarcode = $this->shipmentBarcodeRepository->create();
        $mainBarcode->setValue($shipment->getReturnBarcode());

        array_unshift($items, $mainBarcode);

        return $items;
    }

    /**
     * @param $shipment
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     */
    public function getList($shipment)
    {
        $this->searchCriteriaBuilder->addFilter('parent_id', $shipment->getId());
        $this->searchCriteriaBuilder->addFilter('type', 'return');
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $list = $this->shipmentBarcodeRepository->getList($searchCriteria);

        return $list;
    }
}
