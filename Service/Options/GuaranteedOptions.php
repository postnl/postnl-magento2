<?php

namespace TIG\PostNL\Service\Options;

use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Config\Source\Options\GuaranteedOptionsPackages;
use TIG\PostNL\Config\Source\Options\GuaranteedOptionsCargo;

class GuaranteedOptions
{
    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var GuaranteedOptionsPackages
     */
    private $guranteedOptionsPackages;

    /**
     * @var GuaranteedOptionsCargo
     */
    private $guaranteedOptionsCargo;

    /**
     * GuaranteedOptions constructor.
     *
     * @param ShippingOptions           $shippingOptions
     * @param GuaranteedOptionsPackages $guaranteedOptionsPackages
     * @param GuaranteedOptionsCargo    $guaranteedOptionsCargo
     */
    public function __construct(
        ShippingOptions $shippingOptions,
        GuaranteedOptionsPackages $guaranteedOptionsPackages,
        GuaranteedOptionsCargo $guaranteedOptionsCargo
    ) {
        $this->shippingOptions = $shippingOptions;
        $this->guranteedOptionsPackages = $guaranteedOptionsPackages;
        $this->guaranteedOptionsCargo = $guaranteedOptionsCargo;
    }

    /**
     * @return bool
     */
    public function isGuaranteedActive()
    {
        return $this->shippingOptions->isGuaranteedDeliveryActive();
    }

    /**
     * @return array
     */
    public function getCargoTimeOptions()
    {
        return $this->guaranteedOptionsCargo->toOptionArray();
    }

    /**
     * @return array
     */
    public function getPackagesTimeOptions()
    {
        return $this->guranteedOptionsPackages->toOptionArray();
    }
}
