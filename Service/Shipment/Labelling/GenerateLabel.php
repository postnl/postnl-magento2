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
use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Api\ShipmentLabelRepositoryInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Exception as PostNLException;
use TIG\PostNL\Helper\Data;
use TIG\PostNL\Logging\Log;
use TIG\PostNL\Model\ShipmentLabelFactory;
use TIG\PostNL\Webservices\Endpoints\Labelling;

class GenerateLabel
{
    /**
     * @var Labelling
     */
    private $labelling;

    /**
     * @var Log
     */
    private $logger;

    /**
     * @var ShipmentLabelFactory
     */
    private $shipmentLabelFactory;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var ShipmentLabelRepositoryInterface
     */
    private $shipmentLabelRepository;

    /**
     * @var string
     */
    private $date;

    /**
     * @param Data                             $helper
     * @param Labelling                        $labelling
     * @param ShipmentLabelFactory             $shipmentLabelFactory
     * @param ShipmentLabelRepositoryInterface $shipmentLabelRepository
     * @param ShipmentRepositoryInterface      $shipmentRepository
     * @param Log                              $logger
     */
    public function __construct(
        Data $helper,
        Labelling $labelling,
        ShipmentLabelFactory $shipmentLabelFactory,
        ShipmentLabelRepositoryInterface $shipmentLabelRepository,
        ShipmentRepositoryInterface $shipmentRepository,
        Log $logger
    ) {
        $this->logger = $logger;
        $this->date = $helper->getDate();
        $this->labelling = $labelling;
        $this->shipmentRepository = $shipmentRepository;
        $this->shipmentLabelFactory = $shipmentLabelFactory;
        $this->shipmentLabelRepository = $shipmentLabelRepository;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param                   $currentShipmentNumber
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function get(ShipmentInterface $shipment, $currentShipmentNumber)
    {
        try {
            $label = $this->callEndpoint($shipment, $currentShipmentNumber);
        } catch (\Exception $exception) {
            $this->logger->debug($exception->getMessage());
            return null;
        }

        $labelModel = $this->save($shipment, $currentShipmentNumber, $label);
        $this->shipmentLabelRepository->save($labelModel);
        $shipment->setConfirmedAt($this->date);
        $this->shipmentRepository->save($shipment);

        return $labelModel;
    }

    /**
     * @param ShipmentInterface $postnlShipment
     * @param                   $currentShipmentNumber
     *
     * @return \Magento\Framework\Phrase
     * @throws PostNLException
     */
    private function callEndpoint(ShipmentInterface $postnlShipment, $currentShipmentNumber)
    {
        $this->labelling->setParameters($postnlShipment, $currentShipmentNumber);
        $response          = $this->labelling->call();
        $responseShipments = null;

        if (isset($response->ResponseShipments)) {
            $responseShipments = $response->ResponseShipments;
        }

        if (!is_object($response) || !isset($responseShipments->ResponseShipment)) {
            throw new PostNLException(sprintf('Invalid generateLabel response: %1', var_export($response, true)));
        }

        /**
         * @codingStandardsIgnoreLine
         * TODO: GenerateLabel call usually returns one label, but update so multiple labels are taking in account
         */

        return $responseShipments->ResponseShipment[0]->Labels->Label[0]->Content;
    }

    /**
     * @param ShipmentInterface $shipment
     * @param int               $number
     * @param string            $label
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
