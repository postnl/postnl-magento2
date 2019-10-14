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
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Service\Api\ShipmentManagement;

use Magento\Framework\Webapi\Exception;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Service\Shipment\ConfirmLabel;

class Confirm
{
    /** @var ShipmentRepositoryInterface */
    private $postnlShipmentRepository;

    /** @var ConfirmLabel */
    private $confirmLabel;

    /** @var Track */
    private $track;

    /**
     * @param ShipmentRepositoryInterface $postnlShipmentRepository
     * @param ConfirmLabel                $confirmLabel
     * @param Track                       $track
     */
    public function __construct(
        ShipmentRepositoryInterface $postnlShipmentRepository,
        ConfirmLabel $confirmLabel,
        Track $track
    ) {
        $this->postnlShipmentRepository = $postnlShipmentRepository;
        $this->confirmLabel = $confirmLabel;
        $this->track = $track;
    }

    /**
     * @param int $shipmentId
     *
     * @return void
     * @throws Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function confirm($shipmentId)
    {
        $postnlShipment = $this->postnlShipmentRepository->getByShipmentId($shipmentId);

        $this->confirmLabel->confirm($postnlShipment);

        $magentoShipment = $postnlShipment->getShipment();

        if (!$magentoShipment->getTracks()) {
            $this->track->set($magentoShipment);
        }
    }
}
