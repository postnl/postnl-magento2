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

namespace TIG\PostNL\Service\Handler;

use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Config\Provider\ProductOptions as ProductOptionsConfiguration;
use TIG\PostNL\Model\ResourceModel\ShipmentBarcode\CollectionFactory;
use TIG\PostNL\Model\ShipmentBarcode;
use TIG\PostNL\Model\ShipmentBarcodeFactory;
use TIG\PostNL\Webservices\Endpoints\Barcode as BarcodeEndpoint;
use TIG\PostNL\Webservices\Parser\Label\Shipments as LabelParser;
use TIG\PostNL\Model\Shipment;

// @codingStandardsIgnoreFile
class BarcodeHandler
{
    /**
     * @var BarcodeEndpoint
     */
    private $barcodeEndpoint;

    /**
     * @var CollectionFactory
     */
    private $shipmentBarcodeCollectionFactory;

    /**
     * @var ShipmentBarcodeFactory
     */
    private $shipmentBarcodeFactory;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var ProductOptionsConfiguration
     */
    private $productOptionsConfiguration;

    /**
     * @var string
     */
    private $countryId;

    /**
     * $var null|int
     */
    private $storeId;

    /** @var LabelParser  */
    private $labelParser;

    /** @var Shipment  */
    private $shipment;

    /**
     * @param BarcodeEndpoint             $barcodeEndpoint
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param ShipmentBarcodeFactory      $shipmentBarcodeFactory
     * @param CollectionFactory           $shipmentBarcodeCollectionFactory
     * @param ProductOptionsConfiguration $productOptionsConfiguration
     * @param LabelParser                 $labelParser
     * @param Shipment                    $shipment
     */
    public function __construct(
        BarcodeEndpoint $barcodeEndpoint,
        ShipmentRepositoryInterface $shipmentRepository,
        ShipmentBarcodeFactory $shipmentBarcodeFactory,
        CollectionFactory $shipmentBarcodeCollectionFactory,
        ProductOptionsConfiguration $productOptionsConfiguration,
        LabelParser $labelParser,
        Shipment $shipment
    ) {
        $this->barcodeEndpoint = $barcodeEndpoint;
        $this->shipmentBarcodeCollectionFactory = $shipmentBarcodeCollectionFactory;
        $this->shipmentBarcodeFactory = $shipmentBarcodeFactory;
        $this->shipmentRepository = $shipmentRepository;
        $this->productOptionsConfiguration = $productOptionsConfiguration;
        $this->labelParser = $labelParser;
        $this->shipment = $shipment;
    }

    /**
     * @param $magentoShipmentId
     * @param $countryId
     *
     * @throws LocalizedException
     */
    public function prepareShipment($magentoShipmentId, $countryId)
    {
        $this->countryId = $countryId;
        $shipment = $this->shipmentRepository->getByShipmentId($magentoShipmentId);

        if (!$shipment || $shipment->getMainBarcode() !== null || $shipment->getConfirmedAt() !== null) {
            return;
        }

        $magentoShipment = $shipment->getShipment();
        $this->storeId = $magentoShipment->getStoreId();

        $mainBarcode = $this->generate($shipment);
        $shipment->setMainBarcode($mainBarcode);
        $this->shipmentRepository->save($shipment);

        if ($shipment->getParcelCount() > 1) {
            $this->addBarcodes($shipment, $mainBarcode);
        }

        if ($this->canAddReturnBarcodes($countryId, $shipment)) {
            $this->addReturnBarcodes($shipment);
        }
    }

