<?php

namespace TIG\PostNL\Controller\DeliveryOptions;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Json\EncoderInterface;
use TIG\PostNL\Config\CheckoutConfiguration\IsDeliverDaysActive;
use TIG\PostNL\Config\Provider\ShippingOptions;
use TIG\PostNL\Controller\AbstractDeliveryOptions;
use TIG\PostNL\Helper\AddressEnhancer;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Service\Carrier\Price\Calculator;
use TIG\PostNL\Service\Carrier\QuoteToRateRequest;
use TIG\PostNL\Service\Converter\CanaryIslandToIC;
use TIG\PostNL\Service\Quote\ShippingDuration;
use TIG\PostNL\Service\Shipment\EpsCountries;
use TIG\PostNL\Service\Shipping\BoxablePackets;
use TIG\PostNL\Service\Shipping\LetterboxPackage;
use TIG\PostNL\Service\Validation\CountryShipping;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate;
use TIG\PostNL\Webservices\Endpoints\TimeFrame;

// @codingStandardsIgnoreFile
class Timeframes extends AbstractDeliveryOptions
{
    /**
     * @var AddressEnhancer
     */
    private $addressEnhancer;

    /**
     * @var  TimeFrame
     */
    private $timeFrameEndpoint;

    /**
     * @var Calculator
     */
    private $calculator;

    /**
     * @var IsDeliverDaysActive
     */
    private $isDeliveryDaysActive;

    /**
     * @var ShippingOptions
     */
    private $shippingOptions;

    /**
     * @var LetterboxPackage
     */
    private $letterboxPackage;

    /**
     * @var BoxablePackets
     */
    private $boxablePackets;

    /**
     * @var CanaryIslandToIC
     */
    private $canaryConverter;

    /** @var CountryShipping */
    private $countryShipping;

    /**
     * @param Context             $context
     * @param OrderRepository     $orderRepository
     * @param Session             $checkoutSession
     * @param QuoteToRateRequest  $quoteToRateRequest
     * @param AddressEnhancer     $addressEnhancer
     * @param DeliveryDate        $deliveryDate
     * @param TimeFrame           $timeFrame
     * @param Calculator          $calculator
     * @param IsDeliverDaysActive $isDeliverDaysActive
     * @param ShippingDuration    $shippingDuration
     * @param ShippingOptions     $shippingOptions
     * @param LetterboxPackage    $letterboxPackage
     * @param BoxablePackets      $boxablePackets ,
     * @param CanaryIslandToIC    $canaryConverter
     * @param CountryShipping     $countryShipping
     */
    public function __construct(
        Context $context,
        EncoderInterface $encoder,
        OrderRepository $orderRepository,
        Session $checkoutSession,
        QuoteToRateRequest $quoteToRateRequest,
        AddressEnhancer $addressEnhancer,
        DeliveryDate $deliveryDate,
        TimeFrame $timeFrame,
        Calculator $calculator,
        IsDeliverDaysActive $isDeliverDaysActive,
        ShippingDuration $shippingDuration,
        ShippingOptions $shippingOptions,
        LetterboxPackage $letterboxPackage,
        BoxablePackets $boxablePackets,
        CanaryIslandToIC $canaryConverter,
        CountryShipping $countryShipping
    ) {
        $this->addressEnhancer              = $addressEnhancer;
        $this->timeFrameEndpoint            = $timeFrame;
        $this->calculator                   = $calculator;
        $this->isDeliveryDaysActive         = $isDeliverDaysActive;
        $this->shippingOptions              = $shippingOptions;
        $this->letterboxPackage             = $letterboxPackage;
        $this->boxablePackets               = $boxablePackets;
        $this->canaryConverter              = $canaryConverter;
        $this->countryShipping              = $countryShipping;

        parent::__construct(
            $context,
            $encoder,
            $orderRepository,
            $checkoutSession,
            $quoteToRateRequest,
            $shippingDuration,
            $deliveryDate
        );
    }

