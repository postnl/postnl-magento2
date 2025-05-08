<?php

namespace TIG\PostNL\Api\Data;

/**
 * @api
 */
interface ShipmentBarcodeInterface
{
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
     * @return \TIG\PostNL\Api\Data\ShipmentBarcodeInterface
     */
    public function changeParentId($value);

    /**
     * @return string
     */
    public function getType();

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentBarcodeInterface
     */
    public function changeType($value);

    /**
     * @return int
     */
    public function getNumber();

    /**
     * @param int $value
     * @return \TIG\PostNL\Api\Data\ShipmentBarcodeInterface
     */
    public function changeNumber($value);

    /**
     * @return string
     */
    public function getValue();

    /**
     * @param string $value
     * @return \TIG\PostNL\Api\Data\ShipmentBarcodeInterface
     */
    public function changeValue($value);
}
