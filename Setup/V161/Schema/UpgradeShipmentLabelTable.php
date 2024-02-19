<?php

namespace TIG\PostNL\Setup\V161\Schema;

use \TIG\PostNL\Setup\AbstractColumnsInstaller;

class UpgradeShipmentLabelTable extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'tig_postnl_shipment_label';

    // @codingStandardsIgnoreLine
    protected $columns = [
        'product_code'
    ];

    public function installProductCodeColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length' => 4,
            'nullable' => true,
            'default' => null,
            'comment' => 'Product Code',
            'after' => 'type',
        ];
    }
}
