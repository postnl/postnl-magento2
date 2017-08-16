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
use TIG\PostNL\Model\OrderFactory;
use TIG\PostNL\Helper\AddressEnhancer;
use TIG\PostNL\Service\Carrier\Price\Calculator;
use TIG\PostNL\Service\Carrier\QuoteToRateRequest;
use TIG\PostNL\Webservices\Endpoints\Locations as LocationsEndpoint;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Action\Context;
use Magento\Checkout\Model\Session;

class Locations extends AbstractDeliveryOptions
{
    /**
     * @var AddressEnhancer
     */
    private $addressEnhancer;

    /**
     * @var  LocationsEndpoint
     */
    private $locationsEndpoint;

    /**
     * @var Calculator
     */
    private $priceCalculator;

    /**
     * @param Context            $context
     * @param OrderFactory       $orderFactory
     * @param Session            $checkoutSession
     * @param QuoteToRateRequest $quoteToRateRequest
     * @param AddressEnhancer    $addressEnhancer
     * @param LocationsEndpoint  $locations
     * @param DeliveryDate       $deliveryDate
     * @param Calculator         $priceCalculator
     */
    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        Session $checkoutSession,
        QuoteToRateRequest $quoteToRateRequest,
        AddressEnhancer $addressEnhancer,
        LocationsEndpoint $locations,
        DeliveryDate $deliveryDate,
        Calculator $priceCalculator
    ) {
        $this->addressEnhancer   = $addressEnhancer;
        $this->locationsEndpoint = $locations;
        $this->priceCalculator   = $priceCalculator;

        parent::__construct(
            $context,
            $orderFactory,
            $checkoutSession,
            $quoteToRateRequest,
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

        try {
            $price = $this->priceCalculator->price($this->getRateRequest(), 'pakjegemak');
            return $this->jsonResponse([
                'price' => $price['price'],
                'locations' => $this->getValidResponeType(),
            ]);
        } catch (\Exception $exception) {
            return $this->jsonResponse($exception->getMessage(), Http::STATUS_CODE_503);
        }
    }

    /**
     * @param $address
     *
     * @return \Magento\Framework\Phrase
     */
    private function getLocations($address)
    {
        $deliveryDate = false;
        if ($this->getDeliveryDay($address)) {
            $deliveryDate = $this->getDeliveryDay($address);
        }

        $quote = $this->checkoutSession->getQuote();
        $storeId = $quote->getStoreId();
        $this->locationsEndpoint->setStoreId($storeId);
        $this->locationsEndpoint->setParameters($address, $deliveryDate);
        $response = $this->locationsEndpoint->call();
        //@codingStandardsIgnoreLine
        if (!is_object($response) || !isset($response->GetLocationsResult->ResponseLocation)) {
            return __('Invalid GetLocationsResult response: %1', var_export($response, true));
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
