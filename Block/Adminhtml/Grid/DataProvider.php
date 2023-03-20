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
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\View\Element\BlockInterface;
use Magento\Framework\App\DeploymentConfig\Reader;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Config\Source\Options\FirstLabelPosition;
use TIG\PostNL\Config\Source\Options\ProductOptions;
use TIG\PostNL\Config\Provider\AddressConfiguration;
use TIG\PostNL\Config\Provider\Webshop as WebshopConfig;
use TIG\PostNL\Config\Provider\ProductOptions as OptionConfig;
use TIG\PostNL\Config\Source\Settings\InsuredTiers;
use TIG\PostNL\Service\Filter\DomesticOptions;
use TIG\PostNL\Service\Options\GuaranteedOptions;

class DataProvider extends Template implements BlockInterface
{
    const XPATH_SHOW_GRID_TOOLBAR = 'tig_postnl/extra_settings_advanced/show_grid_toolbar';

    /**
     * @var string
     */
    // @codingStandardsIgnoreLine
    protected $_template = 'TIG_PostNL::grid/DataProvider.phtml';

    /** @var EncoderInterface */
    private $encoder;

    /**
     * @var Reader
     */
    private $configReader;

    /** @var ShippingOptions */
    private $shippingOptions;

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
     * @var DomesticOptions
     */
    private $domesticOptionsFilter;

    /** @var InsuredTiers */
    private $insuredTiers;

    /**
     * DataProvider constructor.
     *
     * @param Template\Context   $context
     * @param EncoderInterface   $encoder
     * @param Reader             $reader
     * @param ShippingOptions    $shippingOptions
     * @param ProductOptions     $productOptions
     * @param OptionConfig       $optionConfig
     * @param WebshopConfig      $webshopConfig
     * @param GuaranteedOptions  $guaranteedOptions
     * @param FirstLabelPosition $firstLabelPosition
     * @param DomesticOptions    $domesticOptionsFilter
     * @param InsuredTiers       $insuredTiers
     * @param array              $data
     */
    public function __construct(
        Template\Context $context,
        EncoderInterface $encoder,
        Reader $reader,
        ShippingOptions $shippingOptions,
        ProductOptions $productOptions,
        OptionConfig $optionConfig,
        WebshopConfig $webshopConfig,
        GuaranteedOptions $guaranteedOptions,
        FirstLabelPosition $firstLabelPosition,
        DomesticOptions $domesticOptionsFilter,
        InsuredTiers $insuredTiers,
        array $data = []
    ) {
        $this->encoder = $encoder;
        $this->configReader = $reader;
        $this->shippingOptions = $shippingOptions;
        $this->productOptions = $productOptions;
        $this->optionConfig = $optionConfig;
        $this->webshopConfig = $webshopConfig;
        $this->guaranteedOptions = $guaranteedOptions;
        $this->firstLabelPosition = $firstLabelPosition;
        $this->domesticOptionsFilter = $domesticOptionsFilter;
        $this->insuredTiers = $insuredTiers;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getProductOptions()
    {
        $supportedTypes = $this->productOptions->get();
        $supportedTypes = $this->domesticOptionsFilter->filter($supportedTypes);

        $options = [];
        foreach ($supportedTypes as $key => $option) {
            $options[] = [
                'value' => (string) $key,
                'text' => __($option['label'])
            ];
        }

        return $this->encoder->encode($options);
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
    public function getInsuredTierOptions()
    {
        $insuredTierOptions = $this->insuredTiers->toOptionArray();

        $options = array_map(function ($option) {
            return [
                'value' => $option['value'],
                'text'  => $option['label']
            ];
        }, $insuredTierOptions);

        return $this->encoder->encode($options);
    }

    /**
     * @return string
     */
    public function getDefaultInsuredTier()
    {
        return $this->shippingOptions->getInsuredTier();
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

        return $this->encoder->encode($options);
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
        return $this->encoder->encode($options);
    }
}
