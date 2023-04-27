<?php

namespace TIG\PostNL\Logging;

use Monolog\Logger;
use Magento\Framework\Logger\Handler\Base;

class DebugHandler extends Base
{
    // @codingStandardsIgnoreLine
    protected $loggerType = Logger::DEBUG;

    // @codingStandardsIgnoreLine
    protected $fileName = '/var/log/PostNL/debug.log';
}
