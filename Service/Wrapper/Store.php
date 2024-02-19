<?php

namespace TIG\PostNL\Service\Wrapper;

use Magento\Store\Model\StoreManagerInterface;

class Store implements StoreInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }

    /**
     * See : https://github.com/magento/magento2/issues/9741
     * Website ID can not be retrieved form the storeManger.
     * This will always returns the website thats stated as default.
     *
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->getStore()->getWebsiteId();
    }
}
