<?php

namespace TIG\PostNL\Setup\V120\Schema;

use \TIG\PostNL\Setup\AbstractColumnsInstaller;

class UpgradeOrderTable extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'tig_postnl_order';

    // @codingStandardsIgnoreLine
    protected $columns = [
        'parcel_count'
    ];

    public function installParcelCountColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length' => 11,
            'nullable' => true,
            'default' => null,
            'comment' => 'Parcel Count',
            'after' => 'product_code',
        ];
    }
}
