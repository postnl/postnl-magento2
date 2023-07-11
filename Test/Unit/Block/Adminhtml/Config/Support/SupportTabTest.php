<?php

namespace TIG\PostNL\Unit\Test\Block\Adminhtml\Config\Support;

use TIG\PostNL\Block\Adminhtml\Config\Support\SupportTab;
use TIG\PostNL\Config\Provider\PostNLConfiguration;
use TIG\PostNL\Test\TestCase;

class SupportTabTest extends TestCase
{
    public $instanceClass = SupportTab::class;

    public function testGetSupportedMagentoVersions()
    {
        $configuration = $this->getConfiguration('getSupportedMagentoVersions', '9.9.9');

        /** @var PostNLConfiguration $instance */
        $instance = $this->getInstance([
            'configuration' => $configuration,
        ]);

        $result = $instance->getSupportedMagentoVersions();

        $this->assertEquals('9.9.9', $result);
    }

    public function getStabilityProvider()
    {
        return [
            'null' => [null, ''],
            'stable' => ['stable', ''],
            'beta' => ['beta', ' - Beta'],
            'alpha' => ['alpha', ' - Alpha'],
        ];
    }

    /**
     * @param $input
     * @param $expected
     *
     * @dataProvider getStabilityProvider
     */
    public function testGetStability($input, $expected)
    {
        $configuration = $this->getConfiguration('getStability', $input);

        /** @var PostNLConfiguration $instance */
        $instance = $this->getInstance([
            'configuration' => $configuration,
        ]);

        $result = $instance->getStability();

        $this->assertEquals($expected, $result);
    }

    /**
     * @param $method
     * @param $return
     *
     * @return PostNLConfiguration
     */
    private function getConfiguration($method, $return)
    {
        $configuration = $this->getFakeMock(PostNLConfiguration::class);
        $configuration->setMethods([$method]);
        $configuration = $configuration->getMock();

        $expects = $configuration->expects($this->once());
        $expects->method($method);
        $expects->willReturn($return);

        return $configuration;
    }
}
