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
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
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
