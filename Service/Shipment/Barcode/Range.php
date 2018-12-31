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
namespace TIG\PostNL\Service\Shipment\Barcode;

use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\Globalpack;
use TIG\PostNL\Config\Provider\PepsConfiguration;
use TIG\PostNL\Exception as PostnlException;
use TIG\PostNL\Service\Shipment\EpsCountries;

class Range
{
    /**
     * Possible barcodes series per barcode type.
     */
    const NL_BARCODE_SERIE_LONG   = '0000000000-9999999999';
    const NL_BARCODE_SERIE_SHORT  = '000000000-999999999';
    const EU_BARCODE_SERIE_LONG   = '00000000-99999999';
    const EU_BARCODE_SERIE_SHORT  = '0000000-9999999';
    const GLOBAL_BARCODE_SERIE    = '0000-9999';

    /**
     * @var AccountConfiguration
     */
    private $accountConfiguration;

    /**
     * @var Globalpack
     */
    private $globalpackConfiguration;

    /**
     * @var PepsConfiguration
     */
    private $pepsConfiguration;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var array
     */
    private $response = [
        'type'  => '',
        'range' => '',
        'serie' => ''
    ];

    /**
     * @param AccountConfiguration  $accountConfiguration
     * @param Globalpack            $globalpack
     * @param PepsConfiguration     $pepsConfiguration
     */
    public function __construct(
        AccountConfiguration $accountConfiguration,
        Globalpack $globalpack,
        PepsConfiguration $pepsConfiguration
    ) {
        $this->accountConfiguration    = $accountConfiguration;
        $this->globalpackConfiguration = $globalpack;
        $this->pepsConfiguration       = $pepsConfiguration;
    }

    /**
     * @param $barcodeType
     *
     * @return array
     */
    public function get($barcodeType)
    {
        $this->set($barcodeType);
        return $this->response;
    }

    /**
     * @param        $countryId
     * @param null   $storeId
     * @param string $type
     *
     * @return array|string
     */
    public function getByCountryId($countryId, $storeId = null, $type = '')
    {
        $this->storeId = $storeId;

        if ($type) {
            return $this->get($type);
        }

        if ($countryId == 'NL') {
            return $this->get('NL');
        }

        if (in_array($countryId, EpsCountries::ALL)) {
            return $this->get('EU');
        }

        return $this->get('GLOBAL');
    }

    /**
     * @param $type
     */
    public function set($type)
    {
        $this->response['type']  = '3S';
        $this->response['range'] = $this->accountConfiguration->getCustomerCode($this->storeId);
        switch (strtoupper($type)) {
            case 'NL' :
                $this->setNlSerie();
                break;
            case 'EU' :
                $this->setEuSerie();
                break;
            case 'GLOBAL' :
                $this->setGlobalPackOptions();
                break;
            case 'PEPS' :
                $this->setPepsOptions();
                break;
            default:
                $this->noBarcodeDataError($type);
                break;
        }
    }

    private function setNlSerie()
    {
        $this->response['serie'] = static::NL_BARCODE_SERIE_LONG;
        if (strlen($this->response['range']) > 3) {
            $this->response['serie'] = static::NL_BARCODE_SERIE_SHORT;
        }
    }

    private function setEuSerie()
    {
        $this->response['serie'] = static::EU_BARCODE_SERIE_LONG;
        if (strlen($this->response['range']) > 3) {
            $this->response['serie'] = static::EU_BARCODE_SERIE_SHORT;
        }
    }

    private function setGlobalPackOptions()
    {
        $this->response['type']  = $this->globalpackConfiguration->getBarcodeType();
        $this->response['range'] = $this->globalpackConfiguration->getBarcodeRange();
        $this->response['serie'] = static::GLOBAL_BARCODE_SERIE;
    }

    private function setPepsOptions()
    {
        $this->response['type']  = $this->pepsConfiguration->getBarcodeType();
        $this->response['range'] = $this->pepsConfiguration->getBarcodeRange();
        $this->response['serie'] = static::EU_BARCODE_SERIE_LONG;
    }

    /**
     * @param $barcodeType
     * @throws PostnlException
     */
    private function noBarcodeDataError($barcodeType)
    {
        // @codingStandardsIgnoreLine
        $error = __('Invalid barcodetype requested: %1', $barcodeType);
        throw new PostnlException(
            $error,
            'POSTNL-0061'
        );
    }
}
