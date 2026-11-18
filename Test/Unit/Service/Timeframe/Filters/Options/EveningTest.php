<?php

namespace TIG\PostNL\Unit\Service\Timeframe\Filters\Options;

use Exception;
use TIG\PostNL\Test\TestCase;
use TIG\PostNL\Service\Timeframe\Filters\Options\Evening;
use TIG\PostNL\Config\Provider\ShippingOptions;

class EveningTest extends TestCase
{
    protected $instanceClass = Evening::class;

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\Timeframes\Options\DataProvider::evening
     *
     * @param $isEnabled
     * @param $options
     * @param $expected
     *
     * @throws Exception
     */
    #[\PHPUnit\Framework\Attributes\DataProviderExternal(\TIG\PostNL\Test\Fixtures\Timeframes\Options\DataProvider::class, 'evening')]
    public function testFilter($isEnabled, $options, $expected)
    {
        $shippingOptions = $this->getFakeMock(ShippingOptions::class)->getMock();
        $expects = $shippingOptions->expects($this->once());
        $expects->method('isEveningDeliveryActive');
        $expects->willReturn($isEnabled);

        $scopeConfig = $this->getMock(\Magento\Framework\App\Config\ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturn('NL');
        $instance = $this->getInstance(['shippingOptions' => $shippingOptions, 'scopeConfig' => $scopeConfig]);
        $result   = $instance->filter($options);

        if ($isEnabled) {
            $expected[1]->Options->validatedType = 'Evening';
        }
        $this->assertEquals($expected, $result);
    }
}
