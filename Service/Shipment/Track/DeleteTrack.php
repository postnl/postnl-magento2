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
namespace TIG\PostNL\Service\Shipment\Track;

use \Magento\Sales\Model\Order\Shipment\Track;
use TIG\PostNL\Service\Shipment\ShipmentServiceAbstract;
use Magento\Framework\Api\SearchCriteriaBuilder;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Exception as PostNLException;
use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Model\ShipmentRepository as PostNLShipmentRepository;

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
