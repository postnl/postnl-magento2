<?php

/**
 * @codingStandardsIgnoreStart
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
use TIG\PostNL\Model\Carrier\Validation\Country;

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
     * @var Country
     */
    private $countryValidation;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory         $rateErrorFactory
     * @param LoggerInterface      $logger
     * @param ResultFactory        $rateResultFactory
     * @param MethodFactory        $rateMethodFactory
     * @param Track                $track
     * @param Calculator           $calculator
     * @param AccountConfiguration $accountConfiguration
     * @param array                $data
     * @param Country              $country
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
        Country $country,
        array $data = []
    ) {
        $this->rateResultFactory      = $rateResultFactory;
        $this->rateMethodFactory      = $rateMethodFactory;
        $this->track                  = $track;
        $this->calculator             = $calculator;
        $this->accountConfiguration   = $accountConfiguration;
        $this->countryValidation      = $country;

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

        if (!$this->countryValidation->validate($request->getData('dest_country_id'))) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();

        $method = $this->getMethod($request);

        if ($method) {
            return $result->append($method);
        }

        return false;
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
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method | bool
     */
    private function getMethod(RateRequest $request)
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->rateMethodFactory->create();
        $amount = $this->getAmount($request);

        // Hide PostNL if no amount could be calculated.
        if ($amount === false) {
            return false;
        }

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
        $method->setPrice($amount['price']);

        return $method;
    }

    /**
     * @param RateRequest $request
     *
     * @return bool | array
     */
    private function getAmount(RateRequest $request)
    {
        $amount = $this->calculator->price($request, null, $this->getStore());

        if ($amount === false) {
            return false;
        }

        if ($amount['price'] == '0') {
            return [
                'price' => $this->getFinalPriceWithHandlingFee($amount['price']),
                'cost' => $this->getFinalPriceWithHandlingFee($amount['cost']),
            ];
        }

        return $amount;
    }
}
/**
 * @codingStandardsIgnoreEnd
 */
