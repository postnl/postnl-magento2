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
use TIG\PostNL\Service\Validation;

class FactoryTest extends TestCase
{
    public $instanceClass = Validation\Factory::class;

    /**
     * @var Validation\Factory
     */
    public $instance;

    public function setUp()
    {
        parent::setUp();

        $this->instance = $this->getInstance([
            'validators' => [
                'decimal' => $this->getObject(Validation\Decimal::class),
                'parcelType' => $this->getObject(Validation\ParcelType::class),
                'duplicateImport' => $this->getObject(Validation\DuplicateImport::class),
            ]
        ]);
    }

    public function testResetCallsTheResetMethodOfTheValidorIfAvailable()
    {
        $validatorInstance = $this->getFakeMock(\TIG\PostNL\Service\Validation\ContractInterface::class);
        $validatorInstance->setMethods(['validate', 'resetData']);
        $validatorInstance = $validatorInstance->getMock();

        $reset = $validatorInstance->expects($this->once());
        $reset->method('resetData');

        /** @var Validation\Factory $instance */
        $instance = $this->getInstance([
            'validators' => [
                'mockValidator' => $validatorInstance,
            ]
        ]);

        $instance->resetData();
    }

    public function testResetIsNotCalledWhenItDoesNotExists()
    {
        $validatorInstance = $this->getMock(\TIG\PostNL\Service\Validation\ContractInterface::class);

        /** @var Validation\Factory $instance */
        $instance = $this->getInstance([
            'validators' => [
                'mockValidator' => $validatorInstance,
            ]
        ]);

        $instance->resetData();
    }

    public function testIncorrectClassThrowsAnException()
    {
        $instance = $this->getInstance([
            'validators' => [
                'class' => static::class,
            ]
        ]);

        try {
            $instance->validate('type', 'value');
        } catch (\TIG\PostNL\Exception $exception) {
            $message = 'Class is not an implementation of ' . Validation\ContractInterface::class;
            $this->assertEquals($message, $exception->getMessage());
            return;
        }

        $this->fail('We added an invalid class and expected an Exception, but got none.');
    }

    public function testANonExistingValidatorThrowsAnExceptions()
    {
        try {
            $this->instance->validate('non-existing', 'some value');
        } catch (\TIG\PostNL\Exception $exception) {
            $message = 'There is no implementation found for the "non-existing" validator';
            $this->assertEquals($message, $exception->getMessage());
            return;
        }

        $this->fail('We expected an exception to be thrown, but got none');
    }

    public function testWeightValidator()
    {
        $this->assertSame(1.333, $this->instance->validate('weight', '1.333'));
    }

    public function testSubtotalValidator()
    {
        $this->assertSame(190.0, $this->instance->validate('subtotal', '190'));
    }

    public function testQuantityValidator()
    {
        $this->assertSame(10.55, $this->instance->validate('quantity', '10.55'));
    }

    public function testPriceValidator()
    {
        $this->assertSame(10.55, $this->instance->validate('price', '10.55'));
    }

    public function testParcelTypeValidator()
    {
        $this->assertSame('regular', $this->instance->validate('parcel-type', 'regular'));
    }

    public function testDuplicateImportValidator()
    {
        $elements = ['a row', 'with', 'enough', 'elements', 'with a', 'length of', '7'];
        $this->assertTrue($this->instance->validate('duplicate-import', $elements));
        $this->assertFalse($this->instance->validate('duplicate-import', $elements));
    }
}
