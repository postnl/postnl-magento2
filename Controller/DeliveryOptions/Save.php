<?php

namespace TIG\PostNL\Controller\DeliveryOptions;

use Magento\Checkout\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\EncoderInterface;
use TIG\PostNL\Controller\AbstractDeliveryOptions;
use TIG\PostNL\Exception;
use TIG\PostNL\Model\OrderRepository;
use TIG\PostNL\Service\Action\OrderSave;
use TIG\PostNL\Service\Carrier\QuoteToRateRequest;
use TIG\PostNL\Service\Quote\ShippingDuration;

class Save extends AbstractDeliveryOptions
{
    private OrderSave $orderSave;

    public function __construct(
        Context $context,
        EncoderInterface $encoder,
        OrderRepository $orderRepository,
        Session $checkoutSession,
        QuoteToRateRequest $quoteToRateRequest,
        ShippingDuration $shippingDuration,
        OrderSave $orderSave
    ) {
        parent::__construct(
            $context,
            $encoder,
            $orderRepository,
            $checkoutSession,
            $quoteToRateRequest,
            $shippingDuration,
        );

        $this->orderSave = $orderSave;
    }

    /**
     * @return ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if (!isset($params['type'])) {
            return $this->jsonResponse(__('No Type specified'));
        }

        try {
            $quoteId = $this->checkoutSession->getQuoteId();
            $postnlOrder = $this->getPostNLOrderByQuoteId($quoteId);
            $this->orderSave->saveDeliveryOption($postnlOrder, $params);
            return $this->jsonResponse('ok');
        } catch (LocalizedException $exception) {
            return $this->jsonResponse($exception->getMessage(), Http::STATUS_CODE_503);
        } catch (\Exception $exception) {
            return $this->jsonResponse($exception->getMessage(), Http::STATUS_CODE_503);
        }
    }
}
