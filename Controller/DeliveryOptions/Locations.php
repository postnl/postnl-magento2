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
namespace TIG\PostNL\Controller\DeliveryOptions;

use TIG\PostNL\Controller\AbstractDeliveryOptions;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Helper\AddressEnhancer;
use TIG\PostNL\Service\Carrier\Price\Calculator;
use TIG\PostNL\Service\Carrier\QuoteToRateRequest;
use TIG\PostNL\Service\Order\FirstDeliveryDate;
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

    /** @var FirstDeliveryDate $firstDeliveryDate */
    private $firstDeliveryDate;

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
     */
    public function __construct(
        Context $context,
        OrderRepository $orderRepository,
        Session $checkoutSession,
        QuoteToRateRequest $quoteToRateRequest,
        AddressEnhancer $addressEnhancer,
        LocationsEndpoint $locations,
        DeliveryDate $deliveryDate,
        Calculator $priceCalculator,
        ShippingDuration $shippingDuration,
        FirstDeliveryDate $firstDeliveryDate
    ) {
        $this->addressEnhancer   = $addressEnhancer;
        $this->locationsEndpoint = $locations;
        $this->priceCalculator   = $priceCalculator;
        $this->firstDeliveryDate = $firstDeliveryDate;

        parent::__construct(
            $context,
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
        $params = $this->getRequest()->getParams();
        if (!isset($params['address']) || !is_array($params['address'])) {
            return $this->jsonResponse(__('No Address data found.'));
        }
        $this->addressEnhancer->set($params['address']);
        $price    = $this->priceCalculator->price($this->getRateRequest(), 'pakjegemak', null, true);
        $postcode = $params['address']['postcode'] ?? '';
        $country  = $params['address']['country'] ?? '';
        try {
            return $this->jsonResponse([
                'price'       => $price['price'],
                'locations'   => $this->getValidResponeType(),
                'pickup_date' => $this->firstDeliveryDate->get($postcode, $country)
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
        $this->locationsEndpoint->updateParameters($address, $deliveryDate);
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
