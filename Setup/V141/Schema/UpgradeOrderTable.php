<?php

namespace TIG\PostNL\Setup\V141\Schema;

use \TIG\PostNL\Setup\AbstractColumnsInstaller;

class UpgradeOrderTable extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'tig_postnl_order';

    // @codingStandardsIgnoreLine
    protected $columns = [
        'shipping_duration'
    ];

    public function installShippingDurationColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length' => 11,
            'nullable' => true,
            'default' => null,
            'comment' => 'Shipping Duration',
            'after' => 'delivery_date',
        ];
    }
}
