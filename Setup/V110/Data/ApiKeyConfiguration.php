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
namespace TIG\PostNL\Setup\V110\Data;

use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Encryption\Encryptor;
use TIG\PostNL\Setup\AbstractDataInstaller;

class ApiKeyConfiguration extends AbstractDataInstaller
{
    /**
     * @var Config
     */
    private $resourceConfig;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Encryptor
     */
    private $encryptor;

    public function __construct(
        Config $resourceConfig,
        ScopeConfigInterface $scopeConfig,
        Encryptor $encryptor
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->resourceConfig = $resourceConfig;
        $this->encryptor = $encryptor;
    }

    /**
     * The api key is normally encrypted before it gets saved in the database. But for testing purposes we want to add
     * a default API key in the test configuration. If we put this in the config.xml the system will try to decrypt
     * an plain text value, resulting in an invalid api key. That's why we read the default value, encrypt it, and
     * save it to the database.
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface   $context
     *
     * @return void
     */
    // @codingStandardsIgnoreLine
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $originalValue = $this->scopeConfig->getValue('tig_postnl/generalconfiguration_extension_status/api_key_test');
        $encryptedValue = $this->encryptor->encrypt($originalValue);

        $this->resourceConfig->saveConfig(
            'tig_postnl/generalconfiguration_extension_status/api_key_test',
            $encryptedValue,
            'default',
            0
        );
    }
}
