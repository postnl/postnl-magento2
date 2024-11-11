<?php

namespace TIG\PostNL\Api\Data;

// @codingStandardsIgnoreFile
use TIG\PostNL\Model\Order;

/**
 * Too many public methods for the code inspection.
 */
interface ShipmentInterface
{
    public const RETURN_STATUS_DEFAULT = 0;
    public const RETURN_STATUS_BLOCKED = 1;
    public const RETURN_STATUS_RELEASED = 2;

    /**
     * @return int|null
     */
    public function getId();

    /**
     * @return int|null
     */
    public function getEntityId();

    /**
     * @param $value
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setShipmentId($value);

    /**
     * @return int|null
     */
    public function getShipmentId();

    /**
     * @param int $value
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setOrderId($value);

    /**
     * @return int|null
     */
    public function getOrderId();

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setMainBarcode($value);

    /**
     * @return string|null
     */
    public function getMainBarcode();

    /**
     * @param int $currentShipmentNumber
     *
     * @return string
     */
    public function getBarcode($currentShipmentNumber = 1);

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setProductCode($value);

    /**
     * @return string|null
     */
    public function getProductCode();

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setShipmentType($value);

    /**
     * @return string|null
     */
    public function getShipmentType();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setShipmentCountry($value);

    /**
     * @return string
     */
    public function getShipmentCountry();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setAcCharacteristic($value);

    /**
     * @return string|null
     */
    public function getAcCharacteristic();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setAcOption($value);

    /**
     * @return string|null
     */
    public function getAcOption();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setAcInformation($value);

    /**
     * @return string|null
     */
    public function getAcInformation();

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setDeliveryDate($value);

    /**
     * @return string|null
     */
    public function getDeliveryDate();

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setIsPakjegemak($value);

    /**
     * @return string|null
     */
    public function getIsPakjegemak();

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setPgLocationCode($value);

    /**
     * @return string|null
     */
    public function getPgLocationCode();

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setPgRetailNetworkId($value);

    /**
     * @return string|null
     */
    public function getPgRetailNetworkId();

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setParcelCount($value);

    /**
     * @return string|null
     */
    public function getParcelCount();

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setShipAt($value);

    /**
     * @return string|null
     */
    public function getShipAt();

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setConfirmedAt($value);

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setConfirmed($value);

    /**
     * @return string|null
     */
    public function getConfirmedAt();

    /**
     * @return bool
     */
    public function getConfirmed();

    /**
     * @return string|null
     */
    public function setDownpartnerId($value);

    /**
     * @return string|null
     */
    public function getDownpartnerId();

    /**
     * @return string|null
     */
    public function setDownpartnerLocation($value);

    /**
     * @return string|null
     */
    public function getDownpartnerLocation();

    /**
     * @return string|null
     */
    public function setDownpartnerBarcode($value);

    /**
     * @return string|null
     */
    public function getDownpartnerBarcode();

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function changeCreatedAt($value);

    /**
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function changeUpdatedAt($value);

    /**
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * @return float
     */
    public function getTotalWeight();

    /**
     * @param string $format
     *
     * @return string
     */
    public function getDeliveryDateFormatted($format = 'd-m-Y H:i:s');

    /**
     * @return bool
     */
    public function isExtraCover();

    /**
     * @return bool
     */
    public function isGlobalPack();

    /**
     * @return bool
     */
    public function isBoxablePackets(): bool;

    /**
     * @return bool
     */
    public function isInternationalPacket(): bool;

    /**
     * @return bool
     */
    public function isExtraAtHome();

    /**
     * @return bool
     */
    public function isBuspakjeShipment();

    /**
     * @return bool
     */
    public function isDomesticShipment();

    /**
     * @return bool
     */
    public function isIDCheck();

    /**
     * @return float
     */
    public function getExtraCoverAmount();

    /**
     * @return \Magento\Sales\Api\Data\OrderAddressInterface;
     */
    public function getOriginalShippingAddress();

    /**
     * @return \Magento\Sales\Api\Data\ShipmentInterface
     */
    public function getShipment();

    /**
     * @return \Magento\Sales\Api\Data\OrderAddressInterface
     */
    public function getShippingAddress();

    /**
     * @return bool
     */
    public function canChangeParcelCount();

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setReturnBarcode($value);

    /**
     * @return string|null
     */
    public function getReturnBarcodes();

    /**
     * @param $value
     *
     * @return TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setIsSmartReturn($value);

    /**
     * @return boolean
     */
    public function getIsSmartReturn();

    /**
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function getPostNLOrder();

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setSmartReturnBarcode($value);

    /**
     * @return string
     */
    public function getSmartReturnBarcode();

    /**
     * @param $value
     * @return boolean
     */
    public function setSmartReturnEmailSent($value);

    /**
     * @return boolean
     */
    public function getSmartReturnEmailSent();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setInsuredTier($value);

    /**
     * @return string
     */
    public function getInsuredTier();

    /**
     * @param int $value
     *
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function setReturnStatus(int $value): ShipmentInterface;

    /**
     * @return int
     */
    public function getReturnStatus(): int;


}
