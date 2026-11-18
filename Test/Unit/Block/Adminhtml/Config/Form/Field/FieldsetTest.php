<?php

namespace TIG\PostNL\Test\Unit\Block\Adminhtml\Config\Form\Field;

use TIG\PostNL\Block\Adminhtml\Config\Form\Field\Fieldset;
use Magento\Backend\Block\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use TIG\PostNL\Test\TestCase;

class FieldsetTest extends TestCase
{
    protected $instanceClass = Fieldset::class;

    /**
     * @return array
     */
    public static function fieldProvider()
    {
        return [
            'inactive' => [
                '0',
                'modus_off'
            ],
            'live modus' => [
                '1',
                'modus_live'
            ],
            'test modus' => [
                '2',
                'modus_test'
            ]
        ];
    }

    /**
     * @param $configValue
     * @param $expected
     * @dataProvider fieldProvider
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('fieldProvider')]
    public function testGetFrontendClass($configValue, $expected)
    {
        $elementMock = $this->getFakeMock(AbstractElement::class)->getMock();
        $requestMock = $this->getFakeMock(RequestInterface::class)->getMock();

        $scopeConfigMock = $this->getFakeMock(ScopeConfigInterface::class)->getMock();
        $scopeConfigMock->expects($this->once())->method('getValue')->willReturn($configValue);

        $contextMock = $this->getFakeMock(Context::class)->onlyMethods(['getScopeConfig', 'getRequest'])->getMock();
        $contextMock->expects($this->once())->method('getScopeConfig')->willReturn($scopeConfigMock);
        $contextMock->expects($this->once())->method('getRequest')->willReturn($requestMock);

        $instance = $this->getInstance(['context' => $contextMock]);
        $result   = $this->invokeArgs('_getFrontendClass', [$elementMock], $instance);
        $this->assertStringContainsString($expected, $result);
    }
}
