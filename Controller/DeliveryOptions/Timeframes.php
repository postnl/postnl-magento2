<?php

namespace TIG\PostNL\Controller\DeliveryOptions;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Json\EncoderInterface;
use TIG\PostNL\Controller\AbstractDeliveryOptions;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Service\Carrier\Price\Calculator;
use TIG\PostNL\Service\Carrier\QuoteToRateRequest;
use TIG\PostNL\Service\Quote\ShippingDuration;
use TIG\PostNL\Service\Timeframe\Resolver;

class Timeframes extends AbstractDeliveryOptions
{
    private Calculator $calculator;
    private Resolver $timeframeResolver;

    public function __construct(
        Context $context,
        EncoderInterface $encoder,
        OrderRepository $orderRepository,
        Session $checkoutSession,
        QuoteToRateRequest $quoteToRateRequest,
        ShippingDuration $shippingDuration,
        Calculator $calculator,
        Resolver $timeframeResolver
    ) {
        $this->calculator = $calculator;
        $this->timeframeResolver = $timeframeResolver;

        parent::__construct(
            $context,
            $encoder,
            $orderRepository,
            $checkoutSession,
            $quoteToRateRequest,
            $shippingDuration,
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
            return $this->jsonResponse($this->timeframeResolver->getFallBackResponse(1));
        }

        $price = $this->calculator->getPriceWithTax($this->getRateRequest());

        if (!isset($price['price'])) {
            return false;
        }

        $result = $this->timeframeResolver->processTimeframes($params['address']);
        $result['price'] = $price['price'];
        return $this->jsonResponse($result);
    }
}
