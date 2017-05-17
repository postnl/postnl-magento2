<?php
/**
 *
 *          ..::..
 *     ..::::::::::::..
 *   ::'''''':''::'''''::
 *   ::..  ..:  :  ....::
 *   ::::  :::  :  :   ::
 *   ::::  :::  :  ''' ::
 *   ::::..:::..::.....::
 *     ''::::::::::::''
 *          ''::''
 *
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
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

    public function testTheModuleIsConfiguredAndEnabledInTheTestEnvironment()
    {
        /** @var ModuleList $moduleList */
        $moduleList = $this->getObject(ModuleList::class);

        $this->assertTrue($moduleList->has($this->moduleName));
    }

    public function testTheModuleIsConfiguredAndEnabledInTheRealEnvironment()
    {
        $dirList = $this->getObject(DirectoryList::class, ['root' => BP]);
        $configReader = $this->getObject(DeploymentConfigReader::class, ['dirList' => $dirList]);
        $deploymentConfig = $this->getObject(DeploymentConfig::class, ['reader' => $configReader]);

        /** @var ModuleList $moduleList */
        $moduleList = $this->getObject(ModuleList::class, ['config' => $deploymentConfig]);

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
            [4950],
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
        $value = $configProvider->getSupportedProductOptions();
        $productOptions = explode(',', $value);

        $this->assertContains($productCode, $productOptions);
    }

    /**
     * @return array
     */
    public function nonDefaultSupportedOptionsProvider()
    {
        return [
            [3628],
            [3629],
            [3653],
            [3783],
            [3790],
            [3791],
            [3792],
            [3793],
        ];
    }

    /**
     * @param $productCode
     *
     * @dataProvider nonDefaultSupportedOptionsProvider
     */
    public function testNonDefaultSupportedOptions($productCode)
    {
        /** @var ProductOptions $configProvider */
        $configProvider = $this->getObject(ProductOptions::class);
        $value = $configProvider->getSupportedProductOptions();
        $productOptions = explode(',', $value);

        $this->assertNotContains($productCode, $productOptions);
    }

    public function testGetDefaultExtraAtHomeProductOption()
    {
        /** @var ProductOptions $configProvider */
        $configProvider = $this->getObject(ProductOptions::class);
        $value = $configProvider->getDefaultExtraAtHomeProductOption();

        $this->assertEquals(3628, $value);
    }
}
