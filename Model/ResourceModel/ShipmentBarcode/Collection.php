<?php

namespace TIG\PostNL\Model\ResourceModel\ShipmentBarcode;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        // @codingStandardsIgnoreLine
        $this->_init('TIG\PostNL\Model\ShipmentBarcode', 'TIG\PostNL\Model\ResourceModel\ShipmentBarcode');
    }
}
