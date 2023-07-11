<?php

namespace TIG\PostNL\Model\ResourceModel\Tablerate;

class Collection extends \Magento\OfflineShipping\Model\ResourceModel\Carrier\Tablerate\Collection
{
    /**
     * OfflineShipping uses the exact methods we need, except for a different resource collection.
     * Therefore only the constructor needs to be overriden with the PostNL resource collection.
     */
    // @codingStandardsIgnoreLine
    protected function _construct()
    {
        // @codingStandardsIgnoreLine
        $this->_init('TIG\PostNL\Model\Carrier\Tablerate', 'TIG\PostNL\Model\ResourceModel\Tablerate');

        $this->_countryTable = $this->getTable('directory_country');
        $this->_regionTable = $this->getTable('directory_country_region');
    }
}
