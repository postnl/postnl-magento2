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

class PepsConfiguration extends AbstractConfigProvider
{
    const XPATH_BARCODE_TYPE  = 'tig_postnl/peps/barcode_type';
    const XPATH_BARCODE_RANGE = 'tig_postnl/peps/barcode_range';

    const XPATH_CALCULATION_MODE = 'tig_postnl/peps/peps_boxable_packets_calculation_mode';

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

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getBoxablePacketCalculationMode($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_CALCULATION_MODE, $storeId);
    }
}
