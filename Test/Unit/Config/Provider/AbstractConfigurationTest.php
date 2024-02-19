<?php

namespace TIG\PostNL\Test\Unit\Config\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use TIG\PostNL\Config\Provider\DefaultConfiguration;
use TIG\PostNL\Config\Provider\PostNLConfiguration;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Config\Provider\ProductOptions;
use TIG\PostNL\Config\Provider\Globalpack;
use TIG\PostNL\Config\Provider\LoggingConfiguration;
use TIG\PostNL\Config\Provider\Webshop;
use TIG\PostNL\Test\TestCase;
use \PHPUnit\Framework\MockObject\Stub\ConsecutiveCalls;

abstract class AbstractConfigurationTest extends TestCase
{
    /**
     * @var ScopeConfigInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $scopeConfigMock;

    protected function initScopeConfigMock()
    {
        $this->scopeConfigMock = $this->getFakeMock(ScopeConfigInterface::class)->getMock();
    }

    /**
     * @param array $args
     *
     * @return DefaultConfiguration|PostNLConfiguration|ShippingOptions|ProductOptions|Globalpack|LoggingConfiguration|Webshop
     */
    public function getInstance(array $args = [])
    {
        $this->initScopeConfigMock();

        $args['scopeConfig'] = $this->scopeConfigMock;

        return parent::getInstance($args);
    }

    /**
     * @param      $xpath
     * @param      $value
     * @param null $storeId
     * @param null $matcher
     */
    protected function setXpath($xpath, $value, $storeId = null, $matcher = null)
    {
        if ($matcher === null) {
            $matcher = $this->once();
        }

        $getValueExpects = $this->scopeConfigMock->expects($matcher);
        $getValueExpects->method('getValue');
        $getValueExpects->with(
            $xpath,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $getValueExpects->willReturn($value);
    }

    /**
     * @param array $xpaths
     * @param array $returns
     */
    protected function setXpathConsecutive($xpaths = [], $returns = [])
    {
        $this->assertEquals(count($xpaths), count($returns), "setXPath needs returns and paths to be of equal length");

        $getValueExpects = $this->scopeConfigMock->expects($this->any());
        $getValueExpects->method('getValue');
        $getValueExpects->withConsecutive(...$xpaths);
        $getValueExpects->willReturnOnConsecutiveCalls(...$returns);
    }
}
