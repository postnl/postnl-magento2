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
namespace TIG\PostNL\Service\Shipment\Labelling;

use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Api\ShipmentLabelRepositoryInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Exception as PostNLException;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Model\ShipmentLabelFactory;
use TIG\PostNL\Webservices\Endpoints\Labelling;
use TIG\PostNL\Webservices\Endpoints\LabellingWithoutConfirm;

abstract class GenerateAbstract
{
    /**
     * @var Labelling|LabellingWithoutConfirm
     */
    // @codingStandardsIgnoreLine
    protected $labelService;

    /**
     * @var Log
     */
    //@codingStandardsIgnoreLine
    protected $logger;

    /**
     * @var ShipmentLabelFactory
     */
    //@codingStandardsIgnoreLine
    protected $shipmentLabelFactory;

    /**
     * @var ShipmentRepositoryInterface
     */
    //@codingStandardsIgnoreLine
    protected $shipmentRepository;

    /**
     * @var ShipmentLabelRepositoryInterface
     */
    //@codingStandardsIgnoreLine
    protected $shipmentLabelRepository;

    /**
     * @var string
     */
    //@codingStandardsIgnoreLine
    protected $date;

    /**
     * @param Data                              $helper
     * @param ShipmentLabelFactory              $shipmentLabelFactory
     * @param ShipmentLabelRepositoryInterface  $shipmentLabelRepository
     * @param ShipmentRepositoryInterface       $shipmentRepository
     * @param Log                               $logger
     */
    public function __construct(
        Data $helper,
        ShipmentLabelFactory $shipmentLabelFactory,
        ShipmentLabelRepositoryInterface $shipmentLabelRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        Log $logger
    ) {
        $this->logger = $logger;
        $this->date = $helper->getDate();
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentLabelFactory = $shipmentLabelFactory;
        $this->shipmentLabelRepository = $shipmentLabelRepository;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param                   $currentShipmentNumber
     * @param                   $confirm
     *
     * @return null|ShipmentLabelInterface[]
     */
    public function getLabel(ShipmentInterface $shipment, $currentShipmentNumber, $confirm)
    {
        try {
            $responseShipments = $this->callEndpoint($shipment, $currentShipmentNumber);
        } catch (\Exception $exception) {
            $this->logger->debug($exception->getMessage());
            return null;
        }

        $labelModels = $this->handleLabels($shipment, $responseShipments, $currentShipmentNumber);

        if ($confirm) {
            $shipment->setConfirmedAt($this->date);
            $this->shipmentRepository->save($shipment);
        }

        return $labelModels;
    }

    /**
     * @param ShipmentInterface $postnlShipment
     * @param                   $currentShipmentNumber
     *
     * @return mixed
     * @throws PostNLException
     */
    //@codingStandardsIgnoreLine
    protected function callEndpoint(ShipmentInterface $postnlShipment, $currentShipmentNumber)
    {
        $this->labelService->setParameters($postnlShipment, $currentShipmentNumber);
        $response          = $this->labelService->call();
        $responseShipments = null;

        if (isset($response->ResponseShipments)) {
            $responseShipments = $response->ResponseShipments;
        }

        if (!is_object($response) || !isset($responseShipments->ResponseShipment)) {
            throw new PostNLException(sprintf('Invalid generateLabel response: %1', var_export($response, true)));
        }

        return $responseShipments->ResponseShipment;
    }

    /**
     * @param $shipment
     * @param $responsShipments
     * @param $currentShipmentNumber
     *
     * @return ShipmentLabelInterface[]
     */
    //@codingStandardsIgnoreStart
    protected function handleLabels($shipment, $responsShipments, $currentShipmentNumber)
    {
        $labelModels = [];
        foreach ($responsShipments as $labelItem) {
            $labelModel    = $this->save($shipment, $currentShipmentNumber, $labelItem->Labels->Label[0]->Content);
            $labelModels[] = $labelModel;
            $this->shipmentLabelRepository->save($labelModel);
            $currentShipmentNumber++;
        }

        return $labelModels;
    }
    //@codingStandardsIgnoreEnd

    /**
     * @param ShipmentInterface|Shipment $shipment
     * @param int                        $number
     * @param string                     $label
     *
     * @return ShipmentLabelInterface
     */
    public function save(ShipmentInterface $shipment, $number, $label)
    {
        /** @var ShipmentLabelInterface $labelModel */
        $labelModel = $this->shipmentLabelFactory->create();
        $labelModel->setParentId($shipment->getId());
        $labelModel->setNumber($number);
        $labelModel->setLabel(base64_encode($label));
        $labelModel->setType(ShipmentLabelInterface::BARCODE_TYPE_LABEL);

        return $labelModel;
    }
}
