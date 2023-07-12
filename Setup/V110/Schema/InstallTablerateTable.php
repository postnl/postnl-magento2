<?php

namespace TIG\PostNL\Setup\V110\Schema;

use TIG\PostNL\Setup\AbstractTableInstaller;

class InstallTablerateTable extends AbstractTableInstaller
{
    const TABLE_NAME = 'tig_postnl_tablerate';

    /**
     * @return void
     */
    // @codingStandardsIgnoreLine
    protected function defineTable()
    {
        $this->addEntityId();

        $this->addInt('website_id', 'Website ID', false, true, 0);
        $this->addText('dest_country_id', 'Destination coutry ISO/2 or ISO/3 code', 4, false, '0');
        $this->addInt('dest_region_id', 'Destination Region ID', false, true, 0);
        $this->addText('dest_zip', 'Destination Post Code (Zip)', 10, false, '*');
        $this->addText('condition_name', 'Rate Condition name', 20, false);
        $this->addDecimal('condition_value', 'Rate condition value', '12,4', false, '0.0000');
        $this->addDecimal('price', 'Price', '12,4', false, '0.0000');
        $this->addDecimal('cost', 'Cost', '12,4', false, '0.0000');

        $this->addIndex([
            'website_id',
            'dest_country_id',
            'dest_region_id',
            'dest_zip',
            'condition_name',
            'condition_value',
            'price',
            'cost'
        ]);
    }
}
