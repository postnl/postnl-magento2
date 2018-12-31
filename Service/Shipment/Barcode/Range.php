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
     * Gets data for the barcode that's requested. Depending on the destination of the shipment several barcode types
     * may be requested.
     *
     * @param string $barcodeType
     * @return array
     * @throws PostnlException
     */
    public function get($barcodeType)
    {
        $barcodeType = strtoupper($barcodeType);

        $barcodeData = $this->getBarcodeData($barcodeType);

        $this->validateBarcodeData($barcodeData);

        return $barcodeData;
    }

    /**
     * @param $countryId
     * @param $storeId
     * @param $type
     *
     * @return array
     */
    public function getByCountryId($countryId, $storeId = null, $type = '')
    {
        $this->storeId = $storeId;

        if ($type && $type == 'PEPS') {
            return $this->get('PEPS');
        }

        if ($countryId == 'NL') {
            return $this->get('NL');
        }

        if (in_array($countryId, EpsCountries::ALL)) {
            return $this->get('EU');
        }

        return $this->get('global');
    }

    /**
     * @return array
     */
    private function getNlBarcode()
    {
        $type  = '3S';
        $range = $this->accountConfiguration->getCustomerCode($this->storeId);
        $serie = static::NL_BARCODE_SERIE_LONG;

        if (strlen($range) > 3) {
            $serie = static::NL_BARCODE_SERIE_SHORT;
        }

        return [
            'type' => $type,
            'range' => $range,
            'serie' => $serie,
        ];
    }

    /**
     * @return array
     */
    private function getEuBarcode()
    {
        $type  = '3S';
        $range = $this->accountConfiguration->getCustomerCode($this->storeId);
        $serie = static::EU_BARCODE_SERIE_LONG;

        if (strlen($range) > 3) {
            $serie = static::EU_BARCODE_SERIE_SHORT;
        }

        return [
            'type' => $type,
            'range' => $range,
            'serie' => $serie,
        ];
    }

    /**
     * @return array
     */
    private function getGlobalBarcode()
    {
        $type  = $this->globalpackConfiguration->getBarcodeType();
        $range = $this->globalpackConfiguration->getBarcodeRange();
        $serie = static::GLOBAL_BARCODE_SERIE;

        return [
            'type' => $type,
            'range' => $range,
            'serie' => $serie,
        ];
    }

    /**
     * @return array
     */
    private function getPepsBarcode()
    {
        $type  = $this->pepsConfiguration->getBarcodeType();
        $range = $this->pepsConfiguration->getBarcodeRange();
        $serie = static::EU_BARCODE_SERIE_LONG;

        return [
            'type' => $type,
            'range' => $range,
            'serie' => $serie,
        ];
    }

    /**
     * @param $barcodeData
     * @throws PostnlException
     */
    private function validateBarcodeData($barcodeData)
    {
        if (!$barcodeData['type'] || !$barcodeData['range']) {
            throw new PostnlException(
                // @codingStandardsIgnoreLine
                __('Unable to retrieve barcode data.'),
                'POSTNL-0111'
            );
        }
    }

    /**
     * @param $barcodeType
     * @return array
     * @throws PostnlException
     */
    private function getBarcodeData($barcodeType)
    {
        if ($barcodeType == 'NL') {
            return $this->getNlBarcode();
        }

        if ($barcodeType == 'EU') {
            return $this->getEuBarcode();
        }

        if ($barcodeType == 'GLOBAL') {
            return $this->getGlobalBarcode();
        }

        if ($barcodeType == 'PEPS') {
            return $this->getPepsBarcode();
        }

        $this->noBarcodeDataError($barcodeType);
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
