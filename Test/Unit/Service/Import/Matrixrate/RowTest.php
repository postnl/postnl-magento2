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

namespace TIG\PostNL\Test\Unit\Service\Import\Matrixrate;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Service\Import\Matrixrate\Row;

class RowTest extends TestCase
{
    public $instanceClass = Row::class;

    /**
     * @var Row
     */
    private $instance;

    public function setUp()
    {
        parent::setUp();

        $store = $this->getMock(\TIG\PostNL\Service\Wrapper\StoreInterface::class);

        $getWebsiteID = $store->method('getWebsiteId');
        $getWebsiteID->willReturn(999);

        $this->instance = $this->getInstance([
            'store' => $store,
        ]);
    }

    public function testThatTheRowIsEmpty()
    {
        $this->instance->process(1, []);

        $this->assertTrue($this->instance->hasErrors());
        $this->assertTrue(in_array('Invalid PostNL matrix rates format in row #%s', $this->instance->getErrors()));
    }
}
