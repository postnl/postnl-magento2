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

use Magento\Framework\HTTP\PhpEnvironment\ServerAddress;
use Magento\Framework\Stdlib\DateTime\DateTime;
use TIG\PostNL\Config\Provider\AccountConfiguration;
use \TIG\PostNL\Helper\Data;

class Message
{
    /**
     * @var ServerAddress
     */
    protected $serverAddress;

    /**
     * @var AccountConfiguration
     */
    protected $accountConfiguration;

    /** @var Data  */
    protected $postNLhelper;

    /**
     * @var array
     */
    protected $messageIdStrings = [];

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

        $message['MessageID']        = md5($messageIdString);
        $message['MessageTimeStamp'] = $this->postNLhelper->getCurrentTimeStamp();

        return $message;
    }

    /**
     * @param $barcode
     *
     * @return string
     */
    protected function getMessageIdString($barcode)
    {
        if (array_key_exists($barcode, $this->messageIdStrings)) {
            return $this->messageIdStrings[$barcode];
        }

        $id = uniqid(
            'postnl_'
            . ip2long($this->serverAddress->getServerAddress())
        );

        $messageIdString = $id
            . $this->accountConfiguration->getCustomerNumber()
            . $barcode
            . microtime();

        $this->messageIdStrings[$barcode] = $messageIdString;

        return $messageIdString;
    }
}
