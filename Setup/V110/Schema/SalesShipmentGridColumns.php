<?php

namespace TIG\PostNL\Setup\V110\Schema;

use TIG\PostNL\Setup\AbstractColumnsInstaller;

class SalesShipmentGridColumns extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'sales_shipment_grid';

    // @codingStandardsIgnoreLine
    protected $columns = [
        'tig_postnl_ship_at',
        'tig_postnl_confirmed_at',
    ];

    /**
     * @return array
     */
    public function installTigPostnlShipAtColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => true,
            'default' => null,
            'comment' => 'When is this shipment due for sending',
            'after' => 'shipping_information',
        ];
    }

    /**
     * @return array
     */
    public function installTigPostnlConfirmedAtColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATE,
            'nullable' => true,
            'default' => null,
            'comment' => 'When is this order confirmed?',
            'after' => 'tig_postnl_ship_at',
        ];
    }
}
