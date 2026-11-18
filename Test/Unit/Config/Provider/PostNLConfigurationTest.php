<?php

namespace TIG\PostNL\Test\Unit\Config\Provider;

use TIG\PostNL\Config\Provider\PostNLConfiguration;

class PostNLConfigurationTest extends AbstractConfiguration
{
    protected $instanceClass = PostNLConfiguration::class;

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::randomWordsProvider
     *
     * @param $value
     */
    #[\PHPUnit\Framework\Attributes\DataProviderExternal(\TIG\PostNL\Test\Fixtures\DataProvider::class, 'randomWordsProvider')]
    public function testGetStability($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(PostNLConfiguration::XPATH_STABILITY, $value);
        $this->assertEquals($value, $instance->getStability());
    }

    /**
     * @dataProvider \TIG\PostNL\Test\Fixtures\DataProvider::randomWordsProvider
     *
     * @param $value
     */
    #[\PHPUnit\Framework\Attributes\DataProviderExternal(\TIG\PostNL\Test\Fixtures\DataProvider::class, 'randomWordsProvider')]
    public function testGetSupportedMagentoVersions($value)
    {
        $instance = $this->getInstance();
        $this->setXpath(PostNLConfiguration::XPATH_TESTED_MAGENTO_VERSION, $value);
        $this->assertEquals($value, $instance->getSupportedMagentoVersions());
    }
}
