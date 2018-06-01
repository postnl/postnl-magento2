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
