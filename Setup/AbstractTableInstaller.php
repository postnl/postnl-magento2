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

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codingStandardsIgnoreStart
 */
abstract class AbstractTableInstaller implements InstallSchemaInterface
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
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $this->setup = $setup;

        if (!$installer->tableExists(static::TABLE_NAME)) {
            $this->createTable();
            $this->defineTable();
            $this->saveTable();
        }
    }

    /**
     * @return Table
     */
    // @codingStandardsIgnoreLine
    protected function createTable()
    {
        $connection = $this->setup->getConnection();
        $this->table = $connection->newTable($this->setup->getTable(static::TABLE_NAME));

        return $this->table;
    }

    /**
     * @return void
     */
    // @codingStandardsIgnoreLine
    abstract protected function defineTable();

    /**
     * @throws \Zend_Db_Exception
     */
    // @codingStandardsIgnoreLine
    protected function addEntityId()
    {
        $this->table->addColumn(
            'entity_id',
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => true,
                'unsigned' => true,
                'nullable' => false,
                'primary' => true
            ],
            'Entity ID'
        );
    }

    /**
     * @param      $name
     * @param      $comment
     * @param bool $nullable
     * @param null $default
     *
     * @throws \Zend_Db_Exception
     */
    // @codingStandardsIgnoreLine
    protected function addDate($name, $comment, $nullable = true, $default = null)
    {
        $this->table->addColumn(
            $name,
            Table::TYPE_DATE,
            null,
            [
                'identity' => false,
                'unsigned' => false,
                'nullable' => $nullable,
                'primary' => false,
                'default' => $default,
            ],
            $comment
        );
    }

    /**
     * @param      $name
     * @param      $comment
     * @param bool $nullable
     * @param null $default
     *
     * @throws \Zend_Db_Exception
     */
    // @codingStandardsIgnoreLine
    protected function addTimestamp($name, $comment, $nullable = true, $default = null)
    {
        $this->table->addColumn(
            $name,
            Table::TYPE_TIMESTAMP,
            null,
            [
                'identity' => false,
                'unsigned' => false,
                'nullable' => $nullable,
                'primary' => false,
                'default' => $default,
            ],
            $comment
        );
    }

    /**
     * @param      $name
     * @param      $comment
     * @param bool $nullable
     * @param bool $unsigned
     * @param null $default
     *
     * @throws \Zend_Db_Exception
     */
    // @codingStandardsIgnoreLine
    protected function addInt($name, $comment, $nullable = true, $unsigned = false, $default = null)
    {
        $this->table->addColumn(
            $name,
            Table::TYPE_INTEGER,
            null,
            [
                'identity' => false,
                'unsigned' => $unsigned,
                'nullable' => $nullable,
                'primary' => false,
                'default' => $default,
            ],
            $comment
        );
    }

    /**
     * @param        $ref_table
     * @param        $ref_table_field
     * @param        $table
     * @param        $table_field
     * @param string $onDelete
     */
    // @codingStandardsIgnoreLine
    protected function addForeignKey(
        $ref_table,
        $ref_table_field,
        $table,
        $table_field,
        $onDelete = Table::ACTION_CASCADE
    ) {
        $this->table->addForeignKey(
            $this->setup->getFkName($table, $table_field, $ref_table, $ref_table_field),
            $table_field,
            $this->setup->getTable($ref_table),
            $ref_table_field,
            $onDelete
        );
    }

    /**
     * @param      $name
     * @param      $comment
     * @param int  $length
     * @param bool $nullable
     * @param null $default
     *
     * @throws \Zend_Db_Exception
     */
    // @codingStandardsIgnoreLine
    protected function addText($name, $comment, $length = 255, $nullable = true, $default = null)
    {
        $this->table->addColumn(
            $name,
            Table::TYPE_TEXT,
            $length,
            [
                'identity' => false,
                'unsigned' => false,
                'nullable' => $nullable,
                'primary' => false,
                'default' => $default,
            ],
            $comment
        );
    }

    /**
     * @param      $name
     * @param      $comment
     * @param int  $length
     * @param bool $nullable
     * @param null $default
     *
     * @throws \Zend_Db_Exception
     */
    // @codingStandardsIgnoreLine
    protected function addBlob($name, $comment, $nullable = true, $default = null)
    {
        $this->table->addColumn(
            $name,
            Table::TYPE_BLOB,
            null,
            [
                'identity' => false,
                'unsigned' => false,
                'nullable' => $nullable,
                'primary' => false,
                'default' => $default,
            ],
            $comment
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
}
/**
 * @codingStandardsIgnoreEnd
 */
