<?php

namespace TIG\PostNL\Setup\V110\Schema;

use Magento\Framework\DB\Ddl\Table;
use TIG\PostNL\Setup\AbstractTableInstaller;

class InstallOrderTable extends AbstractTableInstaller
{
    const TABLE_NAME = 'tig_postnl_order';

    /**
     * @return void
     * @codingStandardsIgnoreLine
     */
    // @codingStandardsIgnoreLine
    protected function defineTable()
    {
        $this->addEntityId();
        $this->addInt('order_id', 'Order ID', true, true);
        $this->addForeignKey('sales_order', 'entity_id', static::TABLE_NAME, 'order_id');
        $this->addInt('quote_id', 'Quote ID', true, true);
        $this->addForeignKey('quote', 'entity_id', static::TABLE_NAME, 'quote_id', Table::ACTION_SET_NULL);
        $this->addText('type', 'Type', 32);
        $this->addTimestamp('delivery_date', 'Delivery date');
        $this->addText('expected_delivery_time_start', 'Expected delivery time start', 16);
        $this->addText('expected_delivery_time_end', 'Expected delivery time end', 16);
        $this->addText('is_pakjegemak', 'Is Pakjegemak', 1);
        $this->addInt('pg_order_address_id', 'Pakjegemak Order Address ID', true, true);
        $this->addText('pg_location_code', 'PakjeGemak Location Code', 32);
        $this->addText('pg_retail_network_id', 'PakjeGemak Retail Netwerok ID', 32);
        $this->addInt('product_code', 'Product code', true);
        $this->addDecimal('fee', 'The fee that is calculated', '15,4');

        $this->addDate('ship_at', 'Ship at');
        $this->addTimestamp('confirmed_at', 'Confirmed at');
        $this->addTimestamp('created_at', 'Created at');
        $this->addTimestamp('updated_at', 'Updated at');
    }
}
