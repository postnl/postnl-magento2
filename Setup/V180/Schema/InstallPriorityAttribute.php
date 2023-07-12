<?php

namespace TIG\PostNL\Setup\V180\Schema;

use \TIG\PostNL\Setup\AbstractColumnsInstaller;

class InstallPriorityAttribute extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'tig_postnl_shipment';
    
    // @codingStandardsIgnoreLine
    protected $columns = [
        'shipment_country'
    ];
    
    public function installShipmentCountryColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'   => 12,
            'nullable' => true,
            'default'  => null,
            'comment'  => 'Country',
            'after'    => 'shipment_type'
        ];
    }
}
