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
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

abstract class AbstractColumnsInstaller implements InstallSchemaInterface
{
    const TABLE_NAME = null;

    /**
     * @var Table
     */
    // @codingStandardsIgnoreLine
    protected $table;

    /**
     * @var SchemaSetupInterface
     */
    // @codingStandardsIgnoreLine
    protected $setup;

    /**
     * @var array
     */
    // @codingStandardsIgnoreLine
    protected $columns = [];

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var null
     */
    protected $columnsList = null;

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @throws LocalizedException
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->setup = $setup;
        $this->connection = $setup->getConnection();
        $this->table = $setup->getTable(static::TABLE_NAME);

        if (!$setup->tableExists(static::TABLE_NAME)) {
            throw new LocalizedException(__('Table %1 does not exists', static::TABLE_NAME));
        }

        foreach ($this->columns as $column) {
            $this->installColumn($column);
        }
    }

    // @codingStandardsIgnoreLine
    protected function installColumn($columnName)
    {
        if ($this->columnExists($columnName)) {
            return;
        }

        $methodName = $this->getMethodName($columnName);

        /** @var array $arguments */
        $arguments = $this->$methodName();

        $this->setup->getConnection()->addColumn(
            $this->table,
            $columnName,
            $arguments
        );
    }

    /**
     * @return \Zend_Db_Statement_Interface
     */
    // @codingStandardsIgnoreLine
    protected function saveTable()
    {
        $connection = $this->setup->getConnection();

        return $connection->createTable($this->table);
    }

    /**
     * @param $columnName
     *
     * @return bool
     */
    // @codingStandardsIgnoreLine
    protected function columnExists($columnName)
    {
        if ($this->columnsList === null) {
            $this->columnsList = $this->connection->describeTable(static::TABLE_NAME);
        }

        return array_key_exists($columnName, $this->columnsList);
    }

    /**
     * Converts this_column_name to installThisColumnNameColumn
     *
     * @param $columnName
     *
     * @return mixed|string
     */
    protected function getMethodName($columnName)
    {
        $methodName = str_replace('_', ' ', $columnName);
        $methodName = ucwords($methodName);
        $methodName = str_replace(' ', '', $methodName);
        $methodName = 'install' . $methodName . 'Column';

        return $methodName;
    }
}
