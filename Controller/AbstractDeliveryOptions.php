<?php

namespace TIG\PostNL\Controller;

use Magento\Framework\Json\EncoderInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Checkout\Model\Session;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Service\Carrier\QuoteToRateRequest;
use TIG\PostNL\Service\Quote\ShippingDuration;
use TIG\PostNL\Webservices\Endpoints\DeliveryDate;

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
     * @var ShippingDuration
     */
    //@codingStandardsIgnoreLine
    protected $shippingDuration;

    /**
     * @var QuoteToRateRequest
     */
    private $quoteToRateRequest;

    /**
     * @param Context            $context
     * @param EncoderInterface   $encoder
     * @param OrderRepository    $orderRepository
     * @param Session            $checkoutSession
     * @param QuoteToRateRequest $quoteToRateRequest
     * @param ShippingDuration   $shippingDuration
     */
    public function __construct(
        Context $context,
        EncoderInterface $encoder,
        OrderRepository $orderRepository,
        Session $checkoutSession,
        QuoteToRateRequest $quoteToRateRequest,
        ShippingDuration $shippingDuration,
    ) {
        $this->encoder            = $encoder;
        $this->orderRepository    = $orderRepository;
        $this->checkoutSession    = $checkoutSession;
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
     * @return RateRequest
     */
    // @codingStandardsIgnoreLine
    protected function getRateRequest()
    {
        $request = $this->getRequest();
        $address = $request->getParam('address');

        /** @var RateRequest $request */
        return $this->quoteToRateRequest->getByUpdatedAddress(
            $address['country'] ?? '',
            $address['postcode'] ?? ''
        );
    }
}
