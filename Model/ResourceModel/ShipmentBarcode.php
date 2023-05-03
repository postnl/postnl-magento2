<?php

namespace TIG\PostNL\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ShipmentBarcode extends AbstractDb
{
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        // @codingStandardsIgnoreLine
        $this->_init('tig_postnl_shipment_barcode', 'entity_id');
    }
}
