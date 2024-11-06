<?php

namespace TIG\PostNL\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    private array $installDataObjects;

    public function __construct(
        \TIG\PostNL\Setup\V120\Data\CustomProductAttributes $customProductAttributes,
        \TIG\PostNL\Setup\V141\Data\ShippingDurationAttribute $shippingDurationAttribute,
        \TIG\PostNL\Setup\V160\Data\ConfigurationData $configurationData
    ) {
        $this->installDataObjects = [
            $customProductAttributes,
            $shippingDurationAttribute,
            $configurationData
        ];
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

        foreach ($this->installDataObjects as $installer) {
            $installer->install($setup, $context);
        }

        $setup->endSetup();
    }
}
