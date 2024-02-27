<?php

namespace TIG\PostNL\Api\Data;

// @codingStandardsIgnoreFile
/**
 * Too many public methods for the code inspection.
 */
interface ShipmentLabelInterface
{
    const BARCODE_TYPE_LABEL = 'label';

    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @return int
     */
    public function getParentId();

    /**
     * @param int $value
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function setParentId($value);

    /**
     * @param int $value
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function setNumber($value);

    /**
     * @return int
     */
    public function getNumber();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function setLabel($value);

    /**
     * @return string
     */
    public function getLabelFileFormat();

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function setLabelFileFormat(string $value);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $value
     * @@return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function setProductCode($value);

    /**
     * @return int
     */
    public function getProductCode();

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function setType($value);

    /**
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function getShipment();

    /**
     * @param boolean $value
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function isReturnLabel($value);

    /**
     * @return string
     */
    public function getReturnLabel();

    /**
     * @param boolean $value
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function isSmartReturnLabel(bool $value): self;

    /**
     * @return bool
     */
    public function getSmartReturnLabel(): bool;
}
