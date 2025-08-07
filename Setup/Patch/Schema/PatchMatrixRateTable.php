<?php
namespace TIG\PostNL\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use TIG\PostNL\Model\Carrier\Matrixrate;

class PatchMatrixRateTable implements SchemaPatchInterface
{
    const TABLE_NAME = 'tig_postnl_matrixrate';

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

        $this->moduleDataSetup->getConnection()->changeColumn(
            $this->moduleDataSetup->getTable(self::TABLE_NAME),
            Matrixrate::FIELD_DESTINY_COUNTRY_ID,
            Matrixrate::FIELD_DESTINY_COUNTRY_ID,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Destination country ID ISO/2',
                'default' => '',
                'length' => 4,
            ]
        );

        $this->moduleDataSetup->getConnection()->changeColumn(
            $this->moduleDataSetup->getTable(self::TABLE_NAME),
            Matrixrate::FIELD_DESTINY_ZIP_CODE,
            Matrixrate::FIELD_DESTINY_ZIP_CODE,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Destiny ZIP Code',
                'default' => '*',
                'length' => 50,
            ]
        );

        $this->moduleDataSetup->getConnection()->changeColumn(
            $this->moduleDataSetup->getTable(self::TABLE_NAME),
            Matrixrate::FIELD_PARCEL_TYPE,
            Matrixrate::FIELD_PARCEL_TYPE,
            [
                'type' => Table::TYPE_TEXT,
                'nullable' => false,
                'comment' => 'Parcel Type',
                'default' => '*',
                'length' => 50,
            ]
        );

        $this->moduleDataSetup->endSetup();
    }
}
