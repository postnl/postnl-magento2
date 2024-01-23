<?php

namespace TIG\PostNL\Setup\V1140\Schema;

use TIG\PostNL\Setup\AbstractColumnsInstaller;

class InstallReturnStatus extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'tig_postnl_shipment';

    protected $columns = [
        'return_status'
    ];

    public function installReturnStatusColumn(): array
    {
        return [
            'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'default'  => 0,
            'nullable' => false,
            'comment'  => 'Return Status'
        ];
    }
}
