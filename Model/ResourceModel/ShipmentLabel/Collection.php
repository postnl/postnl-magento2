<?php

namespace TIG\PostNL\Model\ResourceModel\ShipmentLabel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        // @codingStandardsIgnoreLine
        $this->_init('TIG\PostNL\Model\ShipmentLabel', 'TIG\PostNL\Model\ResourceModel\ShipmentLabel');
    }
}
