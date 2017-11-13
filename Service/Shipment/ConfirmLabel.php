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
namespace TIG\PostNL\Service\Shipment;

use TIG\PostNL\Webservices\Endpoints\Confirming;
use TIG\PostNL\Helper\Data as Helper;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Api\Data\ShipmentInterface;

class ConfirmLabel
{
    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var Confirming
     */
    private $confirming;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * ConfirmLabel constructor.
     *
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param Confirming                  $confirming
     * @param Helper                        $data
     */
    public function __construct(
        ShipmentRepositoryInterface $shipmentRepository,
        Confirming $confirming,
        Helper $data
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->confirming = $confirming;
        $this->helper = $data;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param                   $number
     */
    public function confirm(ShipmentInterface $shipment, $number = 1)
    {
        $this->confirming->setParameters($shipment, $number);
        $this->confirming->call();
        $shipment->setConfirmedAt($this->helper->getDate());
        $this->shipmentRepository->save($shipment);
    }
}
