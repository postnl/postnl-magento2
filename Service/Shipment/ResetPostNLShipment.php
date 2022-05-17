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
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Service\Shipment;

use TIG\PostNL\Service\Shipment\Track\DeleteTrack;
use TIG\PostNL\Service\Shipment\Label\DeleteLabel;
use TIG\PostNL\Service\Shipment\Barcode\DeleteBarcode;
use TIG\PostNL\Model\Shipment as PostNLShipment;
use TIG\PostNL\Api\ShipmentRepositoryInterface;

class ResetPostNLShipment
{
    /**
     * @var DeleteLabel
     */
    private $labelDeleteHandler;

    /**
     * @var DeleteBarcode
     */
    private $barcodeDeleteHandler;

    /**
     * @var DeleteTrack
     */
    private $trackDeleteHandler;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var ShipmentService
     */
    private $shipmentService;

    /**
     * @param DeleteLabel                 $labelDeleteHandler
     * @param DeleteBarcode               $barcodeDeleteHandler
     * @param DeleteTrack                 $trackDeleteHandler
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param ShipmentService             $shipmentService
     */
    public function __construct(
        DeleteLabel $labelDeleteHandler,
        DeleteBarcode $barcodeDeleteHandler,
        DeleteTrack $trackDeleteHandler,
        ShipmentRepositoryInterface $shipmentRepository,
        ShipmentService $shipmentService
    ) {
        $this->labelDeleteHandler = $labelDeleteHandler;
        $this->barcodeDeleteHandler = $barcodeDeleteHandler;
        $this->trackDeleteHandler = $trackDeleteHandler;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentService = $shipmentService;
    }

    /**
     * Resets the confirmation date to null.
     *
     * @param $shipmentId
     *
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     *
     * @return PostNLShipment
     */
    public function resetShipment($shipmentId)
    {
        $postNLShipment = $this->shipmentRepository->getByShipmentId($shipmentId);

        /** @var PostNLShipment $postNLShipment */
        $postNLShipment->setConfirmedAt(null);
        $postNLShipment->setConfirmed(false);
        $postNLShipment->setMainBarcode(null);
        $this->shipmentService->save($postNLShipment);

        $this->barcodeDeleteHandler->deleteAllByShipmentId($postNLShipment->getId());
        $this->labelDeleteHandler->deleteAllByParentId($postNLShipment->getId());
        $this->trackDeleteHandler->deleteAllByShipmentId($shipmentId);

        return $postNLShipment;
    }
}
