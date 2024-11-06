<?php

namespace TIG\PostNL\Api\Data;

// @codingStandardsIgnoreFile
/**
 * Too many public methods for the code inspection.
 */
interface OrderInterface
{
    /**
     * @return int
     */
    public function getEntityId();

    /**
     * Sets entity ID.
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * @param int $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setOrderId($value);

    /**
     * @return int
     */
    public function getOrderId();

    /**
     * @param int $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setQuoteId($value);

    /**
     * @return int
     */
    public function getQuoteId();

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setType($value);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string|null $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setAcCharacteristic($value);

    /**
     * @return string|null
     */
    public function getAcCharacteristic();

    /**
     * @param string|null $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setAcOption($value);

    /**
     * @return string|null
     */
    public function getAcOption();

    /**
     * @param string|null $value
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
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setDeliveryDate($value);

    /**
     * @return string
     */
    public function getDeliveryDate();

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setExpectedDeliveryTimeStart($value);

    /**
     * @return string
     */
    public function getExpectedDeliveryTimeStart();

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setExpectedDeliveryTimeEnd($value);

    /**
     * @return string
     */
    public function getExpectedDeliveryTimeEnd();

    /**
     * @param bool $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setIsPakjegemak($value);

    /**
     * @return bool
     */
    public function getIsPakjegemak();

    /**
     * @return bool|int
     */
    public function getIsStatedAddressOnly();

    /**
     * @param int|bool $value
     *
     * @return OrderInterface
     */
    public function setIsStatedAddressOnly($value): self;

    /**
     * @param int $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setPgOrderAddressId($value);

    /**
     * @return int
     */
    public function getPgOrderAddressId();

    /**
     * @return \Magento\Sales\Api\Data\OrderAddressInterface;
     */
    public function getShippingAddress();

    /**
     * @return \Magento\Sales\Api\Data\OrderAddressInterface;
     */
    public function getBillingAddress();

    /**
     * @return \Magento\Sales\Api\Data\OrderAddressInterface;
     */
    public function getPgOrderAddress();

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setPgLocationCode($value);

    /**
     * @return string
     */
    public function getPgLocationCode();

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setPgRetailNetworkId($value);

    /**
     * @return string
     */
    public function getPgRetailNetworkId();

    /**
     * @param int $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setProductCode($value);

    /**
     * @return int
     */
    public function getProductCode();

    /**
     * @param float $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setFee($value);

    /**
     * @return float
     */
    public function getFee();

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setShipAt($value);

    /**
     * @return string
     */
    public function getShipAt();

    /**
     * @return mixed
     */
    public function getParcelCount();

    /**
     * @param mixed $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setParcelCount($value);

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setConfirmedAt($value);

    /**
     * @return string
     */
    public function getConfirmedAt();

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function changeCreatedAt($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function changeUpdatedAt($value);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @return int
     */
    public function getShippingDuration();

    /**
     * @param int $value
     *
     * @return int
     */
    public function setShippingDuration($value);

    /**
     * @param bool|int $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setConfirmed($value);

    /**
     * @return bool
     */
    public function getConfirmed();

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\OrderInterface
     */
    public function setInsuredTier($value);

    /**
     * @return string
     */
    public function getInsuredTier();
}
