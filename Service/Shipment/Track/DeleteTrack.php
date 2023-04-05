<?php

namespace TIG\PostNL\Service\Shipment\Track;

use TIG\PostNL\Service\Shipment\ShipmentServiceAbstract;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Exception as PostNLException;
use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;
use Magento\Sales\Model\Order\Shipment\Track;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Sales\Model\Order\Shipment;

class DeleteTrack extends ShipmentServiceAbstract
{
    /**
     * @var Track
     */
    private $track;

    /**
     * @param Track                    $track
     * @param Log                      $log
     * @param PostNLShipmentRepository $postNLShipmentRepository
     * @param ShipmentRepository       $shipmentRepository
     * @param SearchCriteriaBuilder    $searchCriteriaBuilder
     */
    public function __construct(
        Track $track,
        Log $log,
        PostNLShipmentRepository $postNLShipmentRepository,
        ShipmentRepository $shipmentRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        parent::__construct(
            $log,
            $postNLShipmentRepository,
            $shipmentRepository,
            $searchCriteriaBuilder
        );

        $this->track  = $track;
    }

    /**
     * Deletes a single track.
     *
     * @param int $trackId
     */
    public function delete($trackId)
    {
        /** @var Track $track */
        $track = $this->track->load($trackId);
        if (!$track->getId()) {
            $this->logger->alert('Can\'t initialize track for deletion', [$trackId]);
        }

        try {
            $track->delete();
        } catch (PostNLException $exception) {
            $this->logger->alert('Can\'t delete tracking number', $exception->getLogMessage());
        }
    }

    /**
     * Deletes all track (T&T) information associated to the Shipment ID.
     *
     * @param $shipmentId
     */
    public function deleteAllByShipmentId($shipmentId)
    {
        /** @var Shipment $shipment */
        $shipment = $this->getShipment($shipmentId);
        $tracks   = $shipment->getAllTracks();

        /** @var Track $track */
        foreach ($tracks as $track) {
            // @codingStandardsIgnoreLine
            $this->delete($track->getId());
        }
    }
}
