<?php

namespace TIG\PostNL\Setup\V152\Schema;

use \TIG\PostNL\Setup\AbstractColumnsInstaller;

class UpgradeShipmentTable extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'tig_postnl_shipment';

    // @codingStandardsIgnoreLine
    protected $columns = [
        'confirmed'
    ];

    public function installConfirmedColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            'default' => 0,
            'nullable' => false,
            'comment' => 'PostNL Confirmed',
            'after' => 'confirmed_at',
        ];
    }
}
