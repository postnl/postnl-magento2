<?php

namespace TIG\PostNL\Config\Provider;

class PostNLConfiguration extends AbstractConfigProvider
{
    const XPATH_STABILITY = 'tig_postnl/stability';
    const XPATH_TESTED_MAGENTO_VERSION = 'tig_postnl/tested_magento_version';

    /**
     * @param null $store
     *
     * @return string
     */
    public function getStability($store = null)
    {
        return $this->getConfigFromXpath(static::XPATH_STABILITY, $store);
    }

    /**
     * @param null $store
     *
     * @return string
     */
    public function getSupportedMagentoVersions($store = null)
    {
        return $this->getConfigFromXpath(static::XPATH_TESTED_MAGENTO_VERSION, $store);
    }
}
