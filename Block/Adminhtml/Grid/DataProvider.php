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
namespace TIG\PostNL\Block\Adminhtml\Grid;

use Magento\Backend\Block\Template;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\App\DeploymentConfig\Reader;
use TIG\PostNL\Config\Source\Options\FirstLabelPosition;
use TIG\PostNL\Config\Source\Options\ProductOptions;
use TIG\PostNL\Config\Provider\AddressConfiguration;
use TIG\PostNL\Config\Provider\Webshop as WebshopConfig;
use TIG\PostNL\Config\Provider\ProductOptions as OptionConfig;
use TIG\PostNL\Service\Options\GuaranteedOptions;

class DataProvider extends Template implements BlockInterface
{
    const XPATH_SHOW_GRID_TOOLBAR = 'tig_postnl/extra_settings_advanced/show_grid_toolbar';

    /**
     * @var string
     */
    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_PostNL::grid/DataProvider.phtml';

    /**
     * @var Reader
     */
    private $configReader;

    /**
     * @var ProductOptions
     */
    private $productOptions;

    /**
     * @var OptionConfig
     */
    private $optionConfig;

    /**
     * @var WebshopConfig
     */
    private $webshopConfig;

    /**
     * @var GuaranteedOptions
     */
    private $guaranteedOptions;

    /**
     * @var FirstLabelPosition
     */
    private $firstLabelPosition;

    /**
     * @var AddressConfiguration
     */
    private $addressConfiguration;

    /**
     * DataProvider constructor.
     *
     * @param Template\Context     $context
     * @param Reader               $reader
     * @param ProductOptions       $productOptions
     * @param OptionConfig         $optionConfig
     * @param WebshopConfig        $webshopConfig
     * @param GuaranteedOptions    $guaranteedOptions
     * @param FirstLabelPosition   $firstLabelPosition
     * @param AddressConfiguration $addressConfiguration
     * @param array                $data
     */
    public function __construct(
        Template\Context $context,
        Reader $reader,
        ProductOptions $productOptions,
        OptionConfig $optionConfig,
        WebshopConfig $webshopConfig,
        GuaranteedOptions $guaranteedOptions,
        FirstLabelPosition $firstLabelPosition,
        AddressConfiguration $addressConfiguration,
        array $data = []
    ) {
        $this->configReader = $reader;
        $this->productOptions = $productOptions;
        $this->optionConfig = $optionConfig;
        $this->webshopConfig = $webshopConfig;
        $this->guaranteedOptions = $guaranteedOptions;
        $this->firstLabelPosition = $firstLabelPosition;
        $this->addressConfiguration = $addressConfiguration;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getProductOptions()
    {
        $supportedTypes = $this->productOptions->get();
        $country = $this->addressConfiguration->getCountry();

        if ($country == 'NL') {
            $supportedTypes = array_filter($supportedTypes, [$this, 'filterBeDomesticOption']);
        }
        if ($country == 'BE') {
            $supportedTypes = array_filter($supportedTypes, [$this, 'filterNlDomesticOption']);
        }

        $options = [];
        foreach ($supportedTypes as $key => $option) {
            $options[] = [
                'value' => (string) $key,
                'text' => __($option['label'])
            ];
        }

        return \Zend_Json::encode($options);
    }

    /**
     * @param $productOption
     *
     * @return bool
     */
    private function filterNlDomesticOption($productOption)
    {
        // countryLimitation is used for country destination.
        // Since all BE > NL shipments are EPS, most NL domestic product codes can be filtered right away via this check
        if ($productOption['countryLimitation'] == 'NL') {
            return false;
        }

        // These are NL > BE shipments, which aren't possible since the shipments come from BE.
        $flags['groups'][] = ['group' => 'be_options'];
        $flags['groups'][] = ['group' => 'pakjegemak_be_options'];

        $domesticOptions = $this->productOptions->getProductOptions($flags);

        $isNotDomestic = $this->isNotDomestic($productOption, $domesticOptions);

        return $isNotDomestic;
    }

    /**
     * @param $productOption
     *
     * @return bool
     */
    private function filterBeDomesticOption($productOption)
    {
        $beDomesticOptions[] = $this->productOptions->getBeDomesticOptions();
        $beDomesticOptions[] = $this->productOptions->getPakjeGemakBeDomesticOptions();

        // @codingStandardsIgnoreLine
        $beDomesticOptions = call_user_func_array("array_merge", $beDomesticOptions);

        $isNotDomestic = $this->isNotDomestic($productOption, $beDomesticOptions);

        return $isNotDomestic;
    }

    /**
     * @param $option
     * @param $domesticOptions
     *
     * @return bool
     */
    private function isNotDomestic($option, $domesticOptions)
    {
        $isNotDomestic = true;

        foreach ($domesticOptions as $domesticOption) {
            if ($option['value'] === $domesticOption['value']) {
                $isNotDomestic = false;
                break;
            }
        }

        return $isNotDomestic;
    }

    /**
     * @return int|string
     */
    public function getDefaultProductOption()
    {
        return $this->optionConfig->getDefaultProductOption();
    }

    /**
     * @return string
     */
    public function getAdminBaseUrl()
    {
        $config = $this->configReader->load();
        $adminSuffix = $config['backend']['frontName'];
        return $this->getBaseUrl() . $adminSuffix . '/';
    }

    /**
     * @return bool
     */
    public function getShowToolbar()
    {
        return $this->webshopConfig->getShowToolbar();
    }

    /**
     * @return bool
     */
    public function isGuaranteedDeliveryActive()
    {
        return $this->guaranteedOptions->isGuaranteedActive();
    }

    /**
     * @return string
     */
    public function getCargoTimeOptions()
    {
        $cargoOptions = $this->guaranteedOptions->getCargoTimeOptions();

        $options = array_map(function ($option) {
            return [
                'value' => $option['value'],
                'text'  => $option['label']
            ];
        }, $cargoOptions);

        return $this->returnTimeOptions($options);
    }

    /**
     * @return string
     */
    public function getPackagesTimeOptions()
    {
        $packagesOptions = $this->guaranteedOptions->getPackagesTimeOptions();

        $options = array_map(function ($option) {
            return [
                'value' => $option['value'],
                'text'  => $option['label']
            ];
        }, $packagesOptions);

        return $this->returnTimeOptions($options);
    }

    /**
     * @return string
     */
    public function getLabelStartPositionOptions()
    {
        $startPositions = $this->firstLabelPosition->toOptionArray();

        $options = [];
        foreach ($startPositions as $key => $option) {
            $options[] = [
                'value' => $option['value'],
                'text' => $option['label']
            ];
        }

        return \Zend_Json::encode($options);
    }

    /**
     * @param $options
     *
     * @return string
     */
    private function returnTimeOptions($options)
    {
        $noneValue = [
            'value' => 'none',
            // @codingStandardsIgnoreLine
            'text'  => __('Not guaranteed')
        ];

        $options = array_merge([$noneValue], $options);
        return \Zend_Json::encode($options);
    }
}
