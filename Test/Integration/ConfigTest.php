<?php

namespace TIG\PostNL\Test\Integration;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\DeploymentConfig\Reader as DeploymentConfigReader;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Module\ModuleList;
use TIG\PostNL\Config\Provider\ProductOptions;

class ConfigTest extends TestCase
{
    protected $moduleName = 'TIG_PostNL';

    public function testModuleIsRegistered()
    {
        $registrar = new ComponentRegistrar();
        $modulesList = $registrar->getPaths(ComponentRegistrar::MODULE);

        $this->assertArrayHasKey($this->moduleName, $modulesList);
    }

    public function testTheModuleIsConfiguredAndEnabled()
    {
        /** @var ModuleList $moduleList */
        $moduleList = $this->getObject(ModuleList::class);

        $this->assertTrue($moduleList->has($this->moduleName));
    }

    public function defaultSupportedOptionsProvider()
    {
        return [
            [3085],
            [3189],
            [3089],
            [3389],
            [3096],
            [3090],
            [3385],
            [3534],
            [3544],
            [3533],
            [3543],
            [4952],
            [2928],
        ];
    }

    /**
     * @param $productCode
     *
     * @dataProvider defaultSupportedOptionsProvider
     */
    public function testDefaultSupportedOptions($productCode)
    {
        /** @var ProductOptions $configProvider */
        $configProvider = $this->getObject(ProductOptions::class);
        $productOptions = $configProvider->getSupportedProductOptions();

        $this->assertContains($productCode, $productOptions);
    }

    public function testGetDefaultExtraAtHomeProductOption()
    {
        /** @var ProductOptions $configProvider */
        $configProvider = $this->getObject(ProductOptions::class);
        $value = $configProvider->getDefaultExtraAtHomeProductOption();

        $this->assertEquals(3628, $value);
    }
}
