<?php

namespace TIG\PostNL\Model;

use TIG\PostNL\Api\Data\ShipmentBarcodeInterface;
use Magento\Framework\Model\AbstractModel as MagentoModel;

class ShipmentBarcode extends MagentoModel implements ShipmentBarcodeInterface
{
    const BARCODE_TYPE_SHIPMENT = 'shipment';
    const BARCODE_TYPE_RETURN = 'return';

    const FIELD_PARENT_ID = 'parent_id';
    const FIELD_TYPE = 'type';
    const FIELD_NUMBER = 'number';
    const FIELD_VALUE = 'value';

    /**
     * @var string
     */
    // @codingStandardsIgnoreLine
    protected $_eventPrefix = 'tig_postnl_shipment_barcode';

    /**
     * Constructor
     */
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        // @codingStandardsIgnoreLine
        $this->_init('TIG\PostNL\Model\ResourceModel\ShipmentBarcode');
    }

    /**
     * @return int
     */
    public function getParentId()
    {
        return $this->getData(static::FIELD_PARENT_ID);
    }

    /**
     * @param int $value
     *
     * @return \TIG\PostNL\Api\Data\ShipmentBarcodeInterface
     */
    public function changeParentId($value)
    {
        return $this->setData(static::FIELD_PARENT_ID, $value);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getData(static::FIELD_TYPE);
    }

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\ShipmentBarcodeInterface
     */
    public function changeType($value)
    {
        return $this->setData(static::FIELD_TYPE, $value);
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->getData(static::FIELD_NUMBER);
    }

    /**
     * @param int $value
     *
     * @return \TIG\PostNL\Api\Data\ShipmentBarcodeInterface
     */
    public function changeNumber($value)
    {
        return $this->setData(static::FIELD_NUMBER, $value);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->getData(static::FIELD_VALUE);
    }

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\ShipmentBarcodeInterface
     */
    public function changeValue($value)
    {
        return $this->setData(static::FIELD_VALUE, $value);
    }
}
