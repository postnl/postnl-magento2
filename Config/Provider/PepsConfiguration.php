<?php

namespace TIG\PostNL\Config\Provider;

class PepsConfiguration extends AbstractConfigProvider
{
    const XPATH_BARCODE_TYPE  = 'tig_postnl/peps/barcode_type';
    const XPATH_BARCODE_RANGE = 'tig_postnl/peps/barcode_range';

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getBarcodeType($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_BARCODE_TYPE, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getBarcodeRange($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_BARCODE_RANGE, $storeId);
    }
}
