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
use TIG\PostNL\Config\Source\Options\ProductOptions;
use TIG\PostNL\Exception as PostnlException;
use TIG\PostNL\Service\Order\ProductInfo;
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
     * @var ProductOptions
     */
    private $options;

    /**
     * @param AccountConfiguration  $accountConfiguration
     * @param Globalpack            $globalpack
     * @param PepsConfiguration     $pepsConfiguration
     * @param ProductOptions        $options
     */
    public function __construct(
        AccountConfiguration $accountConfiguration,
        Globalpack $globalpack,
        PepsConfiguration $pepsConfiguration,
        ProductOptions $options
    ) {
        $this->accountConfiguration    = $accountConfiguration;
        $this->globalpackConfiguration = $globalpack;
        $this->pepsConfiguration       = $pepsConfiguration;
        $this->options = $options;
    }

    /**
     * @param $barcodeType
     *
     * @return array
     * @throws \TIG\PostNL\Exception
     */
    public function get($barcodeType)
    {
        $this->set(strtoupper($barcodeType));
        return $this->response;
    }

    /**
     * @param        $productCode
     * @param null   $storeId
     *
     * @return array|string
     * @throws PostnlException
     */
    public function getByProductCode($productCode, $storeId = null)
    {
        $this->storeId = $storeId;

        if ($this->options->doesProductMatchFlags($productCode, 'group', 'global_options')) {
            return $this->get('GLOBAL');
        }

        if ($this->options->doesProductMatchFlags($productCode, 'group', 'eu_options') ||
            $this->options->doesProductMatchFlags($productCode, 'group', 'be_options') ||
            $this->options->doesProductMatchFlags($productCode, 'group', 'pakjegemak_be_options')
        ) {
            return $this->get('EU');
        }

        if ($this->options->doesProductMatchFlags($productCode, 'group', 'priority_options')) {
            return $this->get('PEPS');
        }

        return $this->get('NL');
    }

    /**
     * @param $type
     *
     * @throws \TIG\PostNL\Exception
     */
    public function set($type)
    {
        $this->response['type']  = '3S';
        $this->response['range'] = $this->accountConfiguration->getCustomerCode($this->storeId);
        switch ($type) {
            case 'NL':
                $this->updateNlSerie();
                return;
            case 'EU':
                $this->updateEuSerie();
                return;
            case 'GLOBAL':
                $this->updateGlobalPackOptions();
                return;
            case 'PEPS':
                $this->updatePepsOptions();
                return;
        }

        $this->noBarcodeDataError($type);
    }

    private function updateNlSerie()
    {
        $this->response['serie'] = static::NL_BARCODE_SERIE_LONG;
        if (strlen($this->response['range']) > 3) {
            $this->response['serie'] = static::NL_BARCODE_SERIE_SHORT;
        }
    }

    private function updateEuSerie()
    {
        $this->response['serie'] = static::EU_BARCODE_SERIE_LONG;
        if (strlen($this->response['range']) > 3) {
            $this->response['serie'] = static::EU_BARCODE_SERIE_SHORT;
        }
    }

    private function updateGlobalPackOptions()
    {
        $this->response['type']  = $this->globalpackConfiguration->getBarcodeType();
        $this->response['range'] = $this->globalpackConfiguration->getBarcodeRange();
        $this->response['serie'] = static::GLOBAL_BARCODE_SERIE;
    }

    private function updatePepsOptions()
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
