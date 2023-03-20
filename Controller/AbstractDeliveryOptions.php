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
namespace TIG\PostNL\Controller;

use Magento\Framework\Json\EncoderInterface;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Service\Carrier\QuoteToRateRequest;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate;
use Magento\Quote\Model\Quote\Address\RateRequest;
use TIG\PostNL\Service\Quote\ShippingDuration;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Checkout\Model\Session;

abstract class AbstractDeliveryOptions extends Action
{
    /**
     * @var EncoderInterface
     */
    private EncoderInterface $encoder;

    /**
     * @var OrderRepository
     */
    //@codingStandardsIgnoreLine
    protected $orderRepository;

    /**
     * @var Session
     */
    //@codingStandardsIgnoreLine
    protected $checkoutSession;

    /**
     * @var DeliveryDate
     */
    //@codingStandardsIgnoreLine
    protected $deliveryEndpoint;

    /**
     * @var ShippingDuration
     */
    //@codingStandardsIgnoreLine
    protected $shippingDuration;

    /**
     * @var QuoteToRateRequest
     */
    private $quoteToRateRequest;

    /**
     * @var array
     */
    //@codingStandardsIgnoreLine
    protected $returnErrors = [
        0 => 'Could not load from soap data',
        1 => 'No Address data found.',
        2 => 'Deliverydays options are disabled.',
        3 => 'Invalid timeframes response, more information can be found in the PostNL log files.',
        4 => 'Invalid locations response, more information can be found in the PostNL log files.',
    ];

    /**
     * @param Context            $context
     * @param EncoderInterface   $encoder
     * @param OrderRepository    $orderRepository
     * @param Session            $checkoutSession
     * @param QuoteToRateRequest $quoteToRateRequest
     * @param ShippingDuration   $shippingDuration
     * @param DeliveryDate|null  $deliveryDate
     */
    public function __construct(
        Context $context,
        EncoderInterface $encoder,
        OrderRepository $orderRepository,
        Session $checkoutSession,
        QuoteToRateRequest $quoteToRateRequest,
        ShippingDuration $shippingDuration,
        DeliveryDate $deliveryDate = null
    ) {
        $this->encoder            = $encoder;
        $this->orderRepository    = $orderRepository;
        $this->checkoutSession    = $checkoutSession;
        $this->deliveryEndpoint   = $deliveryDate;
        $this->quoteToRateRequest = $quoteToRateRequest;
        $this->shippingDuration   = $shippingDuration;

        parent::__construct($context);
    }

    /**
     * Create json response
     *
     * @param string $data
     * @param int    $code
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    //@codingStandardsIgnoreLine
    protected function jsonResponse($data = '', $code = null)
    {
        $response = $this->getResponse();

        if ($code !== null) {
            $response->setStatusCode($code);
        }

        return $response->representJson(
            $this->encoder->encode($data)
        );
    }

    /**
     * @param $quoteId
     *
     * @return \TIG\PostNL\Model\Order
     */
    //@codingStandardsIgnoreLine
    protected function getPostNLOrderByQuoteId($quoteId)
    {
        /** @var \TIG\PostNL\Model\Order $postnlOrder */
        $postnlOrder = $this->orderRepository->getByQuoteId($quoteId);
        if (!$postnlOrder) {
            return $this->orderRepository->create();
        }

        if ($postnlOrder->getOrderId()) {
            // double quote, order probably canceled before. so add new record.
            return $this->orderRepository->create();
        }

        return $postnlOrder;
    }

    /**
     * CIF call to get the delivery day needed for the StartDate param in TimeFrames Call.
     * @param array $address
     *
     * @return array
     */
    //@codingStandardsIgnoreLine
    protected function getDeliveryDay($address)
    {
        $quote   = $this->checkoutSession->getQuote();
        $storeId = $quote->getStoreId();
        $shippingDuration = $this->shippingDuration->get();
        $this->deliveryEndpoint->updateApiKey($storeId);
        $this->deliveryEndpoint->updateParameters($address, $shippingDuration);
        $response = $this->deliveryEndpoint->call();

        if (!is_object($response) || !isset($response->DeliveryDate)) {
            return __('Invalid GetDeliveryDate response: %1', var_export($response, true));
        }

        $this->checkoutSession->setPostNLDeliveryDate($response->DeliveryDate);
        return $response->DeliveryDate;
    }

    /**
     * @return RateRequest
     */
    // @codingStandardsIgnoreLine
    protected function getRateRequest()
    {
        $request = $this->getRequest();
        $address = $request->getParam('address');

        /** @var RateRequest $request */
        $request = $this->quoteToRateRequest->get();
        $request->setDestCountryId($address['country']);
        $request->setDestPostcode($address['postcode']);

        $shippingAddress = $request->getShippingAddress();
        $shippingAddress->setCountryId($address['country']);
        $shippingAddress->setPostcode($address['postcode']);
        $request->setShippingAddress($shippingAddress);

        return $request;
    }
}
