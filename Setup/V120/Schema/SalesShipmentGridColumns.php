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
namespace TIG\PostNL\Setup\V120\Schema;

use TIG\PostNL\Setup\AbstractColumnsInstaller;

class SalesShipmentGridColumns extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'sales_shipment_grid';

    // @codingStandardsIgnoreLine
    protected $columns = [
        'tig_postnl_product_code',
    ];

    /**
     * @return array
     */
    public function installTigPostnlProductCodeColumn()
    {
        return [
            // @codingStandardsIgnoreLine
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length' => 11,
            'nullable' => true,
            'default' => null,
            'comment' => 'PostNL product code',
            'after' => 'tig_postnl_ship_at',
        ];
    }
}
