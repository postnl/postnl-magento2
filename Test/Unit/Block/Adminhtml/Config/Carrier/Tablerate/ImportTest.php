<?php

namespace TIG\PostNL\Test\Unit\Block\Adminhtml\Config\Carrier\Tablerate;

use Magento\Framework\Data\Form;

use TIG\PostNL\Block\Adminhtml\Config\Carrier\Tablerate\Import;
use TIG\PostNL\Block\Adminhtml\Config\Carrier\Tablerate\Renderer\Import as ImportBlock;
use TIG\PostNL\Test\TestCase;

class ImportTest extends TestCase
{
    protected $instanceClass = Import::class;

    public function testGetElementHtml()
    {
        $formMock = $this->getFakeMock(Form::class)->getMock();

        $instance = $this->getInstance();
        $instance->setForm($formMock);

        $result = $instance->getElementHtml();

        $this->assertIsString($result);
    }
}
