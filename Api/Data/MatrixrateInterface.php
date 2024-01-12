<?php

namespace TIG\PostNL\Api\Data;

// @codingStandardsIgnoreFile
interface MatrixrateInterface
{
    /**
     * @return int
     */
    public function getEntityId();

    /**
     * @return int
     */
    public function getWebsiteId();

    /**
     * @param int $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setWebsiteId($value);

    /**
     * @return string
     */
    public function getDestinyCountryId();

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setDestinyCountryId($value);

    /**
     * @return int
     */
    public function getDestinyRegionId();

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setDestinyRegionId($value);

    /**
     * @return string
     */
    public function getDestinyZipCode();

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setDestinyZipCode($value);

    /**
     * @return float
     */
    public function getWeight();

    /**
     * @param float $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setWeight($value);

    /**
     * @return float
     */
    public function getSubtotal();

    /**
     * @param float $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setSubtotal($value);

    /**
     * @return int
     */
    public function getQuantity();

    /**
     * @param int $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setQuantity($value);

    /**
     * @return string
     */
    public function getParcelType();

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setParcelType($value);

    /**
     * @return float
     */
    public function getPrice();

    /**
     * @param float $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setPrice($value);
}
