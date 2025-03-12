<?php

namespace TIG\PostNL\Service\Module;

use Magento\Framework\App\CacheInterface;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Component\ComponentRegistrarInterface;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use TIG\PostNL\Block\Adminhtml\Config\Support\SupportTab;

class Version
{
    private DeploymentConfig $deploymentConfig;
    private ComponentRegistrarInterface $componentRegistrar;
    private ReadFactory $readFactory;
    private ProductMetadataInterface $productMetadata;
    private CacheInterface $cache;
    private $headers = null;

    private array $possibleCheckouts = [
        'Amasty_Checkout',
        'Mageplaza_Osc',
        'Bss_OneStepCheckout',
        'Onestepcheckout_Iosc',
        'Hyva_Checkout',
        'PostNL_HyvaCheckout'
    ];

    public function __construct(
        DeploymentConfig $deploymentConfig,
        ComponentRegistrarInterface $componentRegistrar,
        ReadFactory $readFactory,
        ProductMetadataInterface $productMetadata,
        CacheInterface $cache
    ) {
        $this->deploymentConfig = $deploymentConfig;
        $this->componentRegistrar = $componentRegistrar;
        $this->readFactory = $readFactory;
        $this->cache = $cache;
        $this->productMetadata = $productMetadata;
    }

    public function getModuleVersion($moduleName): string
    {
        try {
            $path = $this->componentRegistrar->getPath(
                \Magento\Framework\Component\ComponentRegistrar::MODULE,
                $moduleName
            );
            $directoryRead = $this->readFactory->create($path);
            $composerJsonData = $directoryRead->readFile('composer.json');
            $data = json_decode($composerJsonData, false, 10, JSON_THROW_ON_ERROR);

            return !empty($data->version) ? $data->version : '';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    public function getCachedVersions(): array
    {
        if (is_array($this->headers)) {
            return $this->headers;
        }
        $cacheKey = 'POSTNL_VERSION_HEADERS';
        $content = $this->cache->load($cacheKey);
        if ($content) {
            try {
                $data = \json_decode($content, false, 10, JSON_THROW_ON_ERROR);
                if (is_array($data)) {
                    $this->headers = $data;
                    return $data;
                }
            } catch (\Exception $e) {
                // walk through modules
            }
        }
        $this->headers = $this->collectVersions();
        $this->cache->save(\json_encode($this->headers), $cacheKey, ['POSTNL_REQUESTS'], 60 * 60 * 6);
        return $this->headers;
    }

    public function collectVersions(): array
    {
        $headers[] = 'Magento:' . $this->productMetadata->getVersion();
        $headers[] = 'TIG_PostNL:' . SupportTab::POSTNL_VERSION;

        $modules = $this->deploymentConfig->get('modules');
        foreach ($this->possibleCheckouts as $moduleKey) {
            if (isset($modules[$moduleKey]) && $modules[$moduleKey] === 1) {
                $headers[] = $moduleKey. ':' .  $this->getModuleVersion($moduleKey);
            }
        }
        return $headers;
    }
}
