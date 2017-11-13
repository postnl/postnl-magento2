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
namespace TIG\PostNL\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var array
     */
    private $installDataObjects;

    /**
     * @param array $installDataObjects
     */
    public function __construct(
        $installDataObjects = []
    ) {
        $this->installDataObjects = $installDataObjects;
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

        foreach ($this->installDataObjects as $version) {
            $this->installData($version, $setup, $context);
        }

        $setup->endSetup();
    }

    /**
     * @param \TIG\PostNL\Setup\AbstractDataInstaller[] $installSchemaObjects
     * @param ModuleDataSetupInterface                  $setup
     * @param ModuleContextInterface                    $context
     */
    private function installData(
        array $installSchemaObjects,
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        foreach ($installSchemaObjects as $installer) {
            $installer->install($setup, $context);
        }
    }
}
