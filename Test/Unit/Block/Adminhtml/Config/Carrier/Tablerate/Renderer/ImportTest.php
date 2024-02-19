<?php

namespace TIG\PostNL\Test\Unit\Block\Adminhtml\Config\Carrier\Tablerate\Renderer;

use TIG\PostNL\Block\Adminhtml\Config\Carrier\Tablerate\Renderer\Import;
use TIG\PostNL\Test\TestCase;

class ImportTest extends TestCase
{
    protected $instanceClass = Import::class;

    public function testGetTimeConditionName()
    {
        $conditionName = 'some_condition_name';

        $instance = $this->getInstance();
        $instance->updateTimeConditionName($conditionName);

        $result = $instance->getTimeConditionName();
        $this->assertEquals($conditionName, $result);
    }
}
