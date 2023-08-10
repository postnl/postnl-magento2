<?php
namespace TIG\PostNL\Setup\V1130\Schema;

use TIG\PostNL\Setup\AbstractColumnsInstaller;

class InstallShipmentAcInformation extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'tig_postnl_shipment';

    // @codingStandardsIgnoreLine
    protected $columns = [
        'ac_information'
    ];

    /**
     * @return array
     */
    public function installAcInformationColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'nullable' => true,
            'default'  => null,
            'comment'  => 'AC Information',
            'after'    => 'ac_option',
        ];
    }
}
