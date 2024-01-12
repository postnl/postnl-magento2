<?php

namespace TIG\PostNL\Setup\V172\Data;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Eav\Setup\EavSetup;
use Magento\Catalog\Model\Product;
use TIG\PostNL\Setup\AbstractDataInstaller;

class UpdateCustomProductAttributes extends AbstractDataInstaller
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * Update product attributes to the eav/attribute
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    // @codingStandardsIgnoreLine
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        $entityType = $eavSetup->getEntityTypeId('catalog_product');

        $eavSetup->updateAttribute(
            $entityType,
            'postnl_parcel_count',
            'default_value',
            0
        );

        $eavSetup->updateAttribute(
            $entityType,
            'postnl_parcel_volume',
            'default_value',
            0
        );
    }
}
