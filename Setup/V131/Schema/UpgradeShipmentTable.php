<?php

namespace TIG\PostNL\Setup\V131\Schema;

use \TIG\PostNL\Setup\AbstractColumnsInstaller;

class UpgradeShipmentTable extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'tig_postnl_shipment';

    // @codingStandardsIgnoreLine
    protected $columns = [
        'ac_characteristic',
        'ac_option'
    ];

    /**
     * Installs the Location AgentCode Characteristic Column
     * @return array
     */
    public function installAcCharacteristicColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'   => 3,
            'nullable' => true,
            'default'  => null,
            'comment'  => 'AC Characteristic',
            'after'    => 'shipment_type',
        ];
    }

    /**
     * Installs the Location AgentCode Option Column
     * @return array
     */
    public function installAcOptionColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'   => 3,
            'nullable' => true,
            'default'  => null,
            'comment'  => 'AC Option',
            'after'    => 'ac_characteristic',
        ];
    }
}
