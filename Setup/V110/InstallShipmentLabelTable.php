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
namespace TIG\PostNL\Setup\V110;

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

        $this->addBlob('label', 'Label');
        $this->addText('type', 'Label Type', 32);
    }
}
