<?php

namespace TIG\PostNL\Config\Source\Options;

use Magento\Framework\Data\OptionSourceInterface;
use TIG\PostNL\Config\Provider\AbstractConfigProvider;
use TIG\PostNL\Config\Provider\ShippingOptions;

/**
 * @todo we need to move the getOption-methods to the ProductOptions class without
 *      creating a circular dependency. For now we allow CS to ignore this file.
 */
// @codingStandardsIgnoreFile
class DefaultOptions implements OptionSourceInterface
{
    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var ProductOptions
     */
    private $productOptions;

    /**
     * DefaultOptions constructor.
     *
     * @param ShippingOptions $shippingOptions
     * @param ProductOptions  $productOptions
     */
    public function __construct(
        ShippingOptions $shippingOptions,
        ProductOptions $productOptions
    ) {
        $this->shippingOptions = $shippingOptions;
        $this->productOptions = $productOptions;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $flags = [];
        $flags['groups'][] = ['group' => 'standard_options'];
        $flags['groups'][] = ['group' => 'buspakje_options'];
        $flags['groups'][] = ['group' => 'only_stated_address_options'];
        if ($this->shippingOptions->isIDCheckActive()) {
            $flags['groups'][] = ['group' => 'id_check_options'];
        }

        if ($this->shippingOptions->canUseCargoProducts()) {
            $flags['groups'][] = ['group' => 'cargo_options'];
        }

        if ($this->shippingOptions->canUseEpsBusinessProducts()) {
            $flags['groups'][] = ['group' => 'eps_package_options'];
        }

        if ($this->shippingOptions->canUseBeProducts()) {
            $flags['groups'][] = ['group' => 'standard_be_options'];
        }

        return $this->productOptions->getProductOptions($flags);
    }

    /**
     * @return array
     */
    public function getBeProducts()
    {
        $beProducts[] = $this->shippingOptions->canUseCargoProducts() ? $this->productOptions->getCargoOptions() : [];
        $beProducts[] = $this->productOptions->getBeOptions();

        if ($this->shippingOptions->isPakjegemakActive('BE')) {
            $beProducts[] = $this->productOptions->getPakjeGemakBeOptions();
        }
        // Add package options as available for those deliveries for selection
        $beProducts[] = $this->productOptions->getPriorityOptions();
        $beProducts[] = $this->productOptions->getBoxableOptions();

        return call_user_func_array("array_merge", $beProducts);
    }

    /**
     * @return array
     */
    public function getBeDomesticProducts()
    {
        $beDomesticProducts[] = $this->productOptions->getBeDomesticOptions();

        if ($this->shippingOptions->isPakjegemakActive('BE')) {
            $beDomesticProducts[] = $this->productOptions->getPakjeGemakBeDomesticOptions();
        }

        return call_user_func_array("array_merge", $beDomesticProducts);
    }

    /**
     * @return array
     */
    public function getEpsProducts()
    {
        $epsProducts[] = $this->productOptions->getEpsOptions();
        // Add package options as available for those deliveries for selection
        $epsProducts[] = $this->productOptions->getPriorityOptions();
        $epsProducts[] = $this->productOptions->getBoxableOptions();

        return call_user_func_array("array_merge", $epsProducts);
    }

    /**
     * @return array
     */
    public function getEpsBusinessProducts()
    {
        $epsBusinessProducts[] = $this->productOptions->getEpsBusinessOptions();

        return call_user_func_array("array_merge", $epsBusinessProducts);
    }

    /**
     * @return array
     */
    public function getPepsProducts()
    {
        $pepsProducts[] = $this->productOptions->getPriorityOptions();

        return call_user_func_array("array_merge", $pepsProducts);
    }

    /**
     * @return array
     */
    public function getPepsBoxableProducts()
    {
        $pepsBoxProducts[] = $this->productOptions->getBoxableOptions();

        return call_user_func_array("array_merge", $pepsBoxProducts);
    }

    /**
     * @return array
     */
    public function getGlobalProducts()
    {
        $globalProducts[] = $this->productOptions->getGlobalPackOptions();
        // Add package options as available for those deliveries for selection
        $globalProducts[] = $this->productOptions->getPriorityOptions();
        $globalProducts[] = $this->productOptions->getBoxableOptions();

        return call_user_func_array("array_merge", $globalProducts);
    }

    /**
     * @return array
     */
    public function getEveningOptionsNL()
    {
        return $this->getEveningOptions('NL');
    }

    /**
     * @return array
     */
    public function getEveningOptionsBE()
    {
        return $this->getEveningOptions('BE');
    }

    /**
     * @param string $country
     *
     * @return array
     */
    public function getEveningOptions($country = 'NL')
    {
        $options = $this->productOptions->getProductOptions(['isEvening' => true, 'countryLimitation' => $country]);
        if ($this->shippingOptions->isIDCheckActive()) {
            return $options;
        }

        $idOptions = $this->productOptions->getProductOptions(
            ['group' => 'id_check_options', 'countryLimitation' => $country]
        );

        $idKeys = array_map(function ($option) {
             return $option['value'];
        }, $idOptions);

        $options = array_filter($options, function ($option) use ($idKeys) {
            return !in_array($option['value'], $idKeys);
        });

        return $options;
    }

    /**
     * @return array
     */
    public function getDeliveryStatedAddressOnlyOptions()
    {
        $options = $this->productOptions->getProductOptions(
            ['group' => 'only_stated_address_options']
        );

        return $options;
    }


    /**
     * @return array
     */
    public function getDeliveryStatedAddressOnlyOptionsBe()
    {
        $options = [];
        $options[] = $this->productOptions->getOptionsByCode('4960');
        $options[] = $this->productOptions->getOptionsByCode('4962');

        return $options;
    }

    /**
     * @return array
     */
    public function getAlternativeDeliveryOptions()
    {

        if (!$this->shippingOptions->canUseBeProducts()) {
            $flags = [];
            $flags['groups'][] = ['group' => 'standard_options'];
            $flags['groups'][] = ['group' => 'buspakje_options'];
            $flags['groups'][] = ['group' => 'only_stated_address_options'];
            if ($this->shippingOptions->isIDCheckActive()) {
                $flags['groups'][] = ['group' => 'id_check_options'];
            }

            if ($this->shippingOptions->canUseCargoProducts()) {
                $flags['groups'][] = ['group' => 'cargo_options'];
            }

            if ($this->shippingOptions->canUseEpsBusinessProducts()) {
                $flags['groups'][] = ['group' => 'eps_package_options'];
            }
        }

        if ($this->shippingOptions->canUseBeProducts()) {
            $flags['groups'][] = ['group' => 'standard_be_options'];
        }

        return $this->productOptions->getProductOptions($flags);
    }
}
