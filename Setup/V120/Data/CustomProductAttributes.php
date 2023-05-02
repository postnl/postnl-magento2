<?php

namespace TIG\PostNL\Setup\V120\Data;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use TIG\PostNL\Config\Provider\ProductType;
use TIG\PostNL\Setup\AbstractDataInstaller;

class CustomProductAttributes extends AbstractDataInstaller
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
     * Add product attributes to the eav/attribute
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @return void
     */
    // @codingStandardsIgnoreLine
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /**
         * Product type
         */
        $eavSetup->addAttribute(
            Product::ENTITY,
            'postnl_product_type',
            [
                'group'                   => 'PostNL',
                'type'                    => 'text',
                'backend'                 => '',
                'frontend'                => '',
                'label'                   => 'Type',
                'input'                   => 'select',
                'class'                   => '',
                'source'                  => ProductType::class,
                'global'                  => EavAttribute::SCOPE_GLOBAL,
                'visible'                 => true,
                'required'                => false,
                'user_defined'            => false,
                'default'                 => 'regular',
                'searchable'              => false,
                'filterable'              => false,
                'comparable'              => false,
                'visible_on_front'        => false,
                'used_in_product_listing' => true,
                'unique'                  => false,
                'apply_to'                => 'simple',
            ]
        );

        /**
         * Parcel count
         */
        $eavSetup->addAttribute(
            Product::ENTITY,
            'postnl_parcel_count',
            [
                'group'                   => 'PostNL',
                'type'                    => 'int',
                'backend'                 => '',
                'frontend'                => '',
                'label'                   => 'Parcel count',
                'input'                   => 'text',
                'class'                   => 'validate-greater-than-zero',
                'source'                  => '',
                'global'                  => EavAttribute::SCOPE_GLOBAL,
                'visible'                 => true,
                'required'                => false,
                'user_defined'            => false,
                'default'                 => 1,
                'searchable'              => false,
                'filterable'              => false,
                'comparable'              => false,
                'visible_on_front'        => false,
                'used_in_product_listing' => true,
                'unique'                  => false,
                'apply_to'                => 'simple',
                'note'                    => 'When sending Extra@Home types, this value is used to calculate the ' .
                                             'coli amount. When using other types this value will be ignored.',
            ]
        );

        /**
         * Volume CM3
         */
        $eavSetup->addAttribute(
            Product::ENTITY,
            'postnl_parcel_volume',
            [
                'group'                   => 'PostNL',
                'type'                    => 'int',
                'backend'                 => '',
                'frontend'                => '',
                'label'                   => 'Volume',
                'input'                   => 'text',
                'class'                   => 'validate-greater-than-zero',
                'source'                  => '',
                'global'                  => EavAttribute::SCOPE_GLOBAL,
                'visible'                 => true,
                'required'                => false,
                'user_defined'            => false,
                'default'                 => 1,
                'searchable'              => false,
                'filterable'              => false,
                'comparable'              => false,
                'visible_on_front'        => false,
                'used_in_product_listing' => true,
                'unique'                  => false,
                'apply_to'                => 'simple',
                'note'                    => 'When sending Extra@Home types, this field is mandatory. '.
                                             'Enter as cubic centimeters like 30000.',
            ]
        );
    }
}
