<?php

namespace TIG\PostNL\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
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
    protected $table;

    /**
     * @var SchemaSetupInterface
     */
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
    protected function createTable()
    {
        $connection = $this->setup->getConnection();
        $this->table = $connection->newTable($this->setup->getTable(static::TABLE_NAME));

        return $this->table;
    }

    /**
     * @return void
     */
    abstract protected function defineTable();

    /**
     * @throws \Zend_Db_Exception
     */
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
     * @param array  $fields
     * @param string $indexType
     *
     * @throws \Zend_Db_Exception
     */
    protected function addIndex($fields, $indexType = AdapterInterface::INDEX_TYPE_UNIQUE)
    {
        $this->table->addIndex(
            $this->setup->getIdxName($this->table->getName(), $fields, $indexType),
            $fields,
            ['type' => $indexType]
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
     * @param bool $nullable
     * @param null $default
     *
     * @throws \Zend_Db_Exception
     */
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
     * @param        $name
     * @param        $comment
     * @param string $size
     * @param bool   $nullable
     * @param null   $default
     *
     * @throws \Zend_Db_Exception
     */
    protected function addDecimal($name, $comment, $size = '15,4', $nullable = true, $default = null)
    {
        $this->table->addColumn(
            $name,
            Table::TYPE_DECIMAL,
            $size,
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
    protected function saveTable()
    {
        $connection = $this->setup->getConnection();

        return $connection->createTable($this->table);
    }
}
/**
 * @codingStandardsIgnoreEnd
 */
