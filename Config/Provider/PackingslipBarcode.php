<?php

namespace TIG\PostNL\Config\Provider;

class PackingslipBarcode extends AbstractConfigProvider
{
    const XPATH_BARCODE_ENABLED        = 'tig_postnl/labelandpackingslipoptions/enable_barcode';
    const XPATH_BARCODE_VALUE          = 'tig_postnl/labelandpackingslipoptions/barcode_value';
    const XPATH_BARCODE_POSITION       = 'tig_postnl/labelandpackingslipoptions/barcode_position';
    const XPATH_BARCODE_TYPE           = 'tig_postnl/labelandpackingslipoptions/barcode_type';
    const XPATH_BARCODE_BACKGROUND     = 'tig_postnl/labelandpackingslipoptions/background_color';
    const XPATH_BARCODE_COLOR          = 'tig_postnl/labelandpackingslipoptions/barcode_color';
    const XPATH_BARCODE_INCLUDE_NUMBER = 'tig_postnl/labelandpackingslipoptions/barcode_numberinclude';

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return (bool) $this->getConfigFromXpath(static::XPATH_BARCODE_ENABLED, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getValue($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_BARCODE_VALUE, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return array
     */
    public function getPosition($storeId = null)
    {
        $position = explode(',', $this->getConfigFromXpath(static::XPATH_BARCODE_POSITION, $storeId));
        if (count($position) !== 4) {
            // Invalid value given, fall back on default setting.
            $position = [360, 750, 550, 790];
        }

        return $position;
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getType($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_BARCODE_TYPE, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getBackgroundColor($storeId = null)
    {
        return strtoupper($this->getConfigFromXpath(static::XPATH_BARCODE_BACKGROUND, $storeId));
    }

    /**
     * @param null $storeId
     *
     * @return string
     */
    public function getFontColor($storeId = null)
    {
        return strtoupper($this->getConfigFromXpath(static::XPATH_BARCODE_COLOR, $storeId));
    }

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function includeNumber($storeId = null)
    {
        return (bool) $this->getConfigFromXpath(static::XPATH_BARCODE_INCLUDE_NUMBER, $storeId);
    }
}
