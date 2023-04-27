<?php

namespace TIG\PostNL\Setup\Patch\Data;

use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoresConfig;

/**
 * Class \Magento\Bundle\Setup\Patch\ApplyAttributesUpdate
 *
 * Remove the default BE option if it's one of the removed options
 */
class ResetBEDefaultOption implements DataPatchInterface
{
    const DEFAULT_BE_OPTION_PATH = 'tig_postnl/delivery_settings/default_be_option';

    private $removedOptions = [
        4944,
        4952,
        4938,
        4940,
        4950,
        4983,
        4985
    ];

    /**
     * @var StoresConfig
     */
    private $storesConfig;

    /**
     * @var Config
     */
    private $configWriter;

    /**
     *
     * UpdateDisableDeliveryDaysAttribute constructor.
     *
     * @param StoresConfig $storesConfig
     * @param Config       $configWriter
     */
    public function __construct(
        StoresConfig $storesConfig,
        Config $configWriter
    ) {
        $this->storesConfig = $storesConfig;
        $this->configWriter = $configWriter;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $savedOptions = $this->storesConfig->getStoresConfigByPath(self::DEFAULT_BE_OPTION_PATH);

        array_walk($savedOptions, function ($option, $scopeId) {
            if (in_array($option, $this->removedOptions)) {
                $this->configWriter->deleteConfig(
                    self::DEFAULT_BE_OPTION_PATH
                );
            }
        });

        return $this;
    }
}
