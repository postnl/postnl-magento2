<?php

namespace TIG\PostNL\Setup\V120\Schema;

use TIG\PostNL\Setup\AbstractColumnsInstaller;

class SalesOrderGridColumns extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'sales_order_grid';

    // @codingStandardsIgnoreLine
    protected $columns = [
        'tig_postnl_product_code',
    ];

    /**
     * @return array
     */
    public function installTigPostnlProductCodeColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length' => 11,
            'nullable' => true,
            'default' => null,
            'comment' => 'PostNL product code',
            'after' => 'tig_postnl_ship_at',
        ];
    }
}
