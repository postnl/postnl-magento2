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

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order\Shipment;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Service\Handler\BarcodeHandler;
use TIG\PostNL\Service\Shipment\Labelling\GetLabels;

class GenerateLabel
{
    /** @var ShipmentRepositoryInterface */
    private $shipmentRepository;

    /** @var BarcodeHandler */
    private $barcodeHandler;

    /** @var GetLabels */
    private $getLabels;

    public function __construct(
        ShipmentRepositoryInterface $shipmentRepository,
        BarcodeHandler $barcodeHandler,
        GetLabels $getLabels
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->barcodeHandler = $barcodeHandler;
        $this->getLabels = $getLabels;
    }

    /**
     * @param $shipmentId
     *
     * @return bool
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function generate($shipmentId, $smartReturns = false)
    {
        $postnlShipment = $this->shipmentRepository->getByShipmentId($shipmentId);

        /** @var Shipment|ShipmentInterface $shipment */
        $shipment = $postnlShipment->getShipment();
        $shippingAddress = $shipment->getShippingAddress();

        $this->barcodeHandler->prepareShipment($shipment->getId(), $shippingAddress->getCountryId(), $smartReturns);
        $labels = $this->getLabels->get($shipment->getId(), false, true);

        if (empty($labels)) {
            return false;
        }

        return true;
    }
}
