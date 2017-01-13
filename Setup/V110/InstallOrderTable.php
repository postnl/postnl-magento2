<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Setup\V110;

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
        $this->addForeignKey('quote', 'entity_id', static::TABLE_NAME, 'quote_id');

        $this->addText('type', 'Type', 32);

        $this->addTimestamp('delivery_date', 'Delivery date');
        $this->addText('expected_delivery_time_start', 'Expected delivery time start', 16);
        $this->addText('expected_delivery_time_end', 'Expected delivery time end', 16);

        $this->addText('is_pakjegemak', 'Is Pakjegemak', 1);
        $this->addText('pg_location_code', 'PakjeGemak Location Code', 32);
        $this->addText('pg_retail_network_id', 'PakjeGemak Retail Netwerok ID', 32);

        $this->addTimestamp('confirmed_at', 'Confirmed at');
        $this->addTimestamp('created_at', 'Created at');
        $this->addTimestamp('updated_at', 'Updated at');
    }
}
