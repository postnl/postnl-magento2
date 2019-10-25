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
use Magento\Sales\Model\Order\ShipmentRepository;
use TIG\PostNL\Model\ShipmentBarcodeRepository;
use Magento\Framework\Api\SearchCriteriaBuilder;

class AddReturnTracks
{
    /** @var TrackFactory */
    private $trackFactory;

    /** @var ShipmentRepository */
    private $shipmentRepository;

    /** @var ShipmentBarcodeRepository */
    private $shipmentBarcodeRepository;

    /** @var SearchCriteriaBuilder */
    private $searchCriteriaBuilder;

    /**
     * @param TrackFactory                       $trackFactory
     * @param ShipmentRepository                 $shipmentRepository
     * @param ShipmentBarcodeRepository          $shipmentBarcodeRepository
     * @param SearchCriteriaBuilder              $searchCriteriaBuilder
     */
    public function __construct(
        TrackFactory $trackFactory,
        ShipmentRepository $shipmentRepository,
        ShipmentBarcodeRepository $shipmentBarcodeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->trackFactory = $trackFactory;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentBarcodeRepository = $shipmentBarcodeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param $postNLShipment
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function addReturnTrack($postNLShipment)
    {
        $returnItems = $this->getList($postNLShipment);
        $shipment = $postNLShipment->getShipment();

        foreach ($returnItems as $item) {
            $track = $this->trackFactory->create();
            $track->setNumber($item->getValue());
            $track->setCarrierCode('tig_postnl');
            $track->setTitle('PostNL Return');
            /** @noinspection PhpUndefinedMethodInspection */
            $shipment->addTrack($track);
        }

        $this->shipmentRepository->save($shipment);
    }

    /**
     * @param $shipment
     *
     * @return \Magento\Framework\Api\ExtensibleDataInterface[]
     */
    public function getList($shipment)
    {
        $this->searchCriteriaBuilder->addFilter('parent_id', $shipment->getId());
        $this->searchCriteriaBuilder->addFilter('type', 'return');
        $searchCriteria = $this->searchCriteriaBuilder->create();

        $result = $this->shipmentBarcodeRepository->getList($searchCriteria);
        $list = $result->getItems();

        return $list;
    }
}
