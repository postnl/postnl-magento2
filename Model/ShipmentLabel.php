<?php

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
    const FIELD_LABEL_FILE   = 'label_file_type';
    const FIELD_TYPE         = 'type';
    const FIELD_PRODUCT_CODE = 'product_code';
    const FIELD_RETURN_LABEL = 'return_label';

    const FIELD_SMART_RETURN = 'smart_return_label';

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
    public function getLabelFileFormat()
    {
        return $this->getData(static::FIELD_LABEL_FILE);
    }

    /**
     * @param string $value
     *
     * @return \TIG\PostNL\Api\Data\ShipmentLabelInterface
     */
    public function setLabelFileFormat(string $value)
    {
        return $this->setData(static::FIELD_LABEL_FILE, $value);
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

    public function isSmartReturnLabel(int $value): ShipmentLabelInterface
    {
        return $this->setData(static::FIELD_SMART_RETURN, self::RETURN_LABEL_SMART_RETURN);
    }

    public function getSmartReturnLabel(): bool
    {
        return $this->isSmartReturnLabelFlag();
    }

    public function setReturnFlag(int $flag): ShipmentLabelInterface
    {
        return $this->setData(static::FIELD_SMART_RETURN, $flag);
    }

    public function getReturnFlag(): int
    {
        return (int)$this->getData(static::FIELD_SMART_RETURN);
    }

    public function isErsLabelFlag(): bool
    {
        return $this->getReturnFlag() === self::RETURN_LABEL_ERS;
    }

    public function isSmartReturnLabelFlag(): bool
    {
        return $this->getReturnFlag() === self::RETURN_LABEL_SMART_RETURN;
    }
}
