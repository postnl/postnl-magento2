<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Setup\V160\Data;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use TIG\PostNL\Setup\AbstractDataInstaller;

// @codingStandardsIgnoreFile
class ConfigurationData extends AbstractDataInstaller
{
    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     */
    // @codingStandardsIgnoreLine
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $configPaths = [
            'tig_postnl/shippingoptions/pakjegemak_active'              => 'tig_postnl/post_offices/pakjegemak_active',
            'tig_postnl/shippingoptions/pakjegemak_express_active'      => 'tig_postnl/post_offices/pakjegemak_express_active',
            'tig_postnl/shippingoptions/pakjegemak_express_fee'         => 'tig_postnl/post_offices/pakjegemak_express_fee',
            'tig_postnl/productoptions/default_pakjegemak_option'       => 'tig_postnl/post_offices/default_pakjegemak_option',
            'tig_postnl/productoptions/default_pakjegemak_early_option' => 'tig_postnl/post_offices/default_pakjegemak_early_option',
            'tig_postnl/shippingoptions/deliverydays_active'            => 'tig_postnl/delivery_days/deliverydays_active',
            'tig_postnl/shippingoptions/max_deliverydays'               => 'tig_postnl/delivery_days/max_deliverydays',
            'tig_postnl/shippingoptions/stockoptions'                   => 'tig_postnl/stock_settings/stockoptions',
            'tig_postnl/shippingoptions/extraathome_active'             => 'tig_postnl/extra_at_home/extraathome_active',
            'tig_postnl/productoptions/default_extraathome_option'      => 'tig_postnl/extra_at_home/default_extraathome_option',
            'tig_postnl/webshop_track_and_trace/email_enabled'          => 'tig_postnl/track_and_trace/email_enabled',
            'tig_postnl/webshop_track_and_trace/template'               => 'tig_postnl/track_and_trace/template',
            'tig_postnl/webshop_track_and_trace/email_bcc'              => 'tig_postnl/track_and_trace/email_bcc',
            'tig_postnl/shippingoptions/idcheck_active'                 => 'tig_postnl/id_check/idcheck_active',
            'tig_postnl/shippingoptions/sundaydelivery_active'          => 'tig_postnl/sunday_delivery/sundaydelivery_active',
            'tig_postnl/shippingoptions/sundaydelivery_fee'             => 'tig_postnl/sunday_delivery/sundaydelivery_fee',
            'tig_postnl/shippingoptions/eveningdelivery_active'         => 'tig_postnl/evening_delivery_nl/eveningdelivery_active',
            'tig_postnl/shippingoptions/eveningdelivery_fee'            => 'tig_postnl/evening_delivery_nl/eveningdelivery_fee',
            'tig_postnl/productoptions/default_evening_option'          => 'tig_postnl/evening_delivery_nl/default_evening_option',
            'tig_postnl/shippingoptions/eveningdelivery_be_active'      => 'tig_postnl/evening_delivery_be/eveningdelivery_be_active',
            'tig_postnl/shippingoptions/eveningdelivery_be_fee'         => 'tig_postnl/evening_delivery_be/eveningdelivery_be_fee',
            'tig_postnl/productoptions/default_evening_be_option'       => 'tig_postnl/evening_delivery_be/default_evening_be_option',
            'tig_postnl/generalconfiguration_logging/types'             => 'tig_postnl/developer_settings/types',
            'tig_postnl/webshop_printer/label_size'                     => 'tig_postnl/extra_settings_printer/label_size',
            'tig_postnl/webshop_advanced/allowed_shipping_methods'      => 'tig_postnl/extra_settings_advanced/allowed_shipping_methods',
            'tig_postnl/webshop_shipping/shipping_duration'             => 'tig_postnl/postnl_settings/shipping_duration',
            'tig_postnl/webshop_shipping/cutoff_time'                   => 'tig_postnl/postnl_settings/cutoff_time',
            'tig_postnl/webshop_shipping/saturday_cutoff_time'          => 'tig_postnl/postnl_settings/saturday_cutoff_time',
            'tig_postnl/webshop_shipping/sunday_cutoff_time'            => 'tig_postnl/postnl_settings/sunday_cutoff_time',
            'tig_postnl/webshop_shipping/shipment_days'                 => 'tig_postnl/postnl_settings/shipment_days',
            'tig_postnl/shippingoptions/shippingoptions_active'         => 'tig_postnl/delivery_settings/shippingoptions_active',
            'tig_postnl/productoptions/default_option'                  => 'tig_postnl/delivery_settings/default_option',
            'tig_postnl/productoptions/use_alternative_default'         => 'tig_postnl/delivery_settings/use_alternative_default',
            'tig_postnl/productoptions/alternative_default_min_amount'  => 'tig_postnl/delivery_settings/alternative_default_min_amount',
            'tig_postnl/productoptions/alternative_default_option'      => 'tig_postnl/delivery_settings/alternative_default_option',
            'tig_postnl/productoptions/supported_options'               => 'tig_postnl/delivery_settings/supported_options'
        ];

        $table = $setup->getTable('core_config_data');
        foreach ($configPaths as $oldPath => $newPath) {
            $setup->getConnection()->update(
                $table, ['path' => $newPath],
                ["path = '$oldPath'"]
            );
        }
    }
}
