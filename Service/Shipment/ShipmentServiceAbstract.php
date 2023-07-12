<?php

namespace TIG\PostNL\Service\Shipment;

use TIG\PostNL\Logging\Log;
use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;
use TIG\PostNL\Model\Shipment as PostNLShipment;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Sales\Model\Order\Shipment;

abstract class ShipmentServiceAbstract
{
    /**
     * @var Log
     */
    //@codingStandardsIgnoreLine
    protected $logger;

    /**
     * @var ShipmentRepository
     */
    //@codingStandardsIgnoreLine
    protected $shipmentRepository;

    /**
     * @var PostNLShipmentRepository
     */
    //@codingStandardsIgnoreLine
    protected $postnlShipmentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    //@codingStandardsIgnoreLine
    protected $searchCriteriaBuilder;

    /**
     * @param Log                      $log
     * @param PostNLShipmentRepository $postNLShipmentRepository
     * @param ShipmentRepository       $shipmentRepository
     */
    public function __construct(
        Log $log,
        PostNLShipmentRepository $postNLShipmentRepository,
        ShipmentRepository $shipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->logger = $log;
        $this->postnlShipmentRepository = $postNLShipmentRepository;
        $this->shipmentRepository = $shipmentRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @param $identifier
     *
     * @return PostNLShipment
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPostNLShipment($identifier)
    {
        return $this->postnlShipmentRepository->getById($identifier);
    }

    /**
     * @param $identifier
     *
     * @return Shipment
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getShipment($identifier)
    {
        return $this->shipmentRepository->get($identifier);
    }
}
