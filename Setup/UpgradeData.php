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

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var array
     */
    private $upgradeDataObjects;

    /**
     * @param array $upgradeDataObjects
     */
    public function __construct(
        $upgradeDataObjects = []
    ) {
        $this->upgradeDataObjects = $upgradeDataObjects;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.2.0', '<')) {
            $this->upgradeData($this->upgradeDataObjects['v1.2.0'], $setup, $context);
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
