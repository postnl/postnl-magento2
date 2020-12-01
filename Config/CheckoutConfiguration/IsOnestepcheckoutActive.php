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

namespace TIG\PostNL\Config\CheckoutConfiguration;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Module\Manager;

/**
 * Some knockout situations work differently on onestepcheckouts.
 * An example is that totals should be recalculated on selecting a delivery option.
 *
 * Class IsOnestepcheckoutActive
 */
class IsOnestepcheckoutActive implements CheckoutConfigurationInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * IsOnestepcheckoutActive constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param Manager              $moduleManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Manager $moduleManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritDoc
     */
    public function getValue()
    {
        if ($this->moduleManager->isEnabled('Onestepcheckout_Iosc') &&
            $this->scopeConfig->getValue('onestepcheckout_iosc/general/enable')
        ) {
            return true;
        }

        if ($this->moduleManager->isEnabled('Amasty_Checkout') &&
            $this->scopeConfig->getValue('amasty_checkout/general/enabled')) {
            return true;
        }

        return false;
    }
}
