<?php

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
    public function changeRequestXml($xml)
    {
        $this->requestXml = $xml;
        $this->composeMessage();

        return $this;
    }

    /**
     * Set $_responseXml to specified value
     *
     * @param $xml
     * @return $this
     */
    public function changeResponseXml($xml)
    {
        $this->responseXml = $xml;
        $this->composeMessage();

        return $this;
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
     * @param $xml
     *
     * @return void
     */
    private function addXml($message, $xml)
    {
        if ($xml === null || $xml == '') {
            return;
        }

        $this->message .= PHP_EOL . PHP_EOL;
        $this->message .= '<<<< ' . $message . ' >>>>' . PHP_EOL;
        $this->message .= $xml;
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
