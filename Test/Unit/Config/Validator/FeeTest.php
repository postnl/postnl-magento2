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
namespace TIG\PostNL\Test\Unit\Config\Validator;

use TIG\PostNL\Config\Validator\Fee;
use TIG\PostNL\Test\TestCase;

class FeeTest extends TestCase
{
    public $instanceClass = Fee::class;

    public function theConfigInputIsProcessedOkProvider()
    {
        return [
            ['2,5', '2.5'],
            ['2,0', '2.0'],
            [' ', ''],
        ];
    }

    /**
     * @dataProvider theConfigInputIsProcessedOkProvider
     *
     * @param $input
     * @param $output
     */
    public function testTheConfigInputIsProcessedOk($input, $output)
    {
        /** @var Fee $instance */
        $instance = $this->getInstance();
        $instance->setValue($input);
        $instance->beforeSave();

        $this->assertEquals($output, $instance->getValue());
    }
}
