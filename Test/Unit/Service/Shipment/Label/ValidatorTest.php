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
namespace TIG\PostNL\Test\Unit\Service\Shipment\Label;

use TIG\PostNL\Service\Shipment\Label\Validator;
use TIG\PostNL\Test\TestCase;

class ValidatorTest extends TestCase
{
    public $instanceClass = Validator::class;

    public function validateProvider()
    {
        return [
            [
                'input' => [
                    [
                        'labels' => [
                            [],
                            'asdf1',
                        ]
                    ],
                    [
                        'labels' => [
                            [],
                        ]
                    ],
                    [
                        'labels' => [
                            'asdf2',
                        ]
                    ]
                ],
                'output' => [
                    [
                        'labels' => [
                            'asdf1',
                        ]
                    ],
                    [
                        'labels' => [
                            'asdf2',
                        ]
                    ],
                ]
            ],
            [
                'input' => [
                    [
                        'labels' => [
                            [],
                            'Invalid response',
                        ]
                    ],
                ],
                'output' => [
                ]
            ],
        ];
    }

    /**
     * @dataProvider validateProvider
     *
     * @param $input
     * @param $expected
     */
    public function testValidate($input, $expected)
    {
        /** @var Validator $instance */
        $instance = $this->getInstance();
        $result = $instance->validate($input);

        $this->assertEquals($expected, $result);
    }
}