    /**
     * Generate and save a new barcode for the just saved shipment
     *
     * @param ShipmentInterface $shipment
     * @param                   $mainBarcode
     *
     * @throws \Exception
     */
    public function addBarcodes(ShipmentInterface $shipment, $mainBarcode)
    {
        /** @var \TIG\PostNL\Model\ResourceModel\ShipmentBarcode\Collection $barcodeModelCollection */
        $barcodeModelCollection = $this->shipmentBarcodeCollectionFactory->create();
        $barcodeModelCollection->load();

        /**
         * The first item is the main barcode
         */
        $barcodeModelCollection->addItem($this->createBarcode($shipment->getId(), 1, $mainBarcode));

        $parcelCount = $shipment->getParcelCount();
        for ($count = 2; $count <= $parcelCount; $count++) {
            $barcodeModelCollection->addItem(
                $this->createBarcode($shipment->getId(), $count, $this->generate($shipment))
            );
        }

        $barcodeModelCollection->save();
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @throws LocalizedException
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    public function addReturnBarcodes(ShipmentInterface $shipment)
    {
        $isReturnBarcode = true;

        /** @var \TIG\PostNL\Model\ResourceModel\ShipmentBarcode\Collection $barcodeModelCollection */
        $barcodeModelCollection = $this->shipmentBarcodeCollectionFactory->create();
        $barcodeModelCollection->load();

        $parcelCount = $shipment->getParcelCount();

        for ($count = 1; $count <= $parcelCount; $count++) {
            $returnBarcode = $this->generate($shipment, $isReturnBarcode);
            $barcodeModelCollection->addItem(
                $this->createBarcode($shipment->getId(), $count, $returnBarcode, $isReturnBarcode)
            );
            $shipment->setReturnBarcode($returnBarcode);
            $this->shipmentRepository->save($shipment);
        }

        $barcodeModelCollection->save();
    }

    /**
     * CIF call to generate a new barcode
     *
     * @param ShipmentInterface $shipment
     * @param bool              $isReturnBarcode
     *
     * @return string
     * @throws LocalizedException
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    private function generate(ShipmentInterface $shipment, $isReturnBarcode = false)
    {
        $magentoShipment = $shipment->getShipment();

        $this->barcodeEndpoint->setCountryId($this->countryId);
        $this->barcodeEndpoint->setStoreId($magentoShipment->getStoreId());
        $this->setTypeByProductCode($shipment->getProductCode());
        $response = $this->barcodeEndpoint->call($shipment, $isReturnBarcode);

        if (!is_object($response) || !isset($response->Barcode)) {
            // Should throw an exception otherwise the postnl flow will break.
            throw new LocalizedException(
                __('Invalid GenerateBarcode response: %1', var_export($response, true))
            );
        }

        return (string) $response->Barcode;
    }

    /**
     * @param $code
     */
    private function setTypeByProductCode($code)
    {
        if ($this->productOptionsConfiguration->checkProductByFlags($code, 'group', 'priority_options')) {
            $this->barcodeEndpoint->setType('PEPS');

            return;
        }

        $this->barcodeEndpoint->setType('');
    }

    /**
     * @param      $shipmentId
     * @param      $count
     * @param      $barcode
     * @param bool $isReturnBarcode
     *
     * @return ShipmentBarcode
     */
    private function createBarcode($shipmentId, $count, $barcode, $isReturnBarcode = false)
    {
        /** @var \TIG\PostNL\Model\ShipmentBarcode $barcodeModel */
        $barcodeModel = $this->shipmentBarcodeFactory->create();
        $barcodeModel->setParentId($shipmentId);
        $barcodeModel->setType(ShipmentBarcode::BARCODE_TYPE_SHIPMENT);

        if ($isReturnBarcode) {
            $barcodeModel->setType(ShipmentBarcode::BARCODE_TYPE_RETURN);
        }

        $barcodeModel->setNumber($count);
        $barcodeModel->setValue($barcode);

        return $barcodeModel;
    }

    /**
     * @param                   $countryId
     * @param ShipmentInterface $shipment
     *
     * @return bool
     */
    public function canAddReturnBarcodes($countryId, ShipmentInterface $shipment)
    {
        if (
            (!in_array($countryId, ['NL', 'BE']) ||
             (!$shipment->isDomesticShipment()) ||
             (!$this->labelParser->canReturnNl($countryId)) ||
             (!$this->labelParser->canReturnBe($countryId)) ||
             ($shipment->isExtraAtHome()) ||
             ($shipment->isBuspakjeShipment()))
        ) {
            return false;
        }

        return true;
    }
}
