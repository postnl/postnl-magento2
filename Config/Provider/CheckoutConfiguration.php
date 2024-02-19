<?php

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
