<?php

namespace TIG\PostNL\Model\Config\Backend;

use TIG\PostNL\Service\Validation\AlternativeDelivery;

class SerializedDelivery extends \Magento\Config\Model\Config\Backend\Serialized\ArraySerialized
{
    public function beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value)) {
            unset($value['__empty']);
            usort($value, fn($a, $b) => $a[AlternativeDelivery::DELIVERY_MAP_SCALE] <=> $b[AlternativeDelivery::DELIVERY_MAP_SCALE]);
            $this->setValue($value);
        }
        return parent::beforeSave();
    }
}
