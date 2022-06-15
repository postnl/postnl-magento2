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
namespace TIG\PostNL\Webservices;

use TIG\PostNL\Helper\Data as Helper;
use TIG\PostNL\Logging\Log;
use Zend\Soap\Client;

class ExceptionHandler
{
    /**
     * CIF error namespace.
     */
    const ERROR_NAMESPACE = 'https://postnl.nl/cif/services/common/';

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
     * @var \SoapFault
     */
    private $soapFault;

    /**
     * @var \DomDocument|null
     */
    private $responseXml;

    /**
     * @var Api\Exception
     */
    private $exception;

    /**
     * @var array
     */
    private $errors = [];

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
     * @param \SoapFault $soapFault
     * @param Client     $client
     *
     * @throws Api\Exception
     */
    public function handle(\SoapFault $soapFault, Client $client = null)
    {
        $this->soapFault = $soapFault;
        $this->prepareException($client);

        if ($this->hasValidErrorsOnly()) {
            return;
        }

        if ($this->hasCifException()) {
            $this->addCifErrorsToException();
        }

        $this->log->critical($this->exception->getMessage());

        // @codingStandardsIgnoreLine
        throw $this->exception;
    }

    /**
     * @return bool
     */
    private function hasCifException()
    {
        $message = $this->soapFault->getMessage();

        return $message === 'Check CIFException in the detail section';
    }

    /**
     * Retrieve the errors and add them to the exception
     */
    private function addCifErrorsToException()
    {
        foreach ($this->getErrorsFromResponse() as $error) {
            $this->exception->addError($error['number'] . ': ' . $error['message']);
        }
    }

    /**
     * @return void
     */
    private function prepareException(Client $client)
    {
        // @codingStandardsIgnoreLine
        $this->exception = new Api\Exception($this->soapFault->getMessage(), null, $this->soapFault);

        if ($lastRequestXml = $client->getLastRequest()) {
            $this->exception->changeRequestXml($this->helper->formatXml($lastRequestXml));
        }

        if ($lastResponseXml = $client->getLastResponse()) {
            // @codingStandardsIgnoreLine
            $this->responseXml = new \DOMDocument;
            $this->responseXml->loadXML($lastResponseXml);
            $this->exception->changeResponseXml($this->helper->formatXml($lastResponseXml));
        }
    }

    /**
     * @return int
     */
    private function hasValidErrorsOnly()
    {
        if (empty($this->responseXml)) {
            return false;
        }

        $errors = $this->getErrorsFromResponse();

        $errors = array_filter($errors, function ($error) {
            return $error['number'] !== static::SHIPMENT_NOT_FOUND_ERROR_NUMBER;
        });

        return !count($errors);
    }

    /**
     * @return array
     */
    private function getErrorsFromResponse()
    {
        if (!empty($this->errors)) {
            return $this->errors;
        }

        $errors = $this->responseXml->getElementsByTagNameNS(static::ERROR_NAMESPACE, 'ExceptionData');

        /** @var \DOMElement $error */
        foreach ($errors as $error) {
            $message = $this->getDomElement($error, 'ErrorMsg');
            $errorNumber = $this->getDomElement($error, 'ErrorNumber');

            $this->errors[] = [
                'message' => $message,
                'number' => $errorNumber,
            ];
        }

        return $this->errors;
    }

    /**
     * @param \DOMElement $element
     * @param string $tagName
     *
     * @return mixed
     */
    private function getDomElement($element, $tagName)
    {
        // @codingStandardsIgnoreLine
        return $element->getElementsByTagName($tagName)->item(0)->nodeValue;
    }
}
