<?php

namespace TIG\PostNL\Setup\V1125\Schema;

use TIG\PostNL\Setup\AbstractColumnsInstaller;

class InstallIsSmartReturn extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'tig_postnl_shipment';

    // @codingStandardsIgnoreLine
    protected $columns = [
        'is_smart_return'
    ];

    /**
     * @return array
     */
    public function installIsSmartReturnColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            'default'  => 0,
            'nullable' => false,
            'comment'  => 'Is Smart Return'
        ];
    }
}
