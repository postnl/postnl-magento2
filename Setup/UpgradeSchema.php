<?php

namespace TIG\PostNL\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

// @codingStandardsIgnoreFile
class UpgradeSchema implements UpgradeSchemaInterface
{
    private $upgradeSchemaObjects;

    public function __construct(
        $upgradeSchemaObjects = []
    ) {
        $this->upgradeSchemaObjects = $upgradeSchemaObjects;
    }

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->upgradeSchemas($this->upgradeSchemaObjects['v1.2.0'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.3.0', '<')) {
            $this->upgradeSchemas($this->upgradeSchemaObjects['v1.3.0'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.3.1', '<')) {
            $this->upgradeSchemas($this->upgradeSchemaObjects['v1.3.1'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.4.0', '<')) {
            $this->upgradeSchemas($this->upgradeSchemaObjects['v1.4.0'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.4.1', '<')) {
            $this->upgradeSchemas($this->upgradeSchemaObjects['v1.4.1'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.5.2', '<')) {
            $this->upgradeSchemas($this->upgradeSchemaObjects['v1.5.2'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.5.3', '<')) {
            $this->upgradeSchemas($this->upgradeSchemaObjects['v1.5.3'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.6.1', '<')) {
            $this->upgradeSchemas($this->upgradeSchemaObjects['v1.6.1'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.7.4', '<')) {
            $this->upgradeSchemas($this->upgradeSchemaObjects['v1.7.4'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.8.0', '<')) {
            $this->upgradeSchemas($this->upgradeSchemaObjects['v1.8.0'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.8.2', '<')) {
            $this->upgradeSchemas($this->upgradeSchemaObjects['v1.8.2'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.9.0', '<')) {
            $this->upgradeSchemas($this->upgradeSchemaObjects['v1.9.0'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.9.1', '<')) {
            $this->upgradeSchemas($this->upgradeSchemaObjects['v1.9.1'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.12.5', '<')) {
            $this->upgradeSchemas($this->upgradeSchemaObjects['v1.12.5'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.12.7', '<')) {
            $this->upgradeSchemas($this->upgradeSchemaObjects['v1.12.7'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.13.0', '<')) {
            $this->upgradeSchemas($this->upgradeSchemaObjects['v1.13.0'], $setup, $context);
        }

        $setup->endSetup();
    }

    /**
     * @param $schemaObjects
     * @param $setup
     * @param $context
     */
    private function upgradeSchemas($schemaObjects, $setup, $context)
    {
        /** @var AbstractColumnsInstaller $schema */
        foreach ($schemaObjects as $schema) {
            $schema->install($setup, $context);
        }
    }
}
