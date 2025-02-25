<?php

namespace TIG\PostNL\Service\Handler;

use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Api\Data\ShipmentInterface;
use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use TIG\PostNL\Api\ShipmentBarcodeRepositoryInterface;
use TIG\PostNL\Api\ShipmentRepositoryInterface;
use TIG\PostNL\Config\Provider\ProductOptions as ProductOptionsConfiguration;
use TIG\PostNL\Model\ShipmentBarcode;
use TIG\PostNL\Model\ShipmentBarcodeFactory;
use TIG\PostNL\Service\Shipment\ResetPostNLShipment;
use TIG\PostNL\Webservices\Endpoints\Barcode as BarcodeEndpoint;
use TIG\PostNL\Webservices\Parser\Label\Shipments as LabelParser;

// @codingStandardsIgnoreFile
class BarcodeHandler
{
    private BarcodeEndpoint $barcodeEndpoint;
    private ShipmentBarcodeFactory $shipmentBarcodeFactory;
    private ShipmentRepositoryInterface $shipmentRepository;
    private ProductOptionsConfiguration $productOptionsConfiguration;
    private LabelParser $labelParser;
    private ResetPostNLShipment $resetPostNLShipment;
    private ShipmentBarcodeRepositoryInterface $shipmentBarcodeRepository;

    private string $countryId;
    private ?int $storeId;

    public function __construct(
        BarcodeEndpoint $barcodeEndpoint,
        ShipmentRepositoryInterface $shipmentRepository,
        ShipmentBarcodeFactory $shipmentBarcodeFactory,
        ProductOptionsConfiguration $productOptionsConfiguration,
        LabelParser $labelParser,
        ResetPostNLShipment $resetPostNLShipment,
        ShipmentBarcodeRepositoryInterface $shipmentBarcodeRepository
    ) {
        $this->barcodeEndpoint = $barcodeEndpoint;
        $this->shipmentBarcodeFactory = $shipmentBarcodeFactory;
        $this->shipmentRepository = $shipmentRepository;
        $this->productOptionsConfiguration = $productOptionsConfiguration;
        $this->labelParser = $labelParser;
        $this->resetPostNLShipment = $resetPostNLShipment;
        $this->shipmentBarcodeRepository = $shipmentBarcodeRepository;
    }

    /**
     * @throws LocalizedException
     */
    public function prepareShipment(int $magentoShipmentId, string $countryId, int $returnTypeFlag)
    {
        $this->countryId = $countryId;
        $shipment = $this->shipmentRepository->getByShipmentId($magentoShipmentId);
        $isReturn = false;

        if ($returnTypeFlag > 0) {
            $shipment->setIsSmartReturn($returnTypeFlag);
            $this->shipmentRepository->save($shipment);
            $isReturn = true;
        }

        if (!$this->validateShipment($magentoShipmentId, $countryId)) {
            return;
        }

        $magentoShipment = $shipment->getShipment();
        $this->storeId = $magentoShipment->getStoreId();

        $mainBarcode = $this->generate($shipment, $isReturn);
        if ($shipment->getIsSmartReturn() > 0) {
            $shipment->setSmartReturnBarcode($mainBarcode);
        } else {
            $shipment->setMainBarcode($mainBarcode);
        }
        $this->shipmentRepository->save($shipment);

        if ($shipment->getParcelCount() > 1) {
            $this->addBarcodes($shipment, $mainBarcode);
        }

        if ($this->canAddReturnBarcodes($countryId, $shipment)) {
            $this->addReturnBarcodes($shipment);
        }
    }

    /**
     * @param ShipmentInterface $shipment
     * @param                   $mainBarcode
     *
     * @throws LocalizedException
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    public function addBarcodes(ShipmentInterface $shipment, $mainBarcode): void
    {
        /**
         * The first item is the main barcode
         */
        $this->createBarcode($shipment->getId(), 1, $mainBarcode);

        $parcelCount = $shipment->getParcelCount();
        for ($count = 2; $count <= $parcelCount; $count++) {
            $this->createBarcode($shipment->getId(), $count, $this->generate($shipment));
        }
    }

    /**
     * @throws LocalizedException
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    public function addReturnBarcodes(ShipmentInterface $shipment): void
    {
        $parcelCount = $shipment->getParcelCount();

        for ($count = 1; $count <= $parcelCount; $count++) {
            $returnBarcode = $this->generate($shipment, true);
            $this->createBarcode($shipment->getId(), $count, $returnBarcode, true);

            if ($shipment->getIsSmartReturn() > 0) {
                $shipment->setSmartReturnBarcode($returnBarcode);
            }

            $shipment->setReturnBarcode($returnBarcode);
            $this->shipmentRepository->save($shipment);
        }
    }

    /**
     * CIF call to generate a new barcode
     *
     * @throws LocalizedException
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    private function generate(ShipmentInterface $shipment, bool $isReturnBarcode = false): string
    {
        $magentoShipment = $shipment->getShipment();

        $this->barcodeEndpoint->changeProductCode($shipment->getProductCode());
        $this->barcodeEndpoint->updateApiKey($magentoShipment->getStoreId());
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
     * @param      $shipmentId
     * @param      $count
     * @param      $barcode
     * @param bool $isReturnBarcode
     */
    private function createBarcode($shipmentId, $count, $barcode, bool $isReturnBarcode = false)
    {
        /** @var \TIG\PostNL\Model\ShipmentBarcode $barcodeModel */
        $barcodeModel = $this->shipmentBarcodeFactory->create();
        $barcodeModel->changeParentId($shipmentId);
        $barcodeModel->setType(ShipmentBarcode::BARCODE_TYPE_SHIPMENT);

        if ($isReturnBarcode) {
            $barcodeModel->setType(ShipmentBarcode::BARCODE_TYPE_RETURN);
        }

        $barcodeModel->setNumber($count);
        $barcodeModel->setValue($barcode);

        $this->shipmentBarcodeRepository->save($barcodeModel);
    }

    public function canAddReturnBarcodes(string $countryId, ShipmentInterface $shipment): bool
    {
        if (
            !$this->labelParser->canReturn($countryId, $shipment) ||
            $shipment->isExtraAtHome() ||
            $shipment->isBuspakjeShipment() ||
            $shipment->isBoxablePackets() ||
            $shipment->isInternationalPacket()
        ) {
            return false;
        }
        if ($shipment->getIsSmartReturn()) {
            return false;
        }

        return true;
    }

    /**
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     */
    private function validateShipment(int $magentoShipmentId, string $countryId): bool
    {
        $shipment = $this->shipmentRepository->getByShipmentId($magentoShipmentId);
        $isPrepared = (!$shipment || $shipment->getMainBarcode() !== null || $shipment->getConfirmedAt() !== null);

        if ($isPrepared && $this->canAddReturnBarcodes($countryId, $shipment) && !$shipment->getReturnBarcodes()) {
            $this->resetPostNLShipment->resetShipment($magentoShipmentId);
            return true;
        }

        if ($isPrepared && $shipment->getIsSmartReturn() > 0 && $shipment->getSmartReturnBarcode() === null) {
            return true;
        }

        if ($isPrepared) {
            return false;
        }

        return true;
    }
}
