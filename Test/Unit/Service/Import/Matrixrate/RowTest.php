<?php

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

    public function setUp() : void
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
        $this->instance->process(1, [], 1);

        $this->assertTrue($this->instance->hasErrors());
        $this->assertTrue(in_array('Invalid PostNL matrix rates format in row #%s', $this->instance->getErrors()));
    }
}
