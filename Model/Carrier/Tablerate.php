<?php

namespace TIG\PostNL\Model\Carrier;

use Magento\OfflineShipping\Model\Carrier\Tablerate as MagentoTablerate;

class Tablerate extends MagentoTablerate
{
    /** @var string */
    // @codingStandardsIgnoreLine
    protected $_code = 'tig_postnl';

    /**
     * Constructor defining the model table and primary key
     */
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        // @codingStandardsIgnoreLine
        $this->_init('tig_postnl_tablerate', 'entity_id');
    }
}
