<?php

namespace TIG\PostNL\Setup\V110\Schema;

use TIG\PostNL\Setup\AbstractTableInstaller;

class InstallShipmentTable extends AbstractTableInstaller
{
    const TABLE_NAME = 'tig_postnl_shipment';

    /**
     * @return void
     */
    // @codingStandardsIgnoreLine
    protected function defineTable()
    {
        $this->addEntityId();

        $this->addInt('shipment_id', 'Shipment ID', true, true);
        $this->addForeignKey('sales_shipment', 'entity_id', static::TABLE_NAME, 'shipment_id');

        $this->addInt('order_id', 'Order ID', true, true);
        $this->addForeignKey('sales_order', 'entity_id', static::TABLE_NAME, 'order_id');

        $this->addText('main_barcode', 'Main Barcode', 32);
        $this->addText('product_code', 'Product Code', 32);
        $this->addText('shipment_type', 'Shipment Type', 32);

        $this->addTimestamp('delivery_date', 'Delivery date');
        $this->addText('expected_delivery_time_start', 'Expected delivery time start', 16);
        $this->addText('expected_delivery_time_end', 'Expected delivery time end', 16);

        $this->addText('is_pakjegemak', 'Is Pakjegemak', 1);
        $this->addText('pg_location_code', 'PakjeGemak Location Code', 16);
        $this->addText('pg_retail_network_id', 'PakjeGemak Retail Netwerok ID', 16);

        $this->addInt('parcel_count', 'Parcel Count', true, true, 1);

        $this->addDate('ship_at', 'Ship the parcel at');
        $this->addTimestamp('confirmed_at', 'Confirmed at');
        $this->addTimestamp('created_at', 'Created at');
        $this->addTimestamp('updated_at', 'Updated at');
    }
}
