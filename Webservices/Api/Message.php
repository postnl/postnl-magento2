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
namespace TIG\PostNL\Webservices\Api;

use Magento\Framework\HTTP\PhpEnvironment\ServerAddress;
use Magento\Framework\Stdlib\DateTime\DateTime;
use TIG\PostNL\Config\Provider\AccountConfiguration;
use \TIG\PostNL\Helper\Data;

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
            . ip2long($this->serverAddress->getServerAddress())
        );

        $messageIdString = $identifier
            . $this->accountConfiguration->getCustomerNumber()
            . $barcode
            . microtime();

        $this->messageIdStrings[$barcode] = $messageIdString;

        return $messageIdString;
    }
}
