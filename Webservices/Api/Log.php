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

use TIG\PostNL\Helper\Data as Helper;
use TIG\PostNL\Logging\Log as Logger;
use Laminas\Soap\Client;

class Log
{
    /**
     * @var Logger
     */
    private $log;
    /**
     * @var Helper
     */
    private $helper;

    /**
     * @param Logger $log
     * @param Helper $helper
     */
    public function __construct(
        Logger $log,
        Helper $helper
    ) {
        $this->log = $log;
        $this->helper = $helper;
    }

    /**
     * @param Client $client
     */
    public function request(Client $client)
    {
        $message = '<<< REQUEST XML >>>' . PHP_EOL;
        $lastRequest = $client->getLastRequest();
        $message .= $this->helper->formatXml($lastRequest);

        $this->log->debug($message);

        $message = '<<< RESPONSE XML >>>' . PHP_EOL;
        $lastResponse = $client->getLastResponse();
        $message .= $this->helper->formatXml($lastResponse);

        $this->log->debug($message);
    }
}
