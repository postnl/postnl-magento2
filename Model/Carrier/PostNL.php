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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;
use \TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Config\Source\Carrier\RateType;
use TIG\PostNL\Services\Shipping\CalculateTablerateShippingPrice;
use TIG\PostNL\Services\Shipping\GetFreeBoxes;

class PostNL extends AbstractCarrier implements CarrierInterface
{
    // @codingStandardsIgnoreLine
    protected $_code = 'tig_postnl';

    /**
     * @var Track
     */
    private $track;

    /**
     * @var GetFreeBoxes
     */
    private $getFreeBoxes;

    /**
     * @var CalculateTablerateShippingPrice
     */
    private $calculateTablerateShippingPrice;

    /**
     * @var ResultFactory
     */
    private $rateResultFactory;

    /**
     * @var MethodFactory
     */
    private $rateMethodFactory;

    /**
     * @param ScopeConfigInterface            $scopeConfig
     * @param ErrorFactory                    $rateErrorFactory
     * @param LoggerInterface                 $logger
     * @param ResultFactory                   $rateResultFactory
     * @param MethodFactory                   $rateMethodFactory
     * @param Track                           $track
     * @param GetFreeBoxes                    $getFreeBoxes
     * @param CalculateTablerateShippingPrice $calculateTablerateShippingPrice
     * @param array                           $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        Track $track,
        GetFreeBoxes $getFreeBoxes,
        CalculateTablerateShippingPrice $calculateTablerateShippingPrice,
        array $data = []
    ) {
        $this->calculateTablerateShippingPrice = $calculateTablerateShippingPrice;
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->getFreeBoxes = $getFreeBoxes;
        $this->track = $track;

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
        $result = $this->rateResultFactory->create();

        $price = $this->getPrice($request);
        $method = $this->getMethod($price);

        $result->append($method);

        return $result;
    }

    /**
     * @note This is for the internal Magento Label service,
     *       after packages are given the request needs to be handeld trough this method.
     *       needs further implementation if packages is required.
     *
     * @param \Magento\Shipping\Model\Shipment\Request $request
     * @return mixed
     */
    public function requestToShipment($request)
    {
        return $this;
    }

    /**
     * @note This is for the internal Magento Label service,
     *       set to true if packages is needed for further implementation
     *
     * @return bool
     */
    public function isShippingLabelsAvailable()
    {
        return false;
    }

    /**
     * @param $tracking
     *
     * @return string
     */
    public function getTrackingInfo($tracking)
    {
        return $this->track->get($tracking);
    }

    /**
     * @param array $price
     *
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    private function getMethod($price)
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->rateMethodFactory->create();

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
