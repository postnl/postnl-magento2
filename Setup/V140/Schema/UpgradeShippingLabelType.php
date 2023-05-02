<?php

namespace TIG\PostNL\Setup\V140\Schema;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeShippingLabelType implements UpgradeSchemaInterface
{
    const TABLE_NAME = 'tig_postnl_shipment_label';

    /**
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     */
    // @codingStandardsIgnoreLine
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->upgrade($setup, $context);
    }

    /**
     * The default blob type of sql is not big enough to save the Globalpack labels, there for we upgrade it to medium
     * blob => 16777215 bytes
     *
     * {@inheritdoc}
     */
    // @codingStandardsIgnoreLine
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $connection = $setup->getConnection();
        $connection->modifyColumn(
            $setup->getTable(static::TABLE_NAME),
            'label',
            'mediumblob'
        );

        $setup->endSetup();
    }
}
