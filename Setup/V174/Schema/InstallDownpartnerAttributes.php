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

namespace TIG\PostNL\Setup\V174\Schema;

use \TIG\PostNL\Setup\AbstractColumnsInstaller;

class InstallDownpartnerAttributes extends AbstractColumnsInstaller
{
    const TABLE_NAME = 'tig_postnl_shipment';
    
    // @codingStandardsIgnoreLine
    protected $columns = [
        'downpartner_id',
        'downpartner_location',
        'downpartner_barcode',
    ];
    
    public function installDownpartnerIdColumn()
    {
        return [
            'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'   => 32,
            'nullable' => true,
            'default'  => null,
            'comment'  => 'Downpartner ID',
            'after'    => 'confirmed'
        ];
    }
    
    public function installDownpartnerLocationColumn()
    {
        return [
            'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'   => 16,
            'nullable' => true,
            'default'  => null,
            'comment'  => 'Downpartner Location',
            'after'    => 'downpartner_id'
        ];
    }
    
    public function installDownpartnerBarcodeColumn()
    {
        return [
            'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'   => 32,
            'nullable' => true,
            'default'  => null,
            'comment'  => 'Downpartner Barcode',
            'after'    => 'downpartner_location'
        ];
    }
}
