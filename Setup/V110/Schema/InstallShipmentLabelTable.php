<?php

namespace TIG\PostNL\Setup\V110\Schema;

use TIG\PostNL\Setup\AbstractTableInstaller;

class InstallShipmentLabelTable extends AbstractTableInstaller
{
    const TABLE_NAME = 'tig_postnl_shipment_label';

    /**
     * @return void
     */
    // @codingStandardsIgnoreLine
    protected function defineTable()
    {
        $this->addEntityId();

        $this->addInt('parent_id', 'Parent ID', true, true);
        $this->addForeignKey('tig_postnl_shipment', 'entity_id', static::TABLE_NAME, 'parent_id');

        $this->addInt('number', 'Number');
        $this->addBlob('label', 'Label');
        $this->addText('type', 'Label Type', 32);
    }
}
