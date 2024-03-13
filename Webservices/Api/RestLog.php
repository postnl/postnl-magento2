<?php

namespace TIG\PostNL\Webservices\Api;

use Laminas\Http\Response;
use TIG\PostNL\Logging\Log as Logger;
use TIG\PostNL\Webservices\Endpoints\RestInterface;

class RestLog
{
    private Logger $log;

    public function __construct(
        Logger $log
    ) {
        $this->log = $log;
    }

    public function request(RestInterface $endpoint, Response $response): void
    {
        $message = '<<< REQUEST JSON >>>' . PHP_EOL;
        $message .= $endpoint->getMethod() . ': ' . $endpoint->getResource()
            . $endpoint->getVersion() . '/' . $endpoint->getEndpoint() . PHP_EOL;
        $message .= print_r($endpoint->getRequestData(), true) . PHP_EOL;

        $message .= '<<< RESPONSE JSON >>>' . PHP_EOL;
        $message .= $response->getBody() . PHP_EOL;

        $this->log->debug($message);
    }
}
