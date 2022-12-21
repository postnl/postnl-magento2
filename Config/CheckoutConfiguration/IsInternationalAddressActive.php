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
 * to support@tig.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact support@tig.nl for more information.
 *
 * @copyright   Copyright (c) Total Internet Group B.V. https://tig.nl/copyright
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Config\CheckoutConfiguration;

use TIG\PostNL\Config\Provider\Webshop;

class IsInternationalAddressActive implements CheckoutConfigurationInterface
{
    /**
     * @var Webshop
     */
    private $webshopConfig;

    /**
     * IsInternationalAddressActive constructor
     *
     * @param Webshop $webshop
     */
    public function __construct(
        Webshop $webshop
    ) {
        $this->webshopConfig = $webshop;
    }

    /**
     * @return bool
     */
    public function getValue()
    {
        return (bool) $this->webshopConfig->getIsInternationalAddressEnabled() && $this->webshopConfig->getIsAddressCheckEnabled();
    }
}
