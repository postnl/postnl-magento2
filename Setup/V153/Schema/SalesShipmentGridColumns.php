<?php

namespace TIG\PostNL\Setup\V153\Schema;

use TIG\PostNL\Setup\AbstractColumnsInstaller;

class SalesShipmentGridColumns extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'sales_shipment_grid';

    // @codingStandardsIgnoreLine
    protected $columns = [
        'tig_postnl_barcode',
    ];

    /**
     * @return array
     */
    public function installTigPostnlBarcodeColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'PostNL Barcode',
            'after' => 'tig_postnl_product_code',
        ];
    }
}
