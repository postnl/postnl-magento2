<?php

namespace TIG\PostNL\Logging;

use Monolog\Logger;
use Magento\Framework\Logger\Handler\Base;

class CriticalHandler extends Base
{
    // @codingStandardsIgnoreLine
    protected $loggerType = Logger::CRITICAL;

    // @codingStandardsIgnoreLine
    protected $fileName = '/var/log/PostNL/critical.log';
}
