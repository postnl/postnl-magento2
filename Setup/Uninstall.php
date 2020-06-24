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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
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
namespace TIG\PostNL\Setup;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

// @codingStandardsIgnoreFile
class Uninstall implements UninstallInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * Uninstall constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     */
    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * @inheritDoc
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        echo "Do you also want to remove Order related PostNL data" .
             "(such as used Productcodes, T&T codes, delivery dates)? [y/N]\n";

        while (true) {
            $handle = fopen("php://stdin", "r");
            $line = strtolower(trim(fgets($handle)));
            if ($line == 'yes' || $line == 'y') {
                $this->removeTables($setup);

                break;
            }

            if ($line == 'n' || $line == 'no') {
                break;
            }

            echo "Please insert y or N.\n";
        }

        $this->removeEavAttributes();
        $this->removeConfigSettings($setup);
        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function removeTables($setup)
    {
        echo "Removing tables...\n";
        $connection = $setup->getConnection();
        $connection->dropTable('tig_postnl_order');
        $connection->dropTable('tig_postnl_shipment');
        $connection->dropTable('tig_postnl_shipment_barcode');
        $connection->dropTable('tig_postnl_shipment_label');
        $connection->dropTable('tig_postnl_tablerate');
        $connection->dropTable('tig_postnl_matrixrate');
        echo "Finished removing tables.\n";
    }

    private function removeEavAttributes()
    {
        echo "Removing PostNL settings from catalog...\n";
        $eavSetup = $this->eavSetupFactory->create();
        $eavSetup->removeAttribute(Product::ENTITY, 'postnl_product_type');
        $eavSetup->removeAttribute(Product::ENTITY, 'postnl_parcel_count');
        $eavSetup->removeAttribute(Product::ENTITY, 'postnl_parcel_volume');
        $eavSetup->removeAttribute(Product::ENTITY, 'postnl_shipping_duration');
        $eavSetup->removeAttribute(Product::ENTITY, 'postnl_disable_delivery_days');
        $eavSetup->removeAttribute(Product::ENTITY, 'postnl_max_qty_letterbox');
        echo "Finished removing PostNL settings from catalog.\n";
    }

    /**
     * @param SchemaSetupInterface $setup
     */
    private function removeConfigSettings($setup)
    {
        echo "Removing PostNL configs...\n";
        $connection = $setup->getConnection();
        $connection->delete('core_config_data', "path LIKE '%tig_postnl%'");
        echo "Finished removing PostNL configs.\n";
    }
}
