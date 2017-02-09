<?php
/**
 *                  ___________       __            __
 *                  \__    ___/____ _/  |_ _____   |  |
 *                    |    |  /  _ \\   __\\__  \  |  |
 *                    |    | |  |_| ||  |   / __ \_|  |__
 *                    |____|  \____/ |__|  (____  /|____/
 *                                              \/
 *          ___          __                                   __
 *         |   |  ____ _/  |_   ____ _______   ____    ____ _/  |_
 *         |   | /    \\   __\_/ __ \\_  __ \ /    \ _/ __ \\   __\
 *         |   ||   |  \|  |  \  ___/ |  | \/|   |  \\  ___/ |  |
 *         |___||___|  /|__|   \_____>|__|   |___|  / \_____>|__|
 *                  \/                           \/
 *                  ________
 *                 /  _____/_______   ____   __ __ ______
 *                /   \  ___\_  __ \ /  _ \ |  |  \\____ \
 *                \    \_\  \|  | \/|  |_| ||  |  /|  |_| |
 *                 \______  /|__|    \____/ |____/ |   __/
 *                        \/                       |__|
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Creative Commons License.
 * It is available through the world-wide-web at this URL:
 * http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 * If you are unable to obtain it through the world-wide-web, please send an email
 * to servicedesk@totalinternetgroup.nl so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future. If you wish to customize this module for your
 * needs please contact servicedesk@totalinternetgroup.nl for more information.
 *
 * @copyright   Copyright (c) 2017 Total Internet Group B.V. (http://www.totalinternetgroup.nl)
 * @license     http://creativecommons.org/licenses/by-nc-nd/3.0/nl/deed.en_US
 */
namespace TIG\PostNL\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use TIG\PostNL\Config\Source\Carrier\RateType;
use TIG\PostNL\Services\Shipping\CalculateTablerateShippingPrice;
use TIG\PostNL\Services\Shipping\GetFreeBoxes;

/**
 * Class PostNL
 *
 * @package TIG\PostNL\Model\Carrier
 */
class PostNL extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{
    // @codingStandardsIgnoreLine
    protected $_code = 'tig_postnl';

    /**
     * @var GetFreeBoxes
     */
    private $getFreeBoxes;

    /**
     * @var CalculateTablerateShippingPrice
     */
    private $calculateTablerateShippingPrice;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface          $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory  $rateErrorFactory
     * @param \Psr\Log\LoggerInterface                                    $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory                  $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param GetFreeBoxes                                                $getFreeBoxes
     * @param CalculateTablerateShippingPrice                             $calculateTablerateShippingPrice
     * @param array                                                       $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        GetFreeBoxes $getFreeBoxes,
        CalculateTablerateShippingPrice $calculateTablerateShippingPrice,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->getFreeBoxes = $getFreeBoxes;
        $this->calculateTablerateShippingPrice = $calculateTablerateShippingPrice;

        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     * @api
     */
    public function getAllowedMethods()
    {
        return ['tig_postnl' => $this->getConfigData('name')];
    }

    /**
     * Collect and get rates
     *
     * @param RateRequest $request
     *
     * @return \Magento\Framework\DataObject|bool|null
     * @api
     */
    // @codingStandardsIgnoreLine
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        $price = $this->getPrice($request);
        $method = $this->getMethod($price);

        $result->append($method);

        return $result;
    }

    /**
     * @param array $price
     *
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    private function getMethod($price)
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier('tig_postnl');
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod('regular');
        $method->setMethodTitle($this->getConfigData('name'));

        $method->setPrice($price['price']);
        $method->setCost($price['cost']);

        return $method;
    }

    /**
     * @param RateRequest $request
     *
     * @return array
     */
    private function getPrice(RateRequest $request)
    {
        $price = $this->getFinalPriceWithHandlingFee($this->getConfigData('price'));
        $cost = $price;

        if ($this->getConfigData('rate_type') == RateType::CARRIER_RATE_TYPE_TABLE) {
            $ratePrice = $this->getTableratePrice($request);

            $price = $ratePrice['price'];
            $cost = $ratePrice['cost'];
        }

        if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes->get($request)) {
            $price = '0.00';
            $cost = '0.00';
        }

        return [
            'price' => $price,
            'cost' => $cost
        ];
    }

    /**
     * @param RateRequest $request
     *
     * @return array
     */
    private function getTableratePrice(RateRequest $request)
    {
        $request->setConditionName($this->getConfigData('condition_name'));

        $includeVirtualPrice = $this->getConfigFlag('include_virtual_price');
        $ratePrice = $this->calculateTablerateShippingPrice->getTableratePrice($request, $includeVirtualPrice);

        $price = $this->getFinalPriceWithHandlingFee($ratePrice['price']);
        $cost = $this->getFinalPriceWithHandlingFee($ratePrice['cost']);

        return [
            'price' => $price,
            'cost' => $cost
        ];
    }
}
