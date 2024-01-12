<?php

namespace TIG\PostNL\Setup\V182\Schema;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class ModifyConditionNameColumn implements InstallSchemaInterface
{
    const TABLE_NAME = 'tig_postnl_tablerate';

    // @codingStandardsIgnoreLine
    protected $columns = [
        'condition_name'
    ];

    // @codingStandardsIgnoreLine
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();
        $connection->changeColumn(
            $setup->getTable(self::TABLE_NAME),
            'condition_name',
            'condition_name',
            [
                'type'   => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 30
            ]
        );
    }
}
