<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
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
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Helper\Tracking;

use \Magento\Sales\Model\Order\Shipment\TrackFactory;
use \Magento\Shipping\Model\Tracking\Result\StatusFactory;
use \Magento\Sales\Model\Order\Shipment;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Sales\Model\Order\ShipmentRepository;
use \TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;

/**
 * Class Track
 *
 * @package TIG\PostNL\Helper\Tracking
 */
class Track
{
    const TRACK_AND_TRACE_SERVICE_URL = 'http://postnl.nl/tracktrace/?';

    /**
     * @var TrackFactory
     */
    private $trackFactory;

    /**
     * @var ShipmentRepository
     */
    private $shimpentRepository;

    /**
     * @var PostNLShipmentRepository
     */
    private $postNLShipmentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var StatusFactory
     */
    private $trackStatusFactory;

    /**
     * @param TrackFactory             $trackFactory
     * @param ShipmentRepository       $shipmentRepository
     * @param PostNLShipmentRepository $postNLShipmentRepository
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder
     * @param StatusFactory            $statusFactory
     */
    public function __construct(
        TrackFactory $trackFactory,
        ShipmentRepository $shipmentRepository,
        PostNLShipmentRepository $postNLShipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        StatusFactory $statusFactory
    ) {
        $this->trackFactory             = $trackFactory;
        $this->shimpentRepository       = $shipmentRepository;
        $this->postNLShipmentRepository = $postNLShipmentRepository;
        $this->searchCriteriaBuilder    = $searchCriteriaBuilder;
        $this->trackStatusFactory       = $statusFactory;
    }

    /**
     * @param Shipment $shipment
     */
    public function set($shipment)
    {
        $trackingNumbers = [];
        foreach ($this->getPostNLshipments($shipment->getId()) as $postnlShipment) {
            $trackingNumbers[] = $postnlShipment->getMainBarcode();
        }

        $this->addTrackingNumbersToShipment($shipment, $trackingNumbers);
    }

    /**
     * @param $tracking
     *
     * @return \Magento\Shipping\Model\Tracking\Result
     */
    public function get($tracking)
    {
        $trackingStatus = $this->trackStatusFactory->create();
        /** @noinspection PhpUndefinedMethodInspection */
        $trackingStatus->setCarrier('tig_postnl');
        /** @noinspection PhpUndefinedMethodInspection */
        $trackingStatus->setCarrierTitle('PostNL');
        /** @noinspection PhpUndefinedMethodInspection */
        $trackingStatus->setTracking($tracking);
        /** @noinspection PhpUndefinedMethodInspection */
        $trackingStatus->setUrl($this->getTrackAndTraceUrl($tracking));

        return $trackingStatus;
    }

    /**
     * @param Shipment $shipment
     * @param $trackingNumbers
     */
    private function addTrackingNumbersToShipment($shipment, $trackingNumbers)
    {
        foreach ($trackingNumbers as $number) {
            $track = $this->trackFactory->create();
            $track->setNumber($number);
            $track->setCarrierCode('tig_postnl');
            $track->setTitle('PostNL');
            $shipment->addTrack($track);
        }

        $this->shimpentRepository->save($shipment);
    }

    /**
     * @param $shipmentId
     *
     * @return \TIG\PostNL\Model\Shipment[]
     */
    private function getPostNLshipments($shipmentId)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('shipment_id', $shipmentId);
        /** @var \Magento\Framework\Api\SearchResults $list */
        $list = $this->postNLShipmentRepository->getList($searchCriteria->create());
        return $list->getItems();
    }

    /**
     * @param $trackingNumber
     *
     * @return \Magento\Framework\Api\AbstractExtensibleObject
     */
    private function getPostNLshipmentByTracking($trackingNumber)
    {
        $searchCriteria = $this->searchCriteriaBuilder->addFilter('main_barcode', $trackingNumber);
        $searchCriteria->setPageSize(1);
        /** @var \Magento\Framework\Api\SearchResults $list */
        $list = $this->postNLShipmentRepository->getList($searchCriteria->create());
        return $list->getItems()[0];
    }

    /**
     * @param $trackingNumber
     * @param string $type
     *
     * @return string
     */
    private function getTrackAndTraceUrl($trackingNumber, $type = 'C')
    {
        /** @var \TIG\PostNL\Model\Shipment $postNLShipment */
        $postNLShipment = $this->getPostNLshipmentByTracking($trackingNumber);
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        $shipment = $this->shimpentRepository->get($postNLShipment->getShipmentId());
        /** @var \Magento\Sales\Api\Data\OrderAddressInterface $address */
        $address  = $shipment->getShippingAddress();

        $lang = !in_array($address->getCountryId(), [
            'NL', 'DE', 'EN', 'FR', 'ED', 'IT', 'CN'
        ]) ? 'EN' : $address->getCountryId();

        $params = [
            'B='.$trackingNumber,
            '&D='.$lang,
            '&P='.$address->getPostcode(),
            '&T='.$type
        ];

        return self::TRACK_AND_TRACE_SERVICE_URL.implode('', $params);
    }
}
