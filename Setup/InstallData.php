<?php

namespace TIG\PostNL\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var array
     */
    private $installDataObjects;

    /**
     * @param array $installDataObjects
     */
    public function __construct(
        $installDataObjects = []
    ) {
        $this->installDataObjects = $installDataObjects;
    }

    /**
     * Installs data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @return void
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        foreach ($this->installDataObjects as $version) {
            $this->installData($version, $setup, $context);
        }

        $setup->endSetup();
    }

    /**
     * @param \TIG\PostNL\Setup\AbstractDataInstaller[] $installSchemaObjects
     * @param ModuleDataSetupInterface                  $setup
     * @param ModuleContextInterface                    $context
     */
    private function installData(
        array $installSchemaObjects,
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        foreach ($installSchemaObjects as $installer) {
            $installer->install($setup, $context);
        }
    }
}
