<?php

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
