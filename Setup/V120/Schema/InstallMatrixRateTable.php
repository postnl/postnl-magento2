<?php

namespace TIG\PostNL\Setup\V120\Schema;

use TIG\PostNL\Model\Carrier\Matrixrate;
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

        $this->addInt(Matrixrate::FIELD_WEBSITE_ID, 'Website ID', true, true, 0);
        $this->addText(Matrixrate::FIELD_DESTINY_COUNTRY_ID, 'Destination country ID ISO/2', 255, false, '');
        $this->addInt(Matrixrate::FIELD_DESTINY_REGION_ID, 'Destiny Region ID', false, false, '0');
        $this->addText(Matrixrate::FIELD_DESTINY_ZIP_CODE, 'Destiny ZIP Code', 255, false, '*');
        $this->addDecimal(Matrixrate::FIELD_WEIGHT, 'Minimum Order Weight', '12,4', false, '0.0000');
        $this->addDecimal(Matrixrate::FIELD_SUBTOTAL, 'Minimum Order Amount', '12,4', false, '0.0000');
        $this->addInt(Matrixrate::FIELD_QUANTITY, 'Minimum Quantity', 10, false, 0);
        $this->addText(Matrixrate::FIELD_PARCEL_TYPE, 'Parcel Type', 255, false, '*');
        $this->addDecimal(Matrixrate::FIELD_PRICE, 'Price', '12,4', false, '0.0000');

        $this->addIndex([
            Matrixrate::FIELD_WEBSITE_ID,
            Matrixrate::FIELD_DESTINY_COUNTRY_ID,
            Matrixrate::FIELD_DESTINY_REGION_ID,
            Matrixrate::FIELD_DESTINY_ZIP_CODE,
            Matrixrate::FIELD_WEIGHT,
            Matrixrate::FIELD_SUBTOTAL,
            Matrixrate::FIELD_QUANTITY,
            Matrixrate::FIELD_PARCEL_TYPE,
        ]);
    }
}
