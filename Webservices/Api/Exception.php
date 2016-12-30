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
namespace TIG\PostNL\Webservices\Api;

use TIG\PostNL\Exception as PostNLException;

class Exception extends PostNLException
{
    /**
     * XML sent to CIF by the extension
     *
     * @var string The XML string sent to CIF
     */
    private $requestXml;

    /**
     * XML received in response
     *
     * @var string The XML string CIF returned
     */
    private $responseXml;

    /**
     * Array of error numbers
     *
     * @var array
     */
    private $errorNumbers = [];

    /**
     * @var array
     */
    private $messages = [];

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

        return $this;
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

        return $this;
    }

    /**
     * Set the error numbers array
     *
     * @param array $errorNumbers
     *
     * @return $this
     */
    public function setErrorNumbers($errorNumbers)
    {
        $this->errorNumbers = $errorNumbers;

        return $this;
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
     * Get $_responseXml
     *
     * @return string
     */
    public function getResponseXml()
    {
        return $this->responseXml;
    }

    /**
     * get the error numbers array
     *
     * @return array
     */
    public function getErrorNumbers()
    {
        return $this->errorNumbers;
    }

    /**
     * @param string|int $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Add an error number to the error numbers array
     *
     * @param int $errorNumber
     *
     * @return $this
     */
    public function addErrorNumber($errorNumber)
    {
        $errorNumbers = $this->getErrorNumbers();
        $errorNumbers[] = $errorNumber;

        $this->setErrorNumbers($errorNumbers);

        return $this;
    }

    /**
     * @param string $type
     *
     * @return array
     *
     * @todo refactor
     */
    public function getMessages($type = '')
    {
        if ('' !== $type) {
            return isset($this->messages[$type]) ? $this->messages[$type] : [];
        }

        $arrRes = [];
        foreach ($this->messages as $messageType => $messages) {
            $arrRes = array_merge($arrRes, $messages);
        }

        return $arrRes;
    }

    /**
     * Set or append a message to existing one
     *
     * @param string $message
     * @param bool $append
     */
    public function setMessage($message, $append = false)
    {
        if ($append) {
            $message = $this->message . $message;
        }

        $this->message = $message;
    }
}
