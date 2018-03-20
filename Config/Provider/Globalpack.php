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

class Globalpack extends AbstractConfigProvider
{
    const XPATH_GLOBALPACK_ACTIVE             = 'tig_postnl/globalpack/enabled';
    const XPATH_GLOBALPACK_BARCODE_TYPE       = 'tig_postnl/globalpack/barcode_type';
    const XPATH_GLOBALPACK_BARCODE_RANGE      = 'tig_postnl/globalpack/barcode_range';
    const XPATH_GLOBALPACK_LICENSE_NUMBER     = 'tig_postnl/globalpack/license_number';
    const XPATH_GLOBALPACK_CERTIFICATE_NUMBER = 'tig_postnl/globalpack/certificate_number';

    /**
     * @param null $storeId
     *
     * @return bool
     */
    public function isEnabled($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_GLOBALPACK_ACTIVE, $storeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getBarcodeType($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_GLOBALPACK_BARCODE_TYPE, $storeId);
    }

    /**
     * @param null $stordeId
     *
     * @return mixed
     */
    public function getBarcodeRange($stordeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_GLOBALPACK_BARCODE_RANGE, $stordeId);
    }

    /**
     * @param null $stordeId
     *
     * @return mixed
     */
    public function getLicenseNumber($stordeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_GLOBALPACK_LICENSE_NUMBER, $stordeId);
    }

    /**
     * @param null $storeId
     *
     * @return mixed
     */
    public function getCertificateNumber($storeId = null)
    {
        return $this->getConfigFromXpath(static::XPATH_GLOBALPACK_CERTIFICATE_NUMBER, $storeId);
    }
}
