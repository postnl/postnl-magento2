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

use TIG\PostNL\Model\Carrier\MatrixRate;
use TIG\PostNL\Setup\AbstractTableInstaller;

class InstallMatrixRateTable extends AbstractTableInstaller
{
    const TABLE_NAME = 'tig_postnl_matrixrate';

    /**
     * @return void
     */
    // @codingStandardsIgnoreLine
    protected function defineTable()
    {
        $this->addEntityId();

        $this->addInt(MatrixRate::FIELD_WEBSITE_ID, 'Website ID', true, true, 0);
        $this->addText(MatrixRate::FIELD_DESTINY_COUNTRY_ID, 'Destination country ID ISO/2', 2, false, '');
        $this->addInt(MatrixRate::FIELD_DESTINY_REGION_ID, 'Destiny Region ID', false, false, '0');
        $this->addText(MatrixRate::FIELD_DESTINY_ZIP_CODE, 'Destiny ZIP Code', 255, false, '*');
        $this->addDecimal(MatrixRate::FIELD_WEIGHT, 'Minimum Order Weight', '12,4', false, '0.0000');
        $this->addDecimal(MatrixRate::FIELD_SUBTOTAL, 'Minimum Order Amount', '12,4', false, '0.0000');
        $this->addInt(MatrixRate::FIELD_QUANTITY, 'Minimum Quantity', 10, false, 0);
        $this->addText(MatrixRate::FIELD_PARCEL_TYPE, 'Parcel Type', 255, false, '*');
        $this->addDecimal(MatrixRate::FIELD_PRICE, 'Price', '12,4', false, '0.0000');

        $this->addIndex([
            MatrixRate::FIELD_WEBSITE_ID,
            MatrixRate::FIELD_DESTINY_COUNTRY_ID,
            MatrixRate::FIELD_DESTINY_REGION_ID,
            MatrixRate::FIELD_DESTINY_ZIP_CODE,
            MatrixRate::FIELD_WEIGHT,
            MatrixRate::FIELD_SUBTOTAL,
            MatrixRate::FIELD_QUANTITY,
            MatrixRate::FIELD_PARCEL_TYPE,
        ]);
    }
}
