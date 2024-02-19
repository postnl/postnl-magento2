<?php

namespace TIG\PostNL\Config\CheckoutConfiguration;

use Magento\Framework\UrlInterface;

class Urls implements CheckoutConfigurationInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    public function getValue()
    {
        return [
            'deliveryoptions_timeframes' => $this->getUrl('postnl/deliveryoptions/timeframes'),
            'deliveryoptions_locations'  => $this->getUrl('postnl/deliveryoptions/locations'),
            'deliveryoptions_save'       => $this->getUrl('postnl/deliveryoptions/save'),
            'pakjegemak_address'         => $this->getUrl('postnl/pakjegemak/address'),
            'address_postcode'           => $this->getUrl('postnl/address/postcode'),
            'international_address'      => $this->getUrl('postnl/international/address')
        ];
    }

    /**
     * @param $path
     *
     * @return string
     */
    private function getUrl($path)
    {
        return $this->urlBuilder->getUrl($path, ['_secure' => true]);
    }
}
