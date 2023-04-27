<?php

namespace TIG\PostNL\Setup\V190\Schema;

use TIG\PostNL\Setup\AbstractColumnsInstaller;

class InstallReturnLabel extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'tig_postnl_shipment_label';

    // @codingStandardsIgnoreLine
    protected $columns = [
        'return_label'
    ];

    public function installReturnLabelColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            'nullable' => false,
            'default'  => 0,
            'comment'  => 'Return Label',
            'after' => 'product_code',
        ];
    }
}
