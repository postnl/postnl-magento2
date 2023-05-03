<?php

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
    // @codingStandardsIgnoreLine
    protected $connection;

    /**
     * @var null
     */
    // @codingStandardsIgnoreLine
    protected $columnsList = null;

    /**
     * Installs DB schema for a module
     *
     * @param SchemaSetupInterface   $setup
     * @param ModuleContextInterface $context
     *
     * @throws LocalizedException
     */
    // @codingStandardsIgnoreLine
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->setup = $setup;
        $this->connection = $setup->getConnection();
        $this->table = $setup->getTable(static::TABLE_NAME);

        if (!$setup->tableExists($this->table)) {
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

        $connection = $this->setup->getConnection();
        $connection->addColumn(
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
            $this->columnsList = $this->connection->describeTable($this->table);
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
    // @codingStandardsIgnoreLine
    protected function getMethodName($columnName)
    {
        $methodName = str_replace('_', ' ', $columnName);
        $methodName = ucwords($methodName);
        $methodName = str_replace(' ', '', $methodName);
        $methodName = 'install' . $methodName . 'Column';

        return $methodName;
    }
}
