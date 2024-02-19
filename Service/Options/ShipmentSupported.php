<?php

namespace TIG\PostNL\Service\Options;

use TIG\PostNL\Config\Provider\ProductOptions as OptionsProvider;
use TIG\PostNL\Config\Source\Options\ProductOptions;
use TIG\PostNL\Service\Shipment\EpsCountries;
use TIG\PostNL\Service\Shipment\PriorityCountries;
use TIG\PostNL\Service\Validation\CountryShipping;

class ShipmentSupported
{
    /**
     * @var ProductOptions
     */
    private $productOptions;

    /**
     * @var OptionsProvider
     */
    private $optionsProvider;

    /** @var CountryShipping */
    private $countryShipping;

    /**
     * @param ProductOptions       $productOptions
     * @param OptionsProvider      $optionsProvider
     * @param CountryShipping      $countryShipping
     */
    public function __construct(
        ProductOptions $productOptions,
        OptionsProvider $optionsProvider,
        CountryShipping $countryShipping
    ) {
        $this->productOptions  = $productOptions;
        $this->optionsProvider = $optionsProvider;
        $this->countryShipping = $countryShipping;
    }

    /**
     * @param \Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order $order
     *
     * @return array
     */
    public function get($order)
    {
        $address = $order->getShippingAddress();

        return $this->availableOptions($address->getCountryId());
    }

    /**
     * @param $country
     *
     * @return array
     */
    private function availableOptions($country)
    {
        // These are the options selected in the configuration by user.
        $supportedOptions = $this->optionsProvider->getSupportedProductOptions();

        $optionsAllowed = $this->getProductOptionsByCountry($country);

        $availableOptions = array_filter($supportedOptions, function ($value) use ($optionsAllowed) {
            $available = false;
            foreach ($optionsAllowed as $option) {
                $available = ($available || (isset($option['value']) && $option['value'] == $value));
            }

            return $available;
        });

        return $availableOptions;
    }

    // @codingStandardsIgnoreStart
    private function getProductOptionsByCountry($country)
    {
        if ($this->countryShipping->isShippingNLDomestic($country)) {
            $options[] = $this->getProductOptions($country);
        }

        $options[] = $this->getEpsProductOptions($country);

        if (in_array($country, array_merge(PriorityCountries::GLOBALPACK, PriorityCountries::EPS))) {
            $options[] = $this->productOptions->getPriorityOptions();
        }

        if (!in_array($country, EpsCountries::ALL)) {
            $options[] = $this->productOptions->getGlobalPackOptions();
        }

        if ($country != 'NL') {
            $options[] = $this->productOptions->getBoxableOptions();
        }

        $options = call_user_func_array("array_merge", $options);

        return $options;
    }
    // @codingStandardsIgnoreEnd

    /**
     * @param string $country
     *
     * @return array
     */
    // @codingStandardsIgnoreStart
    private function getEpsProductOptions($country)
    {
        $options = [];

        // BE to BE options
        if ($this->countryShipping->isShippingBEDomestic($country)) {
            $options = $this->getBeDomesticOptions();
        }

        // NL to BE options
        if ($this->countryShipping->isShippingNLtoBE($country)) {
            $options[] = $this->getProductOptions($country);
            $options[] = $this->productOptions->getBeOptions();

            // getProductOptions() retrieve ALL options of a country.
            // We don't want BE Domestic options though, so those need to be filtered out of the list.
            $options = call_user_func_array("array_merge", $options);
            $options = array_filter($options, [$this, 'filterBeDomesticOption']);
        }

        // BE to NL options
        if ($this->countryShipping->isShippingBEtoNL($country)) {
            $options[] = $this->productOptions->getBeNlOptions();
            $options = call_user_func_array("array_merge", $options);
            return $options;
        }

        // To NL and other EU countries
        if ($country !== 'BE' && in_array($country, EpsCountries::ALL)) {
            $options = $this->productOptions->getEpsProductOptions();
        }

        return $options;
    }
    // @codingStandardsIgnoreEnd

    /**
     * @return array
     */
    private function getBeDomesticOptions()
    {
        $beDomesticOptions[] = $this->productOptions->getBeDomesticOptions();
        $beDomesticOptions[] = $this->productOptions->getPakjeGemakBeDomesticOptions();

        // @codingStandardsIgnoreLine
        $beDomesticOptions = call_user_func_array("array_merge", $beDomesticOptions);

        return $beDomesticOptions;
    }

    /**
     * @param $productOption
     *
     * @return bool
     */
    private function filterBeDomesticOption($productOption)
    {
        $isNotBeDomestic = true;
        $beDomesticOptions = $this->getBeDomesticOptions();

        foreach ($beDomesticOptions as $domesticOption) {
            if ($productOption['value'] === $domesticOption['value']) {
                $isNotBeDomestic = false;
                break;
            }
        }

        return $isNotBeDomestic;
    }

    /**
     * @param $country
     *
     * @return array
     */
    private function getProductOptions($country)
    {
        $options = $this->productOptions->get();

        return array_filter($options, function ($value) use ($country) {
            return ($value['countryLimitation'] == $country);
        });
    }
}
