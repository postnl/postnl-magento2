<?php

namespace TIG\PostNL\Model\Carrier;

use Magento\Framework\Model\AbstractModel;
use TIG\PostNL\Api\Data\MatrixrateInterface;

// @codingStandardsIgnoreFile
class Matrixrate extends AbstractModel implements MatrixrateInterface
{
    /** @var string */
    // @codingStandardsIgnoreLine
    protected $_code = 'tig_postnl';

    const FIELD_WEBSITE_ID = 'website_id';
    const FIELD_DESTINY_COUNTRY_ID = 'destiny_country_id';
    const FIELD_DESTINY_REGION_ID = 'destiny_region_id';
    const FIELD_DESTINY_ZIP_CODE = 'destiny_zip_code';
    const FIELD_WEIGHT = 'weight';
    const FIELD_SUBTOTAL = 'subtotal';
    const FIELD_QUANTITY = 'quantity';
    const FIELD_PARCEL_TYPE = 'parcel_type';
    const FIELD_PRICE = 'price';

    /**
     * Constructor defining the model table and primary key
     */
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        // @codingStandardsIgnoreLine
        $this->_init('TIG\PostNL\Model\Carrier\ResourceModel\Matrixrate');
    }

    /**
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->getData(static::FIELD_WEBSITE_ID);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setWebsiteId($value)
    {
        $this->setData(static::FIELD_WEBSITE_ID, $value);

        return $this;
    }

    /**
     * @return string
     */
    public function getDestinyCountryId()
    {
        return $this->getData(static::FIELD_DESTINY_COUNTRY_ID);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setDestinyCountryId($value)
    {
        $this->setData(static::FIELD_DESTINY_COUNTRY_ID, $value);

        return $this;
    }

    /**
     * @return int
     */
    public function getDestinyRegionId()
    {
        return $this->getData(static::FIELD_DESTINY_REGION_ID);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setDestinyRegionId($value)
    {
        $this->setData(static::FIELD_DESTINY_REGION_ID, $value);

        return $this;
    }

    /**
     * @return string
     */
    public function getDestinyZipCode()
    {
        return $this->getData(static::FIELD_DESTINY_ZIP_CODE);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setDestinyZipCode($value)
    {
        $this->setData(static::FIELD_DESTINY_ZIP_CODE, $value);

        return $this;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->getData(static::FIELD_WEIGHT);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setWeight($value)
    {
        $this->setData(static::FIELD_WEIGHT, $value);

        return $this;
    }

    /**
     * @return float
     */
    public function getSubtotal()
    {
        return $this->getData(static::FIELD_SUBTOTAL);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setSubtotal($value)
    {
        $this->setData(static::FIELD_SUBTOTAL, $value);

        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->getData(static::FIELD_QUANTITY);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setQuantity($value)
    {
        $this->setData(static::FIELD_QUANTITY, $value);

        return $this;
    }

    /**
     * @return string
     */
    public function getParcelType()
    {
        return $this->getData(static::FIELD_PARCEL_TYPE);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setParcelType($value)
    {
        $this->setData(static::FIELD_PARCEL_TYPE, $value);

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->getData(static::FIELD_PRICE);
    }

    /**
     * @param $value
     *
     * @return \TIG\PostNL\Api\Data\MatrixrateInterface
     */
    public function setPrice($value)
    {
        $this->setData(static::FIELD_PRICE, $value);

        return $this;
    }
}
