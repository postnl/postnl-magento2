<?php
declare(strict_types=1);

namespace TIG\PostNL\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\Writer;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use TIG\PostNL\Model\Config;

class Enabled extends Value
{
    private Writer $configWriter;

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource,
        AbstractDb $resourceCollection,
        Writer $configWriter,
        array $data = []
    ) {
        $this->configWriter = $configWriter;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }

    public function afterSave()
    {
        $value = parent::afterSave();

        if ($value->getValue() === '0') {
            $this->configWriter->delete(
                Config::CONFIG_PATH_ENABLED_IN_CART,
                $value->getScope(),
                $value->getScopeId()
            );
            $this->configWriter->delete(
                Config::CONFIG_PATH_ENABLED_IN_CHECKOUT,
                $value->getScope(),
                $value->getScopeId()
            );
            $this->configWriter->delete(
                Config::CONFIG_PATH_ENABLED_IN_MINICART,
                $value->getScope(),
                $value->getScopeId()
            );
        }

        return $value;
    }
}
