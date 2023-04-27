<?php

namespace TIG\PostNL\Controller\DeliveryOptions;

use Magento\Framework\Json\EncoderInterface;
use TIG\PostNL\Controller\AbstractDeliveryOptions;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Helper\AddressEnhancer;
use TIG\PostNL\Service\Carrier\Price\Calculator;
use TIG\PostNL\Service\Carrier\QuoteToRateRequest;
use TIG\PostNL\Service\Shipping\LetterboxPackage;
use TIG\PostNL\Webservices\Endpoints\Locations as LocationsEndpoint;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use TIG\PostNL\Service\Quote\ShippingDuration;

class Locations extends AbstractDeliveryOptions
{
    /** @var AddressEnhancer */
    private $addressEnhancer;

    /** @var  LocationsEndpoint */
    private $locationsEndpoint;

    /** @var Calculator */
    private $priceCalculator;

    /**
     * @var LetterboxPackage
     */
    private $letterboxPackage;

    /**
     * @param Context            $context
     * @param OrderRepository    $orderRepository
     * @param Session            $checkoutSession
     * @param QuoteToRateRequest $quoteToRateRequest
     * @param AddressEnhancer    $addressEnhancer
     * @param LocationsEndpoint  $locations
     * @param DeliveryDate       $deliveryDate
     * @param Calculator         $priceCalculator
     * @param ShippingDuration   $shippingDuration
     * @param LetterboxPackage   $letterboxPackage
     */
    public function __construct(
        Context $context,
        EncoderInterface $encoder,
        OrderRepository $orderRepository,
        Session $checkoutSession,
        QuoteToRateRequest $quoteToRateRequest,
        AddressEnhancer $addressEnhancer,
        LocationsEndpoint $locations,
        DeliveryDate $deliveryDate,
        Calculator $priceCalculator,
        ShippingDuration $shippingDuration,
        LetterboxPackage $letterboxPackage
    ) {
        $this->addressEnhancer   = $addressEnhancer;
        $this->locationsEndpoint = $locations;
        $this->priceCalculator   = $priceCalculator;
        $this->letterboxPackage = $letterboxPackage;

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
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $products = $this->checkoutSession->getQuote()->getAllItems();
        if ($this->letterboxPackage->isLetterboxPackage($products, false)) {
            return $this->jsonResponse([
                'error' => __('Pickup locations are disabled for Letterbox packages.')
            ]);
        }

        $params = $this->getRequest()->getParams();
        if (!isset($params['address']) || !is_array($params['address'])) {
            return $this->jsonResponse(__('No Address data found.'));
        }
        $this->addressEnhancer->set($params['address']);
        $price = $this->priceCalculator->getPriceWithTax($this->getRateRequest(), 'pakjegemak');

        try {
            return $this->jsonResponse([
                'price'       => $price['price'],
                'locations'   => $this->getValidResponeType(),
                'pickup_date' => $this->getDeliveryDay($this->addressEnhancer->get())
            ]);
        } catch (\Exception $exception) {
            return $this->jsonResponse([
                'error' => __('Invalid locations response, more information can be found in the PostNL log files.')
            ]);
        }
    }

    /**
     * @param $address
     *
     * @return mixed
     * @throws \Exception
     */
    private function getLocations($address)
    {
        $deliveryDate = false;
        if ($this->getDeliveryDay($address)) {
            $deliveryDate = $this->getDeliveryDay($address);
        }

        $quote = $this->checkoutSession->getQuote();
        $storeId = $quote->getStoreId();
        $this->locationsEndpoint->changeAPIKeyByStoreId($storeId);
        $this->locationsEndpoint->updateParameters($address ,$deliveryDate);
        $response = $this->locationsEndpoint->call();
        //@codingStandardsIgnoreLine
        if (!is_object($response) || !isset($response->GetLocationsResult->ResponseLocation)) {
            throw new LocalizedException(
                __('Invalid GetLocationsResult response: %1', var_export($response, true))
            );
        }

        //@codingStandardsIgnoreLine
        return $response->GetLocationsResult->ResponseLocation;
    }

    /**
     * @return array|\Magento\Framework\Phrase
     */
    private function getValidResponeType()
    {
        $address = $this->addressEnhancer->get();

        if (isset($address['error'])) {
            //@codingStandardsIgnoreLine
            return ['error' => __('%1 : %2', $address['error']['code'], $address['error']['message'])];
        }

        return $this->getLocations($address);
    }
}
