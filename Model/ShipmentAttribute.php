<?php

namespace TIG\PostNL\Model;

use Magento\Framework\Model\AbstractModel as MagentoAbstractModel;
use TIG\PostNL\Api\Data\ShipmentAttributeInterface;

class ShipmentAttribute extends MagentoAbstractModel implements ShipmentAttributeInterface
{
    public function getProductCode(): string
    {
        return (string)$this->getData(self::PRODUCT_CODE);
    }

    public function setProductCode(string $value): ShipmentAttributeInterface
    {
        return $this->setData(self::PRODUCT_CODE, $value);
    }

    public function getShipAt(): string
    {
        return (string)$this->getData(self::SHIP_AT);
    }

    public function setShipAt(string $value): ShipmentAttributeInterface
    {
        return $this->setData(self::SHIP_AT, $value);
    }

    public function getType(): string
    {
        return (string)$this->getData(self::TYPE);
    }

    public function setType(string $value): ShipmentAttributeInterface
    {
        return $this->setData(self::TYPE, $value);
    }

    public function getDeliveryDate(): ?string
    {
        return (string)$this->getData(self::DELIVERY_DATE);
    }

    public function setDeliveryDate(?string $value): ShipmentAttributeInterface
    {
        return $this->setData(self::DELIVERY_DATE, $value);
    }

    public function getExpectedDeliveryTimeStart(): ?string
    {
        return (string)$this->getData(self::DELIVERY_TIME_START);
    }

    public function setExpectedDeliveryTimeStart(?string $value): ShipmentAttributeInterface
    {
        return $this->setData(self::DELIVERY_TIME_START, $value);
    }

    public function getExpectedDeliveryTimeEnd(): ?string
    {
        return (string)$this->getData(self::DELIVERY_TIME_END);
    }

    public function setExpectedDeliveryTimeEnd(?string $value): ShipmentAttributeInterface
    {
        return $this->setData(self::DELIVERY_TIME_END, $value);
    }
}
