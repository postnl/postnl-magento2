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
 * @copyright   Copyright (c) 2016 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Webservices;

use TIG\PostNL\Logging\Log;

class ExceptionHandler
{
    /**
     * CIF error namespace.
     */
    const CIF_ERROR_NAMESPACE = 'http://postnl.nl/cif/services/common/';

    /**
     * The error number CIF uses for the 'shipment not found' error.
     */
    const SHIPMENT_NOT_FOUND_ERROR_NUMBER = 13;

    /**
     * @var
     */
    private $log;

    /**
     * @param Log $log
     */
    public function __construct(
        Log $log
    ) {
        $this->log = $log;
    }

    /**
     * @param \SoapFault  $soapFault
     * @param \SoapClient $client
     *
     * @return $this
     * @throws Api\Exception
     *
     * @todo Refactor this code, it comes from the M1 version with some required M2 changes.
     */
    public function handle(\SoapFault $soapFault, \SoapClient $client = null)
    {
        $logException = true;

        $exception = new Api\Exception($soapFault->getMessage(), null, $soapFault);

        $requestXML = '';
        $responseXML = '';

        /**
         * Get the request and response XML data
         */
        if ($client) {
            $requestXML  = $this->formatXml($client->__getLastRequest());
            $responseXML = $this->formatXml($client->__getLastResponse());
        }

        /**
         * If we got a response, parse it for specific error messages and add these to the exception.
         */
        if (!empty($responseXML)) {
            /**
             * If we received a response, parse it for errors and create an appropriate exception
             */
            $errorResponse = new \DOMDocument();
            $errorResponse->loadXML($responseXML);

            /**
             * Get all error messages.
             */
            $errors = $errorResponse->getElementsByTagNameNS(static::CIF_ERROR_NAMESPACE, 'ErrorMsg');
            if ($errors) {
                $message = '';
                foreach ($errors as $error) {
                    $message .= $error->nodeValue . PHP_EOL;
                }

                /**
                 * Update the exception.
                 */
                $exception->setMessage($message);
            }

            /**
             * Parse any CIF error numbers we may have received.
             */
            $errorNumbers = $errorResponse->getElementsByTagNameNS(self::CIF_ERROR_NAMESPACE, 'ErrorNumber');
            if ($errorNumbers) {
                foreach ($errorNumbers as $errorNumber) {
                    /**
                     * Error number 13 means that the shipment was not found by PostNL. This error is very common and
                     * can be completely valid. To prevent the log files from filling up extremely quickly, we do not
                     * log this error.
                     */
                    $value = $errorNumber->nodeValue;
                    if ($value == self::SHIPMENT_NOT_FOUND_ERROR_NUMBER) {
                        $logException = false;
                    }

                    $exception->addErrorNumber($value);
                }
            }
        }

        /**
         * Add the response and request data to the exception (to be logged later)
         */
        if (!empty($requestXML) || !empty($responseXML)) {
            $exception->setRequestXml($requestXML);
            $exception->setResponseXml($responseXML);
        }

        if ($logException) {
            /**
             * Log the exception and throw it.
             */
            $this->log->critical($exception);
        }

        throw $exception;
    }

    /**
     * @param $xml
     *
     * @return string
     */
    private function formatXml($xml)
    {
        $domDocument = new \DOMDocument();
        $domDocument->loadXML($xml);
        $domDocument->formatOutput = true;

        return $domDocument->saveXML();
    }
}
