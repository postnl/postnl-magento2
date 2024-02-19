<?php

namespace TIG\PostNL\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

// @codingStandardsIgnoreFile
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var array
     */
    private $upgradeDataObjects;

    /**
     * @param array $upgradeDataObjects
     */
    public function __construct(
        $upgradeDataObjects = []
    ) {
        $this->upgradeDataObjects = $upgradeDataObjects;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->upgradeData($this->upgradeDataObjects['v1.2.0'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.4.1', '<')) {
            $this->upgradeData($this->upgradeDataObjects['v1.4.1'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.6.0', '<')) {
            $this->upgradeData($this->upgradeDataObjects['v1.6.0'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.7.2', '<')) {
            $this->upgradeData($this->upgradeDataObjects['v1.7.2'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.8.1', '<')) {
            $this->upgradeData($this->upgradeDataObjects['v1.8.1'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.9.1', '<')) {
            $this->upgradeData($this->upgradeDataObjects['v1.9.1'], $setup, $context);
        }

        if (version_compare($context->getVersion(), '1.9.4', '<')) {
            $this->upgradeData($this->upgradeDataObjects['v1.9.4'], $setup, $context);
        }

        $setup->endSetup();
    }

    /**
     * @param \TIG\PostNL\Setup\AbstractDataInstaller[] $installSchemaObjects
     * @param ModuleDataSetupInterface                  $setup
     * @param ModuleContextInterface                    $context
     */
    private function upgradeData(
        array $installSchemaObjects,
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        foreach ($installSchemaObjects as $installer) {
            $installer->install($setup, $context);
        }
    }
}