    /**
     * @return bool|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \TIG\PostNL\Exception
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if (!isset($params['address']) || !is_array($params['address'])) {
            return $this->jsonResponse($this->getFallBackResponse(1));
        }

        $price = $this->calculator->getPriceWithTax($this->getRateRequest());

        if (!isset($price['price'])) {
            return false;
        }

        $quote = $this->checkoutSession->getQuote();
        $cartItems = $quote->getAllItems();

        //Letterbox NL
        if ($this->letterboxPackage->isLetterboxPackage($cartItems, false) && $params['address']['country'] == 'NL') {
            return $this->jsonResponse($this->getLetterboxPackageResponse($price['price']));
        }

        //Boxable Packet = Letterbox Worldwide
        if ($this->boxablePackets->isBoxablePacket($cartItems, false) && $params['address']['country'] != 'NL') {
            return $this->jsonResponse($this->getBoxablePacketResponse($price['price']));
        }

        if (in_array($params['address']['country'], EpsCountries::ALL) && $params['address']['country'] === 'ES' && $this->canaryConverter->isCanaryIsland($params['address'])) {
            return $this->jsonResponse($this->getGlobalPackResponse($price['price']));
        }

        if ($this->isEpsCountry($params)) {
            return $this->jsonResponse($this->getEpsCountryResponse($price['price']));
        }

        if (!in_array($params['address']['country'], EpsCountries::ALL) && !in_array($params['address']['country'], ['BE', 'NL'])) {
            return $this->jsonResponse($this->getGlobalPackResponse($price['price']));
        }

        if (!$this->isDeliveryDaysActive->getValue()) {
            return $this->jsonResponse($this->getFallBackResponse(2, $price['price']));
        }

        $this->addressEnhancer->set($params['address']);

        try {
            return $this->jsonResponse($this->getValidResponseType($price['price']));
        } catch (\Exception $exception) {
            return $this->jsonResponse($this->getFallBackResponse(3, $price['price']));
        }
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    private function isEpsCountry($params)
    {
        if (!in_array($params['address']['country'], EpsCountries::ALL)) {
            return false;
        }

        // NL to BE/NL shipments are not EPS shipments
        if ($this->countryShipping->isShippingNLToEps($params['address']['country'])) {
            return true;
        }

        //NL to BE shipments can be a PEPS shipment and should be considered as such when enabled
        if ($this->countryShipping->isShippingNLtoBE($params['address']['country']) && $this->shippingOptions->canUsePriority()) {
            return true;
        }

        // BE to BE shipments is not EPS, but BE to NL is
        if ($this->countryShipping->isShippingBEToEps($params['address']['country'])) {
            return true;
        }

        return false;
    }

    /**
     * @param $address
     *
     * @return array|\Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    private function getPossibleDeliveryDays($address)
    {
        $startDate  = $this->getDeliveryDay($address);

        return $this->getTimeFrames($address, $startDate);
    }

    /**
     * @param $price
     *
     * @return array|\Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    private function getValidResponseType($price)
    {
        $address = $this->addressEnhancer->get();

        if (isset($address['error'])) {
            return [
                'error'      => __('%1 : %2', $address['error']['code'], $address['error']['message']),
                'price'      => $price,
                'timeframes' => [[['fallback' => __('At the first opportunity')]]]
            ];
        }

        $timeframes = $this->getPossibleDeliveryDays($address);
        if (empty($timeframes)) {
            return [
                'error'      => __('No timeframes available.'),
                'price'      => $price,
                'timeframes' => [[['fallback' => __('At the first opportunity')]]]
            ];
        }

        return [
            'price'      => $price,
            'timeframes' => $timeframes
        ];
    }

    /**
     * CIF call to get the timeframes.
     *
     * @param $address
     * @param $startDate
     *
     * @return array|\Magento\Framework\Phrase
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Webapi\Exception
     * @throws \TIG\PostNL\Webservices\Api\Exception
     */
    private function getTimeFrames($address, $startDate)
    {
        $quote = $this->checkoutSession->getQuote();
        $storeId = $quote->getStoreId();
        $this->timeFrameEndpoint->updateApiKey($storeId);
        $this->timeFrameEndpoint->fillParameters($address, $startDate);

        return $this->timeFrameEndpoint->call();
    }

    /**
     * @param $error
     * @param $price
     * @return array
     */
    private function getFallBackResponse($error = 0, $price = null)
    {
        $errorMessage = isset($this->returnErrors[$error]) ? $this->returnErrors[$error] : '';
        return [
            'error'      => __($errorMessage),
            'price'      => $price,
            'timeframes' => [[['fallback' => __('At the first opportunity')]]]
        ];
    }

    private function getLetterboxPackageResponse($price)
    {
        return [
            'price'             => $price,
            'letterbox_package' => true,
            'timeframes'        => [[['letterbox_package' => __('Your order is a letterbox package and will be '
            . 'delivered from Tuesday to Saturday.')]]]
        ];
    }

    private function getBoxablePacketResponse($price)
    {
        return [
            'price'             => $price,
            'boxable_packets'   => true,
            'timeframes'        => [[['boxable_packets' => __('Your order is a boxable packet.')]]]
        ];
    }

    /**
     * @param $price
     *
     * @return array
     */
    private function getEpsCountryResponse($price)
    {
        return [
            'price'      => $price,
            'timeframes' => [[['eps' => __('Ship internationally')]]]
        ];
    }

    /**
     * @param $price
     *
     * @return array
     */
    private function getGlobalPackResponse($price)
    {
        return [
            'price' => $price,
            'timeframes' => [[['gp' => __('Ship internationally')]]]
        ];
    }
}
