<?php

namespace TIG\PostNL\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var InstallSchemaInterface[]
     */
    private array $installSchemaObjects;

    public function __construct(
        \TIG\PostNL\Setup\V110\Schema\InstallShipmentTable $installShipmentTable,
        \TIG\PostNL\Setup\V110\Schema\InstallOrderTable $installOrderTable,
        \TIG\PostNL\Setup\V110\Schema\InstallShipmentLabelTable $installShipmentLabelTable,
        \TIG\PostNL\Setup\V110\Schema\InstallShipmentBarcodeTable $installShipmentBarcodeTable,
        \TIG\PostNL\Setup\V110\Schema\InstallTablerateTable $installTablerateTable,
        \TIG\PostNL\Setup\V110\Schema\SalesShipmentGridColumns $salesShipmentGridColumns,
        \TIG\PostNL\Setup\V110\Schema\SalesOrderGridColumns $salesOrderGridColumns
    ) {
        $this->installSchemaObjects = [
            $installShipmentTable,
            $installOrderTable,
            $installShipmentLabelTable,
            $installShipmentBarcodeTable,
            $installTablerateTable,
            $salesShipmentGridColumns,
            $salesOrderGridColumns
        ];
    }

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->installSchemas($this->installSchemaObjects, $setup, $context);
        }

        $setup->endSetup();
    }

    /**
     * @param InstallSchemaInterface[] $schemaObjects
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function installSchemas(array $schemaObjects, SchemaSetupInterface $setup, ModuleContextInterface $context): void
    {
        /** @var AbstractTableInstaller|AbstractColumnsInstaller $schema */
        foreach ($schemaObjects as $schema) {
            $schema->install($setup, $context);
        }
    }
}
