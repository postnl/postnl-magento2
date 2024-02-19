<?php

namespace TIG\PostNL\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Shipment extends AbstractDb
{
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        // @codingStandardsIgnoreLine
        $this->_init('tig_postnl_shipment', 'entity_id');
    }
}
