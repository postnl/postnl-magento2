<?php
namespace TIG\PostNL\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;


class AddShippingLabelFileFormat implements SchemaPatchInterface
{
    const TABLE_NAME = 'tig_postnl_shipment_label';

    /** @var ModuleDataSetupInterface */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }


    public static function getDependencies()
    {
        return [];
    }


    public function getAliases()
    {
        return [];
    }


    public function apply()
    {
        $this->moduleDataSetup->startSetup();

        $this->moduleDataSetup->getConnection()->addColumn(
            $this->moduleDataSetup->getTable(self::TABLE_NAME),
            'label_file_type',
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => false,
                'length' => 5,
                'comment' => 'Label file format',
                'default' => 'PDF',
            ]
        );

        $this->moduleDataSetup->endSetup();
    }
}
