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
namespace TIG\PostNL\Webservices;

use TIG\PostNL\Helper\Data as Helper;
use TIG\PostNL\Logging\Log;
use Zend\Soap\Client;

class ExceptionHandler
{
    /**
     * CIF error namespace.
     */
    const ERROR_NAMESPACE = 'http://postnl.nl/cif/services/common/';

    /**
     * The error number CIF uses for the 'shipment not found' error.
     */
    const SHIPMENT_NOT_FOUND_ERROR_NUMBER = 13;

    /**
     * @var Log
     */
    private $log;

    /**
     * @var Helper
     */
    private $helper;

    /**
     * @param Log    $log
     * @param Helper $helper
     */
    public function __construct(
        Log $log,
        Helper $helper
    ) {
        $this->log = $log;
        $this->helper = $helper;
    }

    /**
     * @param \SoapFault  $soapFault
     * @param Client      $client
     *
     * @return $this
     * @throws Api\Exception
     */
    public function handle(\SoapFault $soapFault, Client $client = null)
    {
        // @codingStandardsIgnoreLine
        $exception = new Api\Exception($soapFault->getMessage(), null, $soapFault);

        $responseXML = $this->handleSoapData($client, $exception);
        $logException = $this->handleResponseXml($responseXML, $exception);

        if ($logException) {
            /**
             * Log the exception and throw it.
             */
            $this->log->critical($exception);
        }

        throw $exception;
    }

    /**
     * @param Api\Exception $exception
     * @param \DOMDocument  $errorResponse
     *
     * @return bool
     */
    private function addErrorNumbersToException(Api\Exception $exception, \DOMDocument $errorResponse)
    {
        $errorNumbers = $errorResponse->getElementsByTagNameNS(self::ERROR_NAMESPACE, '*');

        if (!$errorNumbers) {
            return false;
        }

        $logException = true;
        foreach ($errorNumbers as $errorNumber) {
            $result = $this->checkErrorNumber($exception, $errorNumber);

            $logException = !$result ? false : $logException;
        }

        return $logException;
    }

    /**
     * @param Api\Exception $exception
     * @param               $errorNumber
     *
     * @return bool
     */
    private function checkErrorNumber(Api\Exception $exception, $errorNumber)
    {
        $logException = true;

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

        return $logException;
    }

    /**
     * @param $errorResponse
     * @param $exception
     */
    private function parseErrors(\DOMDocument $errorResponse, Api\Exception $exception)
    {
        /**
         * Get all error messages.
         */
        $errors = $errorResponse->getElementsByTagNameNS(static::ERROR_NAMESPACE, 'ErrorMsg');
        if (!$errors) {
            return;
        }

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
     * @param $responseXML
     * @param $exception
     *
     * @return bool
     */
    private function handleResponseXml($responseXML, $exception)
    {
        /**
         * If we got a response, parse it for specific error messages and add these to the exception.
         */
        if (empty($responseXML)) {
            return true;
        }

        /**
         * If we received a response, parse it for errors and create an appropriate exception
         */
        // @codingStandardsIgnoreLine
        $errorResponse = new \DOMDocument();
        $errorResponse->loadXML($responseXML);
        $this->parseErrors($errorResponse, $exception);

        /**
         * Parse any CIF error numbers we may have received.
         */
        return $this->addErrorNumbersToException($exception, $errorResponse);
    }

    /**
     * @param Client $client
     * @param        $exception
     *
     * @return string
     */
    private function handleSoapData(Client $client, Api\Exception $exception)
    {
        /**
         * Get the request and response XML data
         */
        if (!$client) {
            return '';
        }

        $requestXML  = $this->helper->formatXml($client->getLastRequest());
        $responseXML = $this->helper->formatXml($client->getLastResponse());

        /**
         * Add the response and request data to the exception (to be logged later)
         */
        $exception->setRequestXml($requestXML);
        $exception->setResponseXml($responseXML);

        return $responseXML;
    }
}
