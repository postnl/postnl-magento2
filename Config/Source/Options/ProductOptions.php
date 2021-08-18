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
namespace TIG\PostNL\Config\Source\Options;

use Magento\Framework\Option\ArrayInterface;
use Magento\Quote\Model\Quote\Address as QuoteAddress;
use Magento\Sales\Model\Order\Address as SalesAddress;
use TIG\PostNL\Config\Source\OptionsAbstract;
use TIG\PostNL\Service\Converter\CanaryIslandToIC;
use TIG\PostNL\Service\Shipment\GuaranteedOptions;

/**
 * As this class holds all the methods to retrieve correct product codes, it is too long for Code Sniffer to check.
 */
// @codingStandardsIgnoreFile
class ProductOptions extends OptionsAbstract implements ArrayInterface
{
    /**
     * @var CanaryIslandToIC
     */
    private $canaryConverter;

    /**
     * @var array
     */
    private $combiProductCodes = [
        '4944' => '4952'
    ];

    /**
     * @param CanaryIslandToIC $canaryConverter
     */
    public function __construct(CanaryIslandToIC $canaryConverter)
    {
        $this->canaryConverter = $canaryConverter;
    }

    /**
     * @param $code
     * @param $type
     *
     * @return array
     */
    public function getLabel($code, $type)
    {
        if (array_key_exists($code, $this->combiProductCodes)) {
            $code = $this->combiProductCodes[$code];
        }

        if (!array_key_exists($code, $this->availableOptions) || !array_key_exists($type, $this->typeToComment)) {
            return ['label' => '', 'type' => '', 'comment' => ''];
        }

        $group = $this->availableOptions[$code]['group'];

        return [
            'label'   => $this->groupToLabel[$group],
            'type'    => $this->typeToComment[$type],
            'comment' => $this->availableOptions[$code]['label']
        ];
    }

    /**
     * @return array
     */
    public function getAllProductCodes()
    {
        return array_keys($this->availableOptions);
    }

    /**
     * Returns option array
     * @return array
     */
    public function toOptionArray()
    {
        $this->setGroupedOptions($this->availableOptions, $this->groups);

        return $this->getGroupedOptions();
    }

    /**
     * Returns options if sunday is true
     * @return array
     */
    public function getIsSundayOptions()
    {
        return $this->getProductOptions(['isSunday' => true]);
    }

    /**
     * Returns options if group equals pakjegemak_options
     * @return array
     */
    public function getPakjeGemakOptions()
    {
        $flags = [];
        $flags['groups'][] = ['group' => 'pakjegemak_options'];
        $flags['groups'][] = ['group' => 'id_check_pakjegemak_options'];
        return $this->getProductOptions($flags);
    }

    /**
     * Returns options if group equals pakjegemak_be_options
     * @return array
     */
    public function getPakjeGemakBeOptions()
    {
        $flags = [];
        $flags['groups'][] = ['group' => 'pakjegemak_be_options'];
        return $this->getProductOptions($flags);
    }

    /**
     * Returns options if group equals pakjegemak_be_domestic_options
     * @return array
     */
    public function getPakjeGemakBeDomesticOptions()
    {
        $flags = [];
        $flags['groups'][] = ['group' => 'pakjegemak_be_domestic_options'];
        return $this->getProductOptions($flags);
    }

    /**
     * Returns options if group equals standard_options
     * @return array
     */
    public function getDefaultOptions()
    {
        $flags = [];
        $flags['groups'][] = ['group' => 'standard_options'];
        $flags['groups'][] = ['group' => 'id_check_options'];
        $flags['groups'][] = ['group' => 'cargo_options'];

        return $this->getProductOptions($flags);
    }

    /**
     * @return array
     */
    public function getExtraCoverProductOptions()
    {
        return $this->getProductOptions(['isExtraCover' => true]);
    }

    /**
     * @param SalesAddress|QuoteAddress|false $address
     *
     * @return array
     */
    public function getEpsProductOptions($address = false)
    {
        // Check if the function is called from SetDefaultData as an object and check if it is a Canary Island
        if ($address && is_object($address) && $address->getCountryId() === 'ES' && $this->canaryConverter->isCanaryIsland($address)) {
            return $this->getGlobalPackOptions();
        }

        // Check if the address is called from Save as an array and check if it is a Canary Island
        if ($address && is_array($address) && $address['country'] === 'ES' && $this->canaryConverter->isCanaryIsland($address)) {
            return $this->getGlobalPackOptions();
        }

        return $this->getProductOptions(['isEvening' => false, 'countryLimitation' => false, 'group' => 'eu_options']);
    }

    /**
     * @return array
     */
    public function getEuOptions()
    {
        $euOptions = $this->getProductOptions(['group' => 'eu_options']);

        return $euOptions;
    }

    /**
     * @return array
     */
    public function getBeOptions()
    {
        $beOptions = $this->getProductOptions(['group' => 'be_options']);

        return $beOptions;
    }

    /**
     * @return array
     */
    public function getBeDomesticOptions()
    {
        $beDomesticOptions = $this->getProductOptions(['group' => 'standard_be_options']);

        return $beDomesticOptions;
    }


    /**
     * @return array
     */
    public function getEpsOptions()
    {
        $epsOptions = $this->getProductOptions(
            ['isEvening' => false, 'countryLimitation' => false, 'group' => 'eu_options']
        );
        return $epsOptions;
    }

    /**
     * @return array
     */
    public function getPriorityOptions()
    {
        $priorityOptions = $this->getProductOptions(['group' => 'priority_options']);

        return $priorityOptions;
    }

    /**
     * @return array
     */
    public function getEpsBusinessOptions()
    {
        $epsBusinessOptions = $this->getProductOptions(
            [
                'isEvening' => false,
                'group' => 'eps_package_options'
            ]
        );

        return $epsBusinessOptions;
    }

    /**
     * @return array
     */
    public function getGlobalPackOptions()
    {
        return $this->getProductOptions(['group' => 'global_options']);
    }

    /**
     * @return array
     */
    public function getExtraAtHomeOptions()
    {
        return $this->getProductOptions(['group' => 'extra_at_home_options']);
    }

    /**
     * @return array
     */
    public function getDefaultGPOption()
    {
        $productOptions = $this->getProductOptions(['isDefault' => 1, 'group' => 'global_options']);
        return array_shift($productOptions);
    }

    /**
     * @return array
     */
    public function getDefaultEUOption()
    {
        $productOptions = $this->getProductOptions(['isDefault' => 1, 'group' => 'eu_options']);
        return array_shift($productOptions);
    }

    /**
     * @return array
     */
    public function getCargoOptions()
    {
        $cargoProducts = $this->getProductOptions(
            ['countryLimitation' => 'BE', 'group' => 'cargo_options']
        );

        return $cargoProducts;
    }

    /**
     * @param $code
     *
     * @return null|string
     */
    public function getGuaranteedType($code)
    {
        $productOption = $this->getOptionsByCode($code);
        if (!$productOption) {
            return null;
        }

        if ($productOption['group'] == 'cargo_options') {
            return GuaranteedOptions::GUARANTEED_TYPE_CARGO;
        }

        return GuaranteedOptions::GUARANTEED_TYPE_PACKAGE;
    }

    /**
     * @param $code
     * @param $key
     * @param $value
     *
     * @return bool|null
     */
    public function doesProductMatchFlags($code, $key, $value)
    {
        $productOption = $this->getOptionsByCode($code);
        if (!$productOption) {
            return null;
        }

        if (!isset($productOption[$key])) {
            return null;
        }

        return $productOption[$key] == $value;
    }
}
