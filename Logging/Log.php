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
namespace TIG\PostNL\Logging;

use Monolog\DateTimeImmutable;
use Monolog\Logger;
use TIG\PostNL\Config\Provider\LoggingConfiguration;

class Log extends Logger
{
    /**
     * @var LoggingConfiguration
     */
    private $logConfig;

    /**
     * @param string               $name
     * @param array                $handlers
     * @param array                $processors
     * @param LoggingConfiguration $loggingConfiguration
     */
    public function __construct(
        $name,
        LoggingConfiguration $loggingConfiguration,
        array $handlers = [],
        array $processors = []
    ) {
        $this->logConfig = $loggingConfiguration;
        parent::__construct($name, $handlers, $processors);
    }

    /**
     * @param int    $level
     * @param string $message
     * @param array  $context
     *
     * @return bool
     */
    public function addRecord($level, $message, array $context = [], DateTimeImmutable $dateTimeImmutable = null):bool
    {
        if (!$this->logConfig->canLog($level)) {
            return false;
        }

        return parent::addRecord($level, $message, $context);
    }
}
