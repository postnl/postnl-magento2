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

namespace TIG\PostNL\Test\Unit\Service\Validation;

use TIG\PostNL\Test\TestCase;

class DuplicateImportTest extends TestCase
{
    public $instanceClass = \TIG\PostNL\Service\Validation\DuplicateImport::class;

    public function testItShouldReturnTrueOnUniqueItems()
    {
        $instance = $this->getInstance();
        foreach (range('a', 'z') as $letter) {
            $this->assertTrue($instance->validate(['a', 'a', 'a', 'a', 'a', 'a', $letter]));
        }
    }

    public function testItShouldThrowAnExceptionOnAnInvalidLengthArray()
    {
        try {
            $this->getInstance()->validate(['not enought elements']);
        } catch (\TIG\PostNL\Exception $exception) {
            $message = 'The array to validate is expected to have 7 elements, you only have 1';
            $this->assertEquals($message, $exception->getMessage());
            return;
        }

        $this->fail('We expected an exception to be thrown, but we got none');
    }

    public function testTheReturnValueForDuplicateImports()
    {
        $instance = $this->getInstance();
        $elements = ['a row', 'with', 'enough', 'elements', 'with a', 'length of', '7'];

        $result = $instance->validate($elements);
        $this->assertTrue($result);

        $result = $instance->validate($elements);
        $this->assertFalse($result);
    }
}
