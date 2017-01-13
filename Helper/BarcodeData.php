<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Helper;

use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Config\Provider\DefaultConfiguration;
use TIG\PostNL\Exception as PostnlException;

class BarcodeData
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
     * @var DefaultConfiguration
     */
    private $defaultConfiguration;

    /**
     * @param AccountConfiguration $accountConfiguration
     * @param DefaultConfiguration $defaultConfiguration
     */
    public function __construct(
        AccountConfiguration $accountConfiguration,
        DefaultConfiguration $defaultConfiguration
    ) {
        $this->accountConfiguration = $accountConfiguration;
        $this->defaultConfiguration = $defaultConfiguration;
    }

    /**
     * Gets data for the barcode that's requested. Depending on the destination of the shipment several barcode types
     * may be requested.
     *
     * @param string $barcodeType
     *
     * @return array
     *
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
     * @return array
     */
    private function getNlBarcode()
    {
        $type  = '3S';
        $range = $this->accountConfiguration->getCustomerCode();
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
        $range = $this->accountConfiguration->getCustomerCode();
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
        $type  = $this->getGlobalBarcodeType();
        $range = $this->getGlobalBarcodeRange();
        $serie = static::GLOBAL_BARCODE_SERIE;

        return [
            'type' => $type,
            'range' => $range,
            'serie' => $serie,
        ];
    }

    /**
     * Gets the global barcode type from system/config
     *
     * @return string
     */
    private function getGlobalBarcodeType()
    {
        return $this->defaultConfiguration->getBarcodeGlobalType();
    }

    /**
     * Gets the global barcode range from system/config
     *
     * @return string
     */
    private function getGlobalBarcodeRange()
    {
        return $this->defaultConfiguration->getBarcodeGlobalRange();
    }

    /**
     * @param $barcodeData
     *
     * @throws PostnlException
     */
    private function validateBarcodeData($barcodeData)
    {
        if (!$barcodeData['type'] || !$barcodeData['range']) {
            // @codingStandardsIgnoreLine
            $error = __('Unable to retrieve barcode data.');
            throw new PostnlException(
                $error,
                'POSTNL-0111'
            );
        }
    }

    /**
     * @param $barcodeType
     *
     * @return array
     * @throws PostnlException
     */
    private function getBarcodeData($barcodeType)
    {
        $barcodeData = null;
        switch ($barcodeType) {
            case 'NL':
                $barcodeData = $this->getNlBarcode();
                break;
            case 'EU':
                $barcodeData = $this->getEuBarcode();
                break;
            case 'GLOBAL':
                $barcodeData = $this->getGlobalBarcode();
                break;
        }

        if ($barcodeData === null) {
            $this->noBarcodeDataError($barcodeType);
        }

        return $barcodeData;
    }

    /**
     * @param $barcodeType
     *
     * @throws PostnlException
     */
    private function noBarcodeDataError($barcodeType)
    {
        // @codingStandardsIgnoreLine
        $error = __('Invalid barcodetype requested: %s', $barcodeType);
        throw new PostnlException(
            $error,
            'POSTNL-0061'
        );
    }
}
