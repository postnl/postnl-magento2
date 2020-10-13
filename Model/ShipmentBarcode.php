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
