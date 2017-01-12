<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
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
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use TIG\PostNL\Setup\V110\InstallOrderTable;
use TIG\PostNL\Setup\V110\InstallShipmentBarcodeTable;
use TIG\PostNL\Setup\V110\InstallShipmentTable;
use TIG\PostNL\Setup\V110\SalesShipmentGridColumns;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var InstallOrderTable
     */
    private $installOrderTable;

    /**
     * @var InstallShipmentTable
     */
    private $installShipmentTable;

    /**
     * @var InstallShipmentBarcodeTable
     */
    private $installShipmentBarcodeTable;

    /**
     * @var SalesShipmentGridColumns
     */
    private $salesShipmentGridColumns;

    /**
     * @param InstallShipmentTable        $installShipmentTable
     * @param InstallOrderTable           $installOrderTable
     * @param InstallShipmentBarcodeTable $installShipmentBarcodeTable
     * @param SalesShipmentGridColumns    $salesShipmentGridColumns
     */
    public function __construct(
        InstallShipmentTable $installShipmentTable,
        InstallOrderTable $installOrderTable,
        InstallShipmentBarcodeTable $installShipmentBarcodeTable,
        SalesShipmentGridColumns $salesShipmentGridColumns
    ) {
        $this->installShipmentTable = $installShipmentTable;
        $this->installOrderTable = $installOrderTable;
        $this->installShipmentBarcodeTable = $installShipmentBarcodeTable;
        $this->salesShipmentGridColumns = $salesShipmentGridColumns;
    }

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->installOrderTable->install($setup, $context);
            $this->installShipmentTable->install($setup, $context);
            $this->installShipmentBarcodeTable->install($setup, $context);
            $this->salesShipmentGridColumns->install($setup, $context);
        }

        $setup->endSetup();
    }
}
