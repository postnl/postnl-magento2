<?php

namespace TIG\PostNL\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

// @codingStandardsIgnoreFile
class UpgradeData implements UpgradeDataInterface
{
    private array $upgradeDataObjects;

    public function __construct(
        \TIG\PostNL\Setup\V120\Data\CustomProductAttributes $customProductAttributes,
        \TIG\PostNL\Setup\V141\Data\ShippingDurationAttribute $shippingDurationAttribute,
        \TIG\PostNL\Setup\V160\Data\ConfigurationData $configurationData,
        \TIG\PostNL\Setup\V172\Data\UpdateCustomProductAttributes $updateCustomProductAttributes,
        \TIG\PostNL\Setup\V181\Data\UpdateConfigData $updateConfigData,
        \TIG\PostNL\Setup\V191\Data\InstallDisableDeliveryDaysAttribute $installDisableDeliveryDaysAttribute,
        \TIG\PostNL\Setup\V194\Data\InstallMaximumQuantityLetterboxPackage $installMaximumQuantityLetterboxPackage,
        \TIG\PostNL\Setup\V1180\Data\InstallLetterboxPackages $installLetterboxPackages
    ) {
        $this->upgradeDataObjects = [
            'v1.2.0' => [$customProductAttributes],
            'v1.4.1' => [$shippingDurationAttribute],
            'v1.6.0' => [$configurationData],
            'v1.7.2' => [$updateCustomProductAttributes],
            'v1.8.1' => [$updateConfigData],
            'v1.9.1' => [$installDisableDeliveryDaysAttribute],
            'v1.9.4' => [$installMaximumQuantityLetterboxPackage],
            'v1.18.0' => [$installLetterboxPackages]
        ];
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

        if (version_compare($context->getVersion(), '1.18.0', '<')) {
            $this->upgradeData($this->upgradeDataObjects['v1.18.0'], $setup, $context);
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
