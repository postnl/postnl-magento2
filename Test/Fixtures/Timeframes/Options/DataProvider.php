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
namespace TIG\PostNL\Test\Fixtures\Timeframes\Options;

class DataProvider
{
    public function allOptions()
    {
        return [
            'when all options are enabled'  => [true, $this->getOptions(), $this->getOptions()],
            'when all options are disabled' => [false, $this->getOptions(), $this->getOptionsWhenAllAreDisabled()]
        ];
    }

    public function evening()
    {
        return [
            'evening is disabled' => [false, $this->getOptions(), $this->getOptionsForEveningDisabled()],
            'evening is enabled'  => [true, $this->getOptions(), $this->getOptions()]
        ];
    }

    public function sunday()
    {
        return [
            'sunday is disabled' => [false, $this->getOptions(), $this->getOptionsForSundayDisabled()],
            'sunday is enabled'  => [true, $this->getOptions(), $this->getOptions()]
        ];
    }

    private function getOptions()
    {
        return [
            (object)[
                'From' => '13:00:00',
                'Options' => (object)[
                    'string' => ['Daytime']
                ],
                'To' => '15:30:00'
            ],
            (object)[
                'From' => '18:00:00',
                'Options' => (object)[
                    'string' => ['Evening']
                ],
                'To' => '22:30:00'
            ],
            (object)[
                'From' => '13:00:00',
                'Options' => (object)[
                    'string' => ['Sunday']
                ],
                'To' => '22:30:00'
            ]
        ];
    }

    private function getOptionsForSundayDisabled()
    {
        return [
            (object)[
                'From' => '13:00:00',
                'Options' => (object)[
                    'string' => ['Daytime']
                ],
                'To' => '15:30:00'
            ],
            (object)[
                'From' => '18:00:00',
                'Options' => (object)[
                    'string' => ['Evening']
                ],
                'To' => '22:30:00'
            ]
        ];
    }

    private function getOptionsForEveningDisabled()
    {
        return [
            (object)[
                'From' => '13:00:00',
                'Options' => (object)[
                    'string' => ['Daytime']
                ],
                'To' => '15:30:00'
            ],
            (object)[
                'From' => '13:00:00',
                'Options' => (object)[
                    'string' => ['Sunday']
                ],
                'To' => '22:30:00'
            ]
        ];
    }

    private function getOptionsWhenAllAreDisabled()
    {
        return [
            (object)[
                'From' => '13:00:00',
                'Options' => (object)[
                    'string' => ['Daytime']
                ],
                'To' => '15:30:00'
            ]
        ];
    }
}
