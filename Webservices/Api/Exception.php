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
namespace TIG\PostNL\Webservices\Api;

use TIG\PostNL\Exception as PostNLException;

class Exception extends PostNLException
{
    /**
     * @var string
     */
    private $originalMessage = '';

    /**
     * @var string
     */
    private $requestXml = null;

    /**
     * @var string
     */
    private $responseXml = null;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * @param \Magento\Framework\Phrase $message
     * @param int                       $code
     * @param null                      $previous
     */
    public function __construct($message, $code = 0, $previous = null)
    {
        $this->originalMessage = $message;

        parent::__construct($message, $code, $previous);
    }

    /**
     * Set $_requestXml to specified value
     *
     * @param $xml
     *
     * @return $this
     */
    public function setRequestXml($xml)
    {
        $this->requestXml = $xml;
        $this->composeMessage();
    }

    /**
     * Set $_responseXml to specified value
     *
     * @param $xml
     * @return $this
     */
    public function setResponseXml($xml)
    {
        $this->responseXml = $xml;
        $this->composeMessage();
    }

    /**
     * @param $error
     */
    public function addError($error)
    {
        $this->errors[] = $error;
        $this->composeMessage();
    }

    /**
     * Get $_requestXml
     *
     * @return string
     */
    public function getRequestXml()
    {
        return $this->requestXml;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Get $_responseXml
     *
     * @return string
     */
    public function getResponseXml()
    {
        return $this->responseXml;
    }

    /**
     * Compose the message returned by the Exception.
     */
    private function composeMessage()
    {
        $this->message = $this->originalMessage;

        $this->addErrorsToMessage();
        $this->addXml('Request XML', $this->requestXml);
        $this->addXml('Response XML', $this->responseXml);
    }

    /**
     * @param $message
     *
     * @return string
     */
    private function addXml($message, $xml)
    {
        if ($xml === null || $xml == '') {
            return;
        }

        $this->message .= PHP_EOL . PHP_EOL;
        $this->message .= '<<<< ' . $message . ' >>>>' . PHP_EOL;
        $this->message .= $this->requestXml;
    }

    /**
     * If there are any errors, add the to the message.
     */
    private function addErrorsToMessage()
    {
        $errors = $this->getErrors();
        if (empty($errors)) {
            return;
        }

        $this->message .= PHP_EOL . PHP_EOL;

        foreach ($this->getErrors() as $error) {
            $this->message .= '- ' . $error . PHP_EOL;
        }

        $this->message = trim($this->message);
    }
}
