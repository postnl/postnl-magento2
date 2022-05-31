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
use TIG\PostNL\Model\Shipment;
use TIG\PostNL\Model\ShipmentLabelFactory;

// @codingStandardsIgnoreFile
abstract class GenerateAbstract
{
    /**
     * @var \TIG\PostNL\Webservices\Endpoints\Labelling|\TIG\PostNL\Webservices\Endpoints\LabellingWithoutConfirm
     * $labelling
     */
    private $labelling;

    /** @var \TIG\PostNL\Service\Shipment\Labelling\Handler $handler */
    private $handler;

    /** @var \TIG\PostNL\Logging\Log $logger */
    private $logger;

    /** @var \TIG\PostNL\Model\ShipmentLabelFactory $shipmentLabelFactory */
    private $shipmentLabelFactory;

    /** @var \TIG\PostNL\Api\ShipmentLabelRepositoryInterface $shipmentLabelRepository */
    private $shipmentLabelRepository;

    /** @var \TIG\PostNL\Api\ShipmentRepositoryInterface $shipmentRepository */
    private $shipmentRepository;

    /** @var string $date */
    private $date;

    /**
     * GenerateAbstract constructor.
     *
     * @param \TIG\PostNL\Helper\Data                          $helper
     * @param \TIG\PostNL\Model\ShipmentLabelFactory           $shipmentLabelFactory
     * @param \TIG\PostNL\Api\ShipmentLabelRepositoryInterface $shipmentLabelRepository
     * @param \TIG\PostNL\Api\ShipmentRepositoryInterface      $shipmentRepository
     * @param \TIG\PostNL\Logging\Log                          $logger
     * @param \TIG\PostNL\Service\Shipment\Labelling\Handler   $handler
     */
    public function __construct(
        Data $helper,
        Handler $handler,
        Log $logger,
        ShipmentLabelFactory $shipmentLabelFactory,
        ShipmentLabelRepositoryInterface $shipmentLabelRepository,
        ShipmentRepositoryInterface $shipmentRepository
    ) {
        $this->handler                 = $handler;
        $this->logger                  = $logger;
        $this->shipmentLabelFactory    = $shipmentLabelFactory;
        $this->shipmentLabelRepository = $shipmentLabelRepository;
        $this->shipmentRepository      = $shipmentRepository;
        $this->date                    = $helper->getDate();
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

        if ($responseShipments) {
            $this->saveDownpartnerData($shipment, $responseShipments);
            $this->saveCountryId($shipment);
        }

        if ($confirm) {
            $shipment->setConfirmedAt($this->date);
            $shipment->setConfirmed(true);
        }

        $labelModels = $this->handleLabels($shipment, $responseShipments, $currentShipmentNumber);

        $this->shipmentRepository->save($shipment);

        return $labelModels;
    }

    /**
     * @param \TIG\PostNL\Api\Data\ShipmentInterface $shipment
     * @param                                        $response
     */
    private function saveDownpartnerData(ShipmentInterface $shipment, $response)
    {
        $downPartnerBarcode  = $response[0]->DownPartnerBarcode;
        $downPartnerId       = $response[0]->DownPartnerID;
        $downPartnerLocation = $response[0]->DownPartnerLocation;

        $shipment->setDownpartnerBarcode($downPartnerBarcode);
        $shipment->setDownpartnerId($downPartnerId);
        $shipment->setDownpartnerLocation($downPartnerLocation);
    }

    /**
     * @param ShipmentInterface $shipment
     */
    private function saveCountryId(ShipmentInterface $shipment)
    {
        $shippingAddress = $shipment->getShippingAddress();
        $countryId       = $shippingAddress->getCountryId();

        $shipment->setShipmentCountry($countryId);
    }

    /**
     * @param \TIG\PostNL\Api\Data\ShipmentInterface $postNlShipment
     * @param                                        $currentShipmentNumber
     *
     * @return mixed
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Exception
     */
    private function callEndpoint(ShipmentInterface $postNlShipment, $currentShipmentNumber)
    {
        $this->labelling->setParameters($postNlShipment, $currentShipmentNumber);
        $response          = $this->labelling->call();
        $responseShipments = null;

        if (isset($response->ResponseShipments)) {
            $responseShipments = $response->ResponseShipments;
        }

        if (!is_object($response) || !isset($responseShipments->ResponseShipment)) {
            throw new PostNLException(sprintf('Invalid generateLabel response: %s', var_export($response, true)));
        }

        return $responseShipments->ResponseShipment;
    }

    /**
     * @param $labelService
     */
    public function setLabelService($labelService)
    {
        $this->labelling = $labelService;
    }

    /**
     * @param $shipment
     * @param $responseShipments
     * @param $currentShipmentNumber
     *
     * @return ShipmentLabelInterface[]
     */
    private function handleLabels($shipment, $responseShipments, $currentShipmentNumber)
    {
        $labelModels = [];
        foreach ($responseShipments as $labelItem) {
            $labelModels = array_merge(
                $labelModels,
                $this->getLabelModels($labelItem, $shipment, $currentShipmentNumber)
            );
            $currentShipmentNumber++;
        }

        return $labelModels;
    }

    /**
     * @param $labelItem
     * @param $shipment
     * @param $currentShipmentNumber
     *
     * @return array
     */
    private function getLabelModels($labelItem, ShipmentInterface $shipment, $currentShipmentNumber)
    {
        $labelModels     = [];
        $labelItemHandle = $this->handler->handle($shipment, $labelItem->Labels->Label);

        foreach ($labelItemHandle['labels'] as $Label) {
            $labelModel    = $this->save($shipment, $currentShipmentNumber, $this->getLabelContent($Label), $labelItemHandle['type'], $labelItem->ProductCodeDelivery, $this->getLabelType($Label, $labelItem->ProductCodeDelivery));
            $labelModels[] = $labelModel;
            $this->shipmentLabelRepository->save($labelModel);
        }

        /**
         * If SAM returned different product code during generation, override
         * it in PostNL Shipment table.
         */
        if ($labelItem->ProductCodeDelivery !== $shipment->getProductCode()) {
            $shipment->setProductCode($labelItem->ProductCodeDelivery);
        }

        return $labelModels;
    }

    /**
     * @param $Label
     *
     * @return string
     */
    private function getLabelContent($Label)
    {
        if (is_array($Label)) {
            return $Label['Content'];
        }

        return $Label;
    }

    /**
     * @param $Label
     * @param $productCodeDelivery
     *
     * @return string
     */
    private function getLabelType($Label, $productCodeDelivery)
    {
        if (is_array($Label)) {
            return $Label['Type'];
        }

        return $productCodeDelivery;
    }

    /**
     * @param ShipmentInterface|Shipment $shipment
     * @param int                        $number
     * @param string                     $label
     * @param null|string                $type
     * @param int                        $productCode
     * @param                            $labelType
     *
     * @return ShipmentLabelInterface
     */
    public function save(ShipmentInterface $shipment, $number, $label, $type, $productCode, $labelType)
    {
        /** @var ShipmentLabelInterface $labelModel */
        $labelModel = $this->shipmentLabelFactory->create();
        $labelModel->setParentId($shipment->getId());
        $labelModel->setNumber($number);
        $labelModel->setLabel(base64_encode($label));
        $labelModel->setType($type ?: ShipmentLabelInterface::BARCODE_TYPE_LABEL);
        $labelModel->setProductCode($productCode);

        if ($labelType == 'Return Label') {
            $labelModel->isReturnLabel(true);
        }

        return $labelModel;
    }
}
