<?php

namespace TIG\PostNL\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

// @codingStandardsIgnoreFile
class UpgradeSchema implements UpgradeSchemaInterface
{
    private array $upgradeSchemaObjects;

    public function __construct(
        \TIG\PostNL\Setup\V120\Schema\SalesShipmentGridColumns $salesShipmentGridColumns,
        \TIG\PostNL\Setup\V120\Schema\SalesOrderGridColumns $salesOrderGridColumns,
        \TIG\PostNL\Setup\V120\Schema\UpgradeOrderTable $upgradeOrderTable,
        \TIG\PostNL\Setup\V120\Schema\InstallMatrixRateTable $installMatrixRateTable,
        \TIG\PostNL\Setup\V130\Schema\UpgradeForeignKeysOrderTable $upgradeForeignKeysOrderTable,
        \TIG\PostNL\Setup\V131\Schema\UpgradeOrderTable $upgradeOrderTable131,
        \TIG\PostNL\Setup\V131\Schema\UpgradeShipmentTable $upgradeShipmentTable131,
        \TIG\PostNL\Setup\V140\Schema\UpgradeShippingLabelType $upgradeShippingLabelType,
        \TIG\PostNL\Setup\V141\Schema\UpgradeOrderTable $upgradeOrderTable141,
        \TIG\PostNL\Setup\V152\Schema\SalesShipmentGridColumns $salesShipmentGridColumns152,
        \TIG\PostNL\Setup\V152\Schema\SalesOrderGridColumns $salesOrderGridColumns152,
        \TIG\PostNL\Setup\V152\Schema\UpgradeOrderTable $upgradeOrderTable152,
        \TIG\PostNL\Setup\V152\Schema\UpgradeShipmentTable $upgradeShipmentTable,
        \TIG\PostNL\Setup\V153\Schema\SalesShipmentGridColumns $salesShipmentGridColumns153,
        \TIG\PostNL\Setup\V161\Schema\UpgradeShipmentLabelTable $upgradeShipmentLabelTable,
        \TIG\PostNL\Setup\V174\Schema\InstallDownpartnerAttributes $installDownpartnerAttributes,
        \TIG\PostNL\Setup\V180\Schema\InstallPriorityAttribute $installPriorityAttribute,
        \TIG\PostNL\Setup\V182\Schema\ModifyConditionNameColumn $modifyConditionNameColumn,
        \TIG\PostNL\Setup\V190\Schema\InstallReturnBarcode $installReturnBarcode,
        \TIG\PostNL\Setup\V190\Schema\InstallReturnLabel $installReturnLabel,
        \TIG\PostNL\Setup\V191\Schema\InstallStatedAddressOnly $installStatedAddressOnly,
        \TIG\PostNL\Setup\V1125\Schema\InstallSmartReturnBarcode $installSmartReturnBarcode,
        \TIG\PostNL\Setup\V1125\Schema\InstallSmartReturnLabel $installSmartReturnLabel,
        \TIG\PostNL\Setup\V1125\Schema\InstallIsSmartReturn $installIsSmartReturn,
        \TIG\PostNL\Setup\V1125\Schema\InstallSmartReturnEmailSent $installSmartReturnEmailSent,
        \TIG\PostNL\Setup\V1127\Schema\InstallOrderInsuredTier $installOrderInsuredTier,
        \TIG\PostNL\Setup\V1127\Schema\InstallShipmentInsuredTier $installShipmentInsuredTier,
        \TIG\PostNL\Setup\V1130\Schema\InstallOrderAcInformation $installOrderAcInformation,
        \TIG\PostNL\Setup\V1130\Schema\InstallShipmentAcInformation $installShipmentAcInformation,
        \TIG\PostNL\Setup\V1140\Schema\InstallReturnStatus $installReturnStatus
    ) {
        $this->upgradeSchemaObjects = [
            'v1.2.0' => [$salesShipmentGridColumns, $salesOrderGridColumns, $upgradeOrderTable, $installMatrixRateTable],
            'v1.3.0' => [$upgradeForeignKeysOrderTable],
            'v1.3.1' => [$upgradeOrderTable131, $upgradeShipmentTable131],
            'v1.4.0' => [$upgradeShippingLabelType],
            'v1.4.1' => [$upgradeOrderTable141],
            'v1.5.2' => [$salesShipmentGridColumns152, $salesOrderGridColumns152, $upgradeOrderTable152, $upgradeShipmentTable],
            'v1.5.3' => [$salesShipmentGridColumns153],
            'v1.6.1' => [$upgradeShipmentLabelTable],
            'v1.7.4' => [$installDownpartnerAttributes],
            'v1.8.0' => [$installPriorityAttribute],
            'v1.8.2' => [$modifyConditionNameColumn],
            'v1.9.0' => [$installReturnBarcode, $installReturnLabel],
            'v1.9.1' => [$installStatedAddressOnly],
            'v1.12.5' => [$installSmartReturnBarcode, $installSmartReturnLabel, $installIsSmartReturn, $installSmartReturnEmailSent],
            'v1.12.7' => [$installOrderInsuredTier, $installShipmentInsuredTier],
            'v1.13.0' => [$installOrderAcInformation, $installShipmentAcInformation],
            'v1.14.0' => [$installReturnStatus]
        ];
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

        if (version_compare($context->getVersion(), '1.14.0', '<')) {
            $this->upgradeSchemas($this->upgradeSchemaObjects['v1.14.0'], $setup, $context);
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
