<?php

namespace TIG\PostNL\Webservices\Api;

use TIG\PostNL\Helper\Data;
use TIG\PostNL\Config\Provider\AccountConfiguration;
use Magento\Framework\HTTP\PhpEnvironment\ServerAddress;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Message
{
    /**
     * @var ServerAddress
     */
    private $serverAddress;

    /**
     * @var AccountConfiguration
     */
    private $accountConfiguration;

    /**
     * @var Data
     */
    private $postNLhelper;

    /**
     * @var array
     */
    private $messageIdStrings = [];

    /**
     * @param ServerAddress        $serverAddress
     * @param Data                 $postNLhelper
     * @param AccountConfiguration $accountConfiguration
     */
    public function __construct(
        ServerAddress $serverAddress,
        Data $postNLhelper,
        AccountConfiguration $accountConfiguration
    ) {
        $this->serverAddress = $serverAddress;
        $this->accountConfiguration = $accountConfiguration;
        $this->postNLhelper = $postNLhelper;
    }

    /**
     * @param       $barcode
     * @param array $message
     *
     * @return array
     */
    public function get($barcode, $message = [])
    {
        $messageIdString = $this->getMessageIdString($barcode);

        // @codingStandardsIgnoreLine
        $message['MessageID']        = md5($messageIdString);
        $message['MessageTimeStamp'] = $this->postNLhelper->getCurrentTimeStamp();

        return $message;
    }

    /**
     * @param $barcode
     *
     * @return string
     */
    private function getMessageIdString($barcode)
    {
        if (array_key_exists($barcode, $this->messageIdStrings)) {
            return $this->messageIdStrings[$barcode];
        }

        $identifier = uniqid(
            'postnl_'
            . $this->serverAddress->getServerAddress(true)
        );

        $messageIdString = $identifier
            . $this->accountConfiguration->getCustomerNumber()
            . $barcode
            . microtime();

        $this->messageIdStrings[$barcode] = $messageIdString;

        return $messageIdString;
    }
}
