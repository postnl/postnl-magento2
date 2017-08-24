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
namespace TIG\PostNL\Model\Carrier;

use TIG\PostNL\Helper\Tracking\Track;
use TIG\PostNL\Config\Provider\AccountConfiguration;
use TIG\PostNL\Service\Carrier\Price\Calculator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Psr\Log\LoggerInterface;

class PostNL extends AbstractCarrier implements CarrierInterface
{
    // @codingStandardsIgnoreLine
    protected $_code = 'tig_postnl';

    /**
     * @var Track
     */
    private $track;

    /**
     * @var ResultFactory
     */
    private $rateResultFactory;

    /**
     * @var MethodFactory
     */
    private $rateMethodFactory;

    /**
     * @var Calculator
     */
    private $calculator;

    /**
     * @var AccountConfiguration $accountConfiguration
     */
    private $accountConfiguration;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory         $rateErrorFactory
     * @param LoggerInterface      $logger
     * @param ResultFactory        $rateResultFactory
     * @param MethodFactory        $rateMethodFactory
     * @param Track                $track
     * @param Calculator           $calculator
     * @param array                $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        Track $track,
        Calculator $calculator,
        AccountConfiguration $accountConfiguration,
        array $data = []
    ) {
        $this->rateResultFactory      = $rateResultFactory;
        $this->rateMethodFactory      = $rateMethodFactory;
        $this->track                  = $track;
        $this->calculator             = $calculator;
        $this->accountConfiguration   = $accountConfiguration;

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
     * @return bool|\Magento\Framework\DataObject|\Magento\Shipping\Model\Rate\Result|null
     * @api
     */
    public function collectRates(RateRequest $request)
    {
        if ($this->accountConfiguration->isModusOff()) {
            return false;
        }

        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();

        $method = $this->getMethod($request);

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
     * @param $tracking
     *
     * @return string
     */
    public function getTrackingInfo($tracking)
    {
        return $this->track->get($tracking);
    }

    /**
     * @param RateRequest $request
     *
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    private function getMethod(RateRequest $request)
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->rateMethodFactory->create();
        $amount = $this->getAmount($request);

        /** @noinspection PhpUndefinedMethodInspection */
        $method->setCarrier('tig_postnl');
        /** @noinspection PhpUndefinedMethodInspection */
        $method->setCarrierTitle($this->getConfigData('title'));
        /** @noinspection PhpUndefinedMethodInspection */
        $method->setMethod('regular');
        /** @noinspection PhpUndefinedMethodInspection */
        $method->setMethodTitle($this->getConfigData('name'));
        /** @noinspection PhpUndefinedMethodInspection */
        $method->setCost($amount['cost']);
        /** @noinspection PhpUndefinedMethodInspection */
        $method->setCost($amount['cost']);
        $method->setPrice($amount['price']);

        return $method;
    }

    /**
     * @param RateRequest $request
     *
     * @return array
     */
    private function getAmount(RateRequest $request): array
    {
        $amount = $this->calculator->price($request, null, $this->getStore());

        if ($amount['price'] == '0') {
            return [
                'price' => $this->getFinalPriceWithHandlingFee($amount['price']),
                'cost' => $this->getFinalPriceWithHandlingFee($amount['cost']),
            ];
        }

        return $amount;
    }
}
