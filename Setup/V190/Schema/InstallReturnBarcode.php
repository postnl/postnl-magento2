<?php

namespace TIG\PostNL\Setup\V190\Schema;

use TIG\PostNL\Setup\AbstractColumnsInstaller;

class InstallReturnBarcode extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'tig_postnl_shipment';

    // @codingStandardsIgnoreLine
    protected $columns = [
        'return_barcode'
    ];

    public function installReturnBarcodeColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'   => 32,
            'nullable' => true,
            'default'  => null,
            'comment'  => 'Return Barcode'
        ];
    }
}
