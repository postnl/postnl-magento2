<?php

namespace TIG\PostNL\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var array
     */
    private $installSchemaObjects;

    /**
     * @param array $installSchemaObjects
     */
    public function __construct(
        $installSchemaObjects = []
    ) {
        $this->installSchemaObjects = $installSchemaObjects;
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
            $this->installSchemas($this->installSchemaObjects['v1.1.0'], $setup, $context);
        }

        $setup->endSetup();
    }

    /**
     * @param $schemaObjects
     * @param $setup
     * @param $context
     */
    private function installSchemas($schemaObjects, $setup, $context)
    {
        /** @var AbstractTableInstaller|AbstractColumnsInstaller $schema */
        foreach ($schemaObjects as $schema) {
            $schema->install($setup, $context);
        }
    }
}
