<?php

namespace TIG\PostNL\Setup\V181\Data;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use TIG\PostNL\Setup\AbstractDataInstaller;
use TIG\PostNL\Config\Provider\ShippingOptions;

// @codingStandardsIgnoreFile
class UpdateConfigData extends AbstractDataInstaller
{
    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    // @codingStandardsIgnoreLine
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $table = $setup->getTable('core_config_data');
        $setup->getConnection()->delete($table, 'path = \'' . ShippingOptions::XPATH_SHIPPING_OPTION_EVENING_BE_ACTIVE . '\'');
        $setup->getConnection()->delete($table, 'path = \'' . ShippingOptions::XPATH_SHIPPING_OPTION_EVENING_BE_FEE . '\'');
    }
}
