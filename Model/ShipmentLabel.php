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

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use TIG\PostNL\Api\Data\ShipmentLabelInterface;
use Magento\Framework\Model\AbstractModel as MagentoModel;
use TIG\PostNL\Api\ShipmentRepositoryInterface;

// @codingStandardsIgnoreFile
class ShipmentLabel extends MagentoModel implements ShipmentLabelInterface
{
    const FIELD_PARENT_ID    = 'parent_id';
    const FIELD_NUMBER       = 'number';
    const FIELD_LABEL        = 'label';
    const FIELD_TYPE         = 'type';
    const FIELD_PRODUCT_CODE = 'product_code';
    const FIELD_RETURN_LABEL = 'return_label';

    /**
     * @var string
     */
    // @codingStandardsIgnoreLine
    protected $_eventPrefix = 'tig_postnl_shipment_label';

    /** @var ShipmentRepositoryInterface  */
    private $shipmentRepository;

    /**
     * @param Context                     $context
     * @param Registry                    $registry
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param AbstractResource|null       $resource
     * @param AbstractDb|null             $resourceCollection
     * @param array                       $data
     */
    public function __construct(
        Context                     $context,
        Registry                    $registry,
        ShipmentRepositoryInterface $shipmentRepository,
        AbstractResource            $resource = null,
        AbstractDb                  $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * Constructor
     */
    protected function _construct()
    {
        // @codingStandardsIgnoreLine
        $this->_init('TIG\PostNL\Model\ResourceModel\ShipmentLabel');
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
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function setParentId($value)
    {
        return $this->setData(static::FIELD_PARENT_ID, $value);
    }

    /**
     * @param int $value
     *
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function setNumber($value)
    {
        return $this->setData(static::FIELD_NUMBER, $value);
    }

    /**
     * @return int
     */
    public function getNumber()
    {
        return $this->getData(static::FIELD_NUMBER);
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->getData(static::FIELD_LABEL);
    }

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function setLabel($value)
    {
        return $this->setData(static::FIELD_LABEL, $value);
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
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function setType($value)
    {
        return $this->setData(static::FIELD_TYPE, $value);
    }

    /**
     * @return int
     */
    public function getProductCode()
    {
        return $this->getData(static::FIELD_PRODUCT_CODE);
    }

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function setProductCode($value)
    {
        return $this->setData(static::FIELD_PRODUCT_CODE, $value);
    }

    /**
     * @return \TIG\PostNL\Api\Data\ShipmentInterface
     */
    public function getShipment()
    {
        return $this->shipmentRepository->getById($this->getParentId());
    }

    /**
     * @return string
     */
    public function getReturnLabel()
    {
        return $this->getData(static::FIELD_RETURN_LABEL);
    }

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function isReturnLabel($value)
    {
        return $this->setData(static::FIELD_RETURN_LABEL, $value);
    }
}
