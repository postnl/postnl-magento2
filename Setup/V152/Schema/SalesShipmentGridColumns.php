<?php

namespace TIG\PostNL\Setup\V152\Schema;

use TIG\PostNL\Setup\AbstractColumnsInstaller;

class SalesShipmentGridColumns extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'sales_shipment_grid';

    // @codingStandardsIgnoreLine
    protected $columns = [
        'tig_postnl_confirmed',
    ];

    /**
     * @return array
     */
    public function installTigPostnlConfirmedColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            'default' => 0,
            'nullable' => false,
            'comment' => 'PostNL Confirmed',
            'after' => 'tig_postnl_product_code',
        ];
    }
}
