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
        $this->addText('condition_name', 'Rate Condition name', 30, false);
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
