<?php

namespace TIG\PostNL\Service\Timeframe\Filters\Options;

use TIG\PostNL\Service\Timeframe\Filters\OptionsFilterInterface;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Helper\AddressEnhancer;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Evening implements OptionsFilterInterface
{
    const TIMEFRAME_OPTION_EVENING = 'Evening';

    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var AddressEnhancer
     */
    private $addressEnhancer;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ShippingOptions      $shippingOptions
     * @param AddressEnhancer      $addressEnhancer
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ShippingOptions $shippingOptions,
        AddressEnhancer $addressEnhancer,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->shippingOptions = $shippingOptions;
        $this->addressEnhancer = $addressEnhancer;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param array|object $options
     *
     * @return array
     */
    public function filter($options)
    {
        $filterdOptions = array_filter($options, [$this, 'canDeliver']);
        return array_values($filterdOptions);
    }

    /**
     * @param $option
     *
     * @return bool
     */
    public function canDeliver($option)
    {
        $option = $option->Options;
        if (!isset($option->string[0])) {
            return false;
        }

        $result = false;

        foreach ($option->string as $string) {
            if ($string !== static::TIMEFRAME_OPTION_EVENING) {
                $result = true;
            }

            if ($string === static::TIMEFRAME_OPTION_EVENING
                && $this->shippingOptions->isEveningDeliveryActive($this->getCountryId())) {
                $option->validatedType = $string;
                $result = true;
            }
        }

        return $result;
    }

    /**
     * @return mixed
     */
    private function getCountryId()
    {
        $countryId = $this->scopeConfig->getValue('general/store_information/country_id');
        $address   = $this->addressEnhancer->get();
        if ($address && isset($address['country'])) {
            $countryId = $address['country'];
        }

        return $countryId;
    }
}
