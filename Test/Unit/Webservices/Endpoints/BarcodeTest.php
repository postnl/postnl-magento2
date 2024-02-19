<?php

namespace TIG\PostNL\Test\Unit\Webservices\Endpoints;

use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Webservices\Endpoints\Barcode;

class BarcodeTest extends TestCase
{
    public $instanceClass = Barcode::class;

    public function testShouldThrowAnExceptionWhenNoProductCodeIsset()
    {
        $this->expectException(\TIG\PostNL\Exception::class);
        $this->expectExceptionMessage("Please provide the productcode first by calling setProductCode");
        /** @var Barcode $instance */
        $this->getInstance()->call();
    }
}
