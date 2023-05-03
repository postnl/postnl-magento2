<?php

namespace TIG\PostNL\Model\Carrier\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Matrixrate extends AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init('tig_postnl_matrixrate', 'entity_id');
    }
}
