<?php

namespace TIG\PostNL\Service\Wrapper;

interface StoreInterface
{
    /**
     * @return \Magento\Store\Api\Data\StoreInterface
     */
    public function getStore();

    /**
     * @return int
     */
    public function getWebsiteId();
}
