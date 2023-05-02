<?php

namespace TIG\PostNL\Setup\V110\Schema;

use TIG\PostNL\Setup\AbstractTableInstaller;

class InstallShipmentBarcodeTable extends AbstractTableInstaller
{
    const TABLE_NAME = 'tig_postnl_shipment_barcode';

    /**
     * @return void
     */
    // @codingStandardsIgnoreLine
    protected function defineTable()
    {
        $this->addEntityId();

        $this->addInt('parent_id', 'Parent ID', true, true);
        $this->addForeignKey('tig_postnl_shipment', 'entity_id', static::TABLE_NAME, 'parent_id');

        $this->addText('type', 'Barcode Type', 16);
        $this->addInt('number', 'Barcode Number');
        $this->addText('value', 'Barcode', 32);
    }
}
