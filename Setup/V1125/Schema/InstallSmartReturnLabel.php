<?php

namespace TIG\PostNL\Setup\V1125\Schema;

use TIG\PostNL\Setup\AbstractColumnsInstaller;

class InstallSmartReturnLabel extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'tig_postnl_shipment_label';

    // @codingStandardsIgnoreLine
    protected $columns = [
        'smart_return_label'
    ];

    /**
     * @return array
     */
    public function installSmartReturnLabelColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            'nullable' => false,
            'default'  => 0,
            'comment'  => 'Smart Return Label',
            'after'    => 'return_label',
        ];
    }
}
