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
namespace TIG\PostNL\Config\Provider;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Encryption\Encryptor;

abstract class AbstractConfigProvider
{
    /**
     * @var ScopeConfigInterface
     */
    // @codingStandardsIgnoreLine
    protected $scopeConfig;

    /**
     * @var Encryptor
     */
    // @codingStandardsIgnoreLine
    protected $crypt;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Encryptor            $crypt
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Encryptor $crypt
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->crypt = $crypt;
    }

    /**
     * Get Config value with xpath
     *
     * @param      $xpath
     * @param null $store
     *
     * @return mixed
     */
    // @codingStandardsIgnoreLine
    protected function getConfigFromXpath($xpath, $store = null)
    {
        return $this->scopeConfig->getValue(
            $xpath,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
