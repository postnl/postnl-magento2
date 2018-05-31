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
namespace TIG\PostNL\Test\Unit\Config\Provider;

use TIG\PostNL\Config\Provider\LoggingConfiguration;

class LoggingConfigurationTest extends AbstractConfigurationTest
{
    protected $instanceClass = LoggingConfiguration::class;

    public function testGetLoggingTypes($value = '100,200,300')
    {
        $instance = $this->getInstance();
        $this->setXpath(LoggingConfiguration::XPATH_LOGGING_TYPE, $value);
        $this->assertEquals($value, $instance->getLoggingTypes());
    }

    public function canLogProvider()
    {
        return [
            'can log' => [100, '100,200,300', true],
            'can not log' => [400, '100,200,300', false]
        ];
    }

    /**
     * @dataProvider canLogProvider
     * @param $level
     * @param $value
     * @param $expected
     */
    public function testCanLog($level, $value, $expected)
    {
        $instance = $this->getInstance();
        $this->setXpath(LoggingConfiguration::XPATH_LOGGING_TYPE, $value);

        $result = $instance->canLog($level);
        $this->assertEquals($expected, $result);
    }
}
