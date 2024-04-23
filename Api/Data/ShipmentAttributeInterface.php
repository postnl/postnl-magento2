<?php

namespace TIG\PostNL\Api\Data;

interface ShipmentAttributeInterface
{
    public const PRODUCT_CODE = 'product_code';
    public const SHIP_AT = 'ship_at';
    public const TYPE = 'type';
    public const DELIVERY_DATE = 'delivery_date';
    public const DELIVERY_TIME_START = 'expected_delivery_time_start';
    public const DELIVERY_TIME_END = 'expected_delivery_time_end';

    /**
     * @return string
     */
    public function getProductCode(): string;

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentAttributeInterface
     */
    public function setProductCode(string $value): ShipmentAttributeInterface;

    /**
     * @return string
     */
    public function getShipAt(): string;

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentAttributeInterface
     */
    public function setShipAt(string $value): ShipmentAttributeInterface;

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentAttributeInterface
     */
    public function setType(string $value): ShipmentAttributeInterface;

    /**
     * @return string
     */
    public function getDeliveryDate(): ?string;

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentAttributeInterface
     */
    public function setDeliveryDate(?string $value): ShipmentAttributeInterface;

    /**
     * @return string
     */
    public function getExpectedDeliveryTimeStart(): ?string;

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentAttributeInterface
     */
    public function setExpectedDeliveryTimeStart(?string $value): ShipmentAttributeInterface;

    /**
     * @return string
     */
    public function getExpectedDeliveryTimeEnd(): ?string;

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentAttributeInterface
     */
    public function setExpectedDeliveryTimeEnd(?string $value): ShipmentAttributeInterface;
}
