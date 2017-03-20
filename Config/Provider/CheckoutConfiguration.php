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

use Magento\Checkout\Model\ConfigProviderInterface;
use TIG\PostNL\Config\CheckoutConfiguration\CheckoutConfigurationInterface;
use TIG\PostNL\Exception as PostNLException;

class CheckoutConfiguration implements ConfigProviderInterface
{
    /**
     * @var array
     */
    private $shippingConfiguration;

    /**
     * @param CheckoutConfigurationInterface[] $shippingConfiguration
     */
    public function __construct(
        $shippingConfiguration = []
    ) {
        $this->shippingConfiguration = $shippingConfiguration;
    }

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     * @throws PostNLException
     */
    public function getConfig()
    {
        $shipping = [];

        foreach ($this->shippingConfiguration as $key => $configuration) {
            $this->checkImplementation($configuration, $key);

            $shipping[$key] = $configuration->getValue();
        }

        return [
            'shipping' => [
                'postnl' => $shipping,
            ]
        ];
    }

    /**
     * @param $configuration
     * @param $key
     *
     * @throws PostNLException
     */
    private function checkImplementation($configuration, $key)
    {
        if (!($configuration instanceof CheckoutConfigurationInterface)) {
            // @codingStandardsIgnoreLine
            throw new PostNLException(__($key . ' is not an implementation of CheckoutConfigurationInterface'));
        }
    }
}
